<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\Trainer;
use App\Models\KasTransaction; 
use App\Models\Member; 
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
    /**
     * Menampilkan daftar transaksi Kas yang terkait dengan Kegiatan/Umum, dan saldo kas global.
     * Route: kegiatan.index
     */
    public function index()
    {
        // 1. Saldo Global
        $globalIncome = KasTransaction::where('type', 'in')->sum('amount');
        $globalExpense = KasTransaction::where('type', 'out')->sum('amount');
        
        $totalKas = $globalIncome; 
        $saldo = $globalIncome - $globalExpense; 

        // 2. Mengambil daftar transaksi Kas untuk ditampilkan di tabel
        $transactions = KasTransaction::with('activity') 
            ->orderBy('date', 'desc')
            ->paginate(15); 
        
        return view('kegiatan.index', compact('transactions', 'totalKas', 'saldo'));
    }

    public function edit(Activity $activity) 
    { 
        $trainers = Trainer::orderBy('name')->get();
        return view('kegiatan.edit', compact('activity', 'trainers')); 
    }
    
    public function update(Request $request, Activity $activity) 
    { 
        $validated = $request->validate([
            'material'    => 'required|string|max:255',
            'date'        => 'required|date',
            'description' => 'nullable|string',
            'trainer_id'  => 'nullable|exists:trainers,id',
        ]);

        $activity->update($validated);
        return redirect()->route('kegiatan.index')->with('success', 'Kegiatan berhasil diperbarui.');
    }
    
    /**
     * Menghapus Kegiatan dan SEMUA transaksi kas terkait (Cascading Delete Manual).
     */
    public function destroy(Activity $activity) 
    { 
        DB::beginTransaction();
        try {
            // Hapus semua transaksi kas yang terhubung dengan activity_id ini
            KasTransaction::where('activity_id', $activity->id)->delete(); 
            
            // Hapus kegiatan
            $activity->delete();
            
            DB::commit();
            return redirect()->route('kegiatan.index')->with('success', 'Kegiatan dan semua transaksi terkait berhasil dihapus.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus kegiatan: ' . $e->getMessage());
        }
    }
    
    public function updateKonfirmasi(Request $request, Activity $activity)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'approved', 'rejected'])]
        ]);
        
        DB::beginTransaction();
        try {
            $activity->confirmation_status = $validated['status'];
            $activity->save();

            // Cari transaksi pengeluaran yang sudah ada untuk kegiatan ini
            $existingKas = KasTransaction::where('activity_id', $activity->id)
                                         ->where('type', 'out') 
                                         ->first();
                                         
            if ($activity->confirmation_status === 'approved') {
                if ($activity->nominal > 0) {
                    $dataKas = [
                        'date'        => $activity->date, 
                        'amount'      => $activity->nominal, 
                        'description' => "Pengeluaran kegiatan: {$activity->material}", 
                        'user_id'     => Auth::id(), 
                        'activity_id' => $activity->id,
                        'type'        => 'out', 
                    ];
                    
                    if ($existingKas) {
                        $existingKas->update($dataKas);
                    } else {
                        KasTransaction::create($dataKas);
                    }
                }
            } else {
                // Jika status diubah ke rejected atau pending, hapus catatan di kas
                if ($existingKas) {
                    $existingKas->delete();
                }
            }
            
            DB::commit();
            return response()->json(['message' => 'Status konfirmasi berhasil diperbarui.']);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal memperbarui status: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Menampilkan laporan rekapitulasi kegiatan bulanan.
     */
    public function report(Request $request)
{
    $filterDate = $request->input('filter_month', Carbon::now()->format('Y-m'));
    $titleDate = Carbon::createFromFormat('Y-m', $filterDate);
    $startDate = $titleDate->copy()->startOfMonth()->toDateString();
    $endDate = $titleDate->copy()->endOfMonth()->toDateString();

    // AMBIL DATA BERDASARKAN TRANSAKSI KAS (Source of Truth)
    // Kita mencari semua transaksi 'out' yang memiliki activity_id pada bulan terpilih
    $activities = Activity::with(['trainer'])
        ->whereBetween('date', [$startDate, $endDate])
        ->orderBy('date', 'asc')
        ->get()
        ->map(function ($activity) {
            
            // AMBIL NOMINAL LANGSUNG DARI ID DI TABEL KAS_TRANSACTIONS
            // Mencari nominal berdasarkan activity_id dan tipe 'out'
            $transaction = KasTransaction::where('activity_id', $activity->id)
                ->where('type', 'out')
                ->first();

            // Masukkan amount ke dalam properti nominal_kas
            // Jika data di database amount-nya 0 atau null, akan tetap 0
            $activity->nominal_kas = $transaction ? $transaction->amount : 0;
            
            return $activity;
        });

    return view('laporan.kegiatan', compact('titleDate', 'activities', 'filterDate'));
}
}