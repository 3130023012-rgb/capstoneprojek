<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KasTransaction; 
use App\Models\Activity; 
use App\Models\Member; 
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class KasController extends Controller
{
    /**
     * Menampilkan status kas dan STATUS PEMBAYARAN ANGGOTA.
     * Dipanggil oleh route kas.index.
     */
    public function index(Request $request) 
    {
        // 1. Logika Filter Bulan
        $filterMonth = $request->input('filter_month', Carbon::now()->format('Y-m'));
        
        $bulanFilter = Carbon::createFromFormat('Y-m', $filterMonth)->startOfMonth();
        $bulanFilterEnd = $bulanFilter->copy()->endOfMonth();
        
        // Logika Perhitungan Saldo Kas NYATA (Global)
        $totalIn = KasTransaction::where('type', 'in')->sum('amount');
        $totalOut = KasTransaction::where('type', 'out')->sum('amount');
        $totalKas = $totalIn; 
        $saldo = $totalIn - $totalOut; 

        // 2. LOGIKA UTAMA MODUL KAS: Data Status Pembayaran Anggota
        $members = Member::all(); 
        
        $memberStatus = $members->map(function ($member) use ($bulanFilter, $bulanFilterEnd) {
            
            // Mencari pembayaran berdasarkan member_id di bulan filter.
            $lastPayment = KasTransaction::where('type', 'in')
                ->where('member_id', $member->id) 
                ->whereBetween('date', [$bulanFilter->toDateString(), $bulanFilterEnd->toDateString()])
                ->latest('date')
                ->first();

            return (object)[ 
                'id' => $member->id, 
                'name' => $member->name,
                'status' => $lastPayment ? 'Sudah Bayar' : 'Belum Bayar',
                'last_payment_date' => $lastPayment ? $lastPayment->date : null,
                'last_payment_id' => $lastPayment ? $lastPayment->id : null,
                'user_id' => $member->user_id,
            ];
        });

        return view('kas.index', compact('saldo', 'totalKas', 'memberStatus', 'filterMonth', 'bulanFilter'));
    }

    /**
     * Menampilkan form untuk menambah transaksi IURAN ANGGOTA.
     * (Method create() sudah benar)
     */
    public function create(Request $request)
    {
        $activities = Activity::select('id', 'material', 'date') 
                              ->orderBy('date', 'desc')
                              ->get();
                              
        $memberIdFromQuery = $request->query('member_id'); 
        
        $prefillData = [
            'member_id' => $memberIdFromQuery,
            'user_id' => null, 
            'type' => $request->query('type', null), 
            'description' => $request->query('desc', null), 
            'current_month' => Carbon::now()->translatedFormat('F Y'),
            'default_month_year' => $request->query('month_year', Carbon::now()->format('Y-m')),
        ];

        $memberName = 'Data Anggota Tidak Ditemukan';
        if ($memberIdFromQuery) {
            $member = Member::find($memberIdFromQuery);
            
            if ($member) {
                $memberName = $member->name;
                $prefillData['user_id'] = $member->user_id; 
                
                if (is_null($prefillData['description'])) {
                    $monthDisplay = Carbon::createFromFormat('Y-m', $prefillData['default_month_year'])->translatedFormat('F Y');
                    $prefillData['description'] = "Iuran Anggota {$memberName} Bulan {$monthDisplay}";
                }
            }
        }
        
        return view('kas.create', compact('activities', 'prefillData', 'memberName')); 
    }

    /**
     * Menyimpan transaksi kas baru ke database.
     * (Method store() sudah benar)
     */
    public function store(Request $request)
    {
        // 1. Validasi Input Dasar
        $rules = [
            'type' => ['required', Rule::in(['in', 'out'])], 
            'amount' => 'required|integer|min:1000',
            'description' => 'required|string|max:255', 
            'activity_id' => 'nullable|exists:activities,id', 
            
            // Field untuk Kas Umum/Iuran
            'date' => 'nullable|date',             
            'iuran_month_year' => 'nullable|date_format:Y-m', 
            'member_id' => 'nullable|exists:members,id',     
        ];
        
        // 2. Validasi Kondisional
        $isIuran = $request->filled('member_id') && $request->filled('iuran_month_year');

        if ($isIuran) {
            $rules['iuran_month_year'] = 'required|date_format:Y-m';
            $rules['member_id'] = 'required|exists:members,id';
            $rules['type'] = ['required', Rule::in(['in'])]; // Iuran harus pemasukan
        } else {
            $rules['date'] = 'required|date'; // Kas Umum wajib ada tanggal
        }

        $validated = $request->validate($rules);
        
        // 3. Logika Penentuan Tanggal dan User ID
        
        if ($isIuran) {
            $transactionDate = Carbon::createFromFormat('Y-m', $validated['iuran_month_year'])->startOfMonth()->toDateString();
            
            $member = Member::find($validated['member_id']);
            $userIdToStore = ($member && is_null($member->user_id)) ? Auth::id() : $member->user_id;
            
            $dataToCreate = $request->only(['type', 'amount', 'description', 'activity_id', 'member_id']);
            $dataToCreate['member_id'] = $validated['member_id'];
            $redirectFilter = $validated['iuran_month_year'];

        } else {
            $transactionDate = $validated['date'];
            $userIdToStore = Auth::id(); 
            
            $dataToCreate = $request->only(['type', 'amount', 'description', 'activity_id']);
            $dataToCreate['member_id'] = null;
            $redirectFilter = Carbon::parse($transactionDate)->format('Y-m');
        }

        // Finalisasi data wajib
        $dataToCreate['date'] = $transactionDate;
        $dataToCreate['user_id'] = $userIdToStore;

        // 4. Simpan ke Database
        KasTransaction::create($dataToCreate);

        // 5. REDIRECT KE INDEX DENGAN FILTER BULAN YANG BARU DISIMPAN
        return redirect()->route('kas.index', [
            'filter_month' => $redirectFilter
        ])->with('success', 'Transaksi Kas berhasil ditambahkan.');
    }
    
    /**
     * Menampilkan form untuk mengedit transaksi IURAN ANGGOTA.
     * NOTE: Logika untuk Kas Umum/Kegiatan (else block) telah dipindahkan 
     * ke GeneralKasController. Ini hanya melayani EDIT IURAN ANGGOTA.
     */
    public function edit(KasTransaction $kas)
    {
        // Hanya melayani EDIT IURAN ANGGOTA
        if ($kas->member_id === null) {
            // Jika ada yang mencoba mengedit Kas Umum melalui route kas.edit, 
            // kita redirect ke halaman Kas Utama.
            return redirect()->route('kas.index')->with('error', 'Transaksi Umum harus diedit melalui halaman Kegiatan.');
        }

        $activities = Activity::select('id', 'material', 'date')->get(); 
        $member = Member::find($kas->member_id);
        $memberName = $member->name ?? 'Anggota Tidak Dikenal';

        // Merender form edit Iuran Anggota (yang seharusnya hanya menampilkan tombol Batal/Detail)
        return view('kas.edit', compact('kas', 'activities', 'memberName')); 
    }
    
    /**
     * Memperbarui transaksi kas IURAN ANGGOTA.
     * NOTE: Karena form edit Iuran Anggota di kas.edit sekarang hanya menampilkan 
     * tombol Batal/Detail (tidak ada tombol Submit), method ini pada dasarnya 
     * tidak akan ter-trigger dari form tersebut. Namun, kita tetap membiarkannya 
     * di sini dan memastikan ia redirect ke kas.index.
     */
    public function update(Request $request, KasTransaction $kas)
    {
        // KARENA FORM EDIT IURAN (kas.edit) DIHILANGKAN FUNGSI UPDATENYA, 
        // METHOD INI DIASUMSIKAN TIDAK DIPANGGIL.
        // Jika dipanggil, lakukan validasi minimum dan redirect ke kas.index
        
        $validated = $request->validate([
            'date' => 'required|date',
            'amount' => 'required|integer|min:1000',
            // ... (validasi lainnya jika diperlukan)
        ]);

        $kas->update($validated);
        
        // ✅ Redirect ke index Kas Utama
        return redirect()->route('kas.index')->with('success', 'Transaksi Kas Iuran berhasil diperbarui.');
    }
    
    /**
     * Menghapus transaksi kas IURAN ANGGOTA.
     * NOTE: Method ini dipanggil oleh tombol 'Hapus Transaksi' di kas.index.
     */
    public function destroy(KasTransaction $kas)
    {
        $kas->delete();
        
        // ✅ Redirect ke index Kas Utama
        return redirect()->route('kas.index')->with('success', 'Transaksi Kas berhasil dihapus.');
    }
    
    /**
     * Menampilkan laporan rekapitulasi kas bulanan.
     */
    public function report(Request $request)
    {
        // ... (Logika Report Kas tidak diubah) ...
        $filterDate = $request->input('filter_month', Carbon::now()->format('Y-m'));
        $titleDate = Carbon::createFromFormat('Y-m', $filterDate)->startOfMonth();
        $nextMonth = $titleDate->copy()->addMonth()->format('Y-m');
        $prevMonth = $titleDate->copy()->subMonth()->format('Y-m');
        
        $startOfMonth = $titleDate->startOfMonth()->toDateString();
        $endOfMonth = $titleDate->endOfMonth()->toDateString();
        
        $transactions = KasTransaction::whereBetween('date', [$startOfMonth, $endOfMonth])
            ->orderBy('date', 'asc')
            ->get();
        
        $totalIncome = $transactions->where('type', 'in')->sum('amount');
        $totalExpense = $transactions->where('type', 'out')->sum('amount');
        $saldoCurrentMonth = $totalIncome - $totalExpense;
        
        $saldoStart = KasTransaction::where('date', '<', $startOfMonth)->sum(DB::raw("CASE WHEN type = 'in' THEN amount ELSE -amount END"));

        return view('laporan.kas', compact('transactions', 'totalIncome', 'totalExpense', 'saldoCurrentMonth', 'saldoStart', 'titleDate', 'filterDate', 'nextMonth', 'prevMonth'));
    }
}