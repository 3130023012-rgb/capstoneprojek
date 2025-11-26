<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Anggota Manual</title>
    <style>
        :root { --color-primary: #007bff; --color-success: #28a745; --color-danger: #dc3545; --color-white: #fff; }
        .form-container { max-width: 500px; margin: 50px auto; padding: 30px; border: 1px solid #ccc; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); background-color: var(--color-white); }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .btn-submit { background-color: var(--color-success); color: var(--color-white); padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        .btn-submit:hover { opacity: 0.9; }
        .error { color: var(--color-danger); font-size: 0.9em; }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Tambah Data Anggota Baru</h2>
    
    <form action="{{ route('member.store') }}" method="POST">
        @csrf
        
        @if ($errors->any())
            <div class="error" style="border: 1px solid var(--color-danger); padding: 10px; margin-bottom: 15px;">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="form-group">
            <label for="name">Nama Lengkap Anggota:</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required>
            @error('name')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="member_id">NIM:</label>
            <input type="text" name="member_id" id="member_id" value="{{ old('member_id') }}">
            @error('member_id')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn-submit">Simpan Anggota</button>
        <a href="{{ route('absensi.create') }}" style="margin-left: 15px; color: var(--color-primary); text-decoration: none;">Kembali ke Input Kehadiran</a>
    </form>
</div>

</body>
</html>