<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kehadiran - UKM PAGARNUSA</title>
    
    <style>
        /* Menggunakan style dasar dari absensi.blade.php */
        :root {
            --color-primary: #007bff;
            --color-success: #28a745;
            --color-danger: #dc3545;
            --color-dark: #343a40;
            --color-light: #f8f9fa;
            --color-white: #fff;
        }
        body { font-family: sans-serif; margin: 0; background-color: var(--color-light); }
        .form-container { 
            max-width: 800px; margin: 50px auto; 
            background-color: var(--color-white); 
            padding: 30px; border-radius: 8px; 
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; color: var(--color-dark); }
        .form-group input:not([type="radio"]), .form-group select {
            width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;
        }
        .form-group table { width: 100%; border-collapse: collapse; }
        .form-group th, .form-group td { padding: 10px; border: 1px solid #eee; text-align: left; }
        .btn-submit { background-color: var(--color-success); color: var(--color-white); padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        .btn-submit:hover { background-color: #218838; }
        .alert-error { color: var(--color-danger); margin-bottom: 15px; }
        
        /* Gaya untuk tombol tambah anggota di dalam form */
        .btn-tambah-anggota {
            color: var(--color-primary); 
            text-decoration: none; 
            font-weight: bold;
            border: 1px solid #ccc;
            padding: 8px 15px;
            border-radius: 4px;
            background-color: var(--color-light);
            display: inline-block;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Tambah Data Kehadiran Kegiatan Baru</h2>

    @if (session('success'))
        <div style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif
    
    @if ($errors->any())
        <div class="alert-error" style="border: 1px solid var(--color-danger); padding: 10px; border-radius: 5px;">
            <p style="font-weight: bold;">Terdapat {{ $errors->count() }} kesalahan:</p>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('absensi.store') }}" method="POST">
        @csrf

        <h3>Detail Kegiatan</h3>
        <div class="form-group">
            <label for="date">Tanggal Kegiatan:</label>
            <input type="date" name="date" id="date" value="{{ old('date', \Carbon\Carbon::now()->format('Y-m-d')) }}" required>
        </div>
        <div class="form-group">
            <label for="material">Materi / Topik:</label>
            <input type="text" name="material" id="material" value="{{ old('material') }}" required>
        </div>
        <div class="form-group">
            <label for="trainer_name">Nama Pelatih:</label>
            <input type="text" name="trainer_name" id="trainer_name" value="{{ old('trainer_name') }}" required>
        </div>
        
        <hr style="margin: 30px 0;">

        <h3>Status Kehadiran Anggota</h3>
        
        <div style="margin-bottom: 15px; text-align: right;">
            <a href="{{ route('member.create') }}" class="btn-tambah-anggota">
                + Tambah Anggota Baru Manual
            </a>
        </div>
        
        <div class="form-group">
            <table>
                <thead>
                    <tr>
                        <th>Anggota</th>
                        <th>NIM</th>
                        <th>Hadir</th>
                        <th>Absen</th>
                        <th>Sakit</th>
                        <th>Izin</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($members as $member)
                        <tr>
                            <td>{{ $member->name }}</td>
                            <td>{{ $member->member_id ?? '-' }}</td> <td><input type="radio" name="member_statuses[{{ $member->id }}]" value="present" required {{ old("member_statuses.{$member->id}") == 'present' ? 'checked' : '' }}></td>
                            <td><input type="radio" name="member_statuses[{{ $member->id }}]" value="absent" required {{ old("member_statuses.{$member->id}") == 'absent' ? 'checked' : '' }}></td>
                            <td><input type="radio" name="member_statuses[{{ $member->id }}]" value="sick_leave" required {{ old("member_statuses.{$member->id}") == 'sick_leave' ? 'checked' : '' }}></td>
                            <td><input type="radio" name="member_statuses[{{ $member->id }}]" value="permission" required {{ old("member_statuses.{$member->id}") == 'permission' ? 'checked' : '' }}></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center;">
                                Belum ada data anggota. 
                                <a href="{{ route('member.create' ) }}" style="color: var(--color-primary); text-decoration: none; font-weight: bold;">
                                    Klik di sini untuk Tambah Anggota Manual.
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <button type="submit" class="btn-submit" {{ $members->isEmpty() ? 'disabled' : '' }}>Simpan Kehadiran</button>
        <a href="{{ route('absensi') }}" style="margin-left: 15px; color: var(--color-primary); text-decoration: none;">Batal</a>
    </form>
</div>

</body>
</html>