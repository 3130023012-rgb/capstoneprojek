<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <div class="panel panel-success">
                <div class="panel-heading">
                    <h3 class="panel-title"><span class="fa fa-plus"></span> Tambah User</h3>
                </div>
                <div class="panel-body">
                    <form method="post" action="">
                        <div class="form-group">
                            <label>Nama</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="paswd" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Keterangan</label>
                            <input type="text" name="ket" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Level</label>
                            <select name="level" class="form-control" required>
                                <option value="">-- Pilih Level --</option>
                                <option value="1">Admin</option>
                                <option value="2">Anggota</option>
                                <option value="3">Kepala</option>
                            </select>
                        </div>
                        <input type="submit" name="simpan" value="Simpan" class="btn btn-success">
                        <a href="?page=user&actions=tampil" class="btn btn-danger">Batal</a>
                    </form>
                    <?php
                    if (isset($_POST['simpan'])) {
                        $nama = $_POST['nama'];
                        $username = $_POST['username'];
                        $paswd = md5($_POST['paswd']);
                        $email = $_POST['email'];
                        $ket = $_POST['ket'];
                        $level = $_POST['level'];

                        $sql = "INSERT INTO user VALUES ('','$username','$paswd','$nama','$email','$ket','$level')";
                        $query = mysqli_query($koneksi, $sql) or die("SQL Simpan User Error");
                        if ($query) {
                            echo "<script>window.location.assign('?page=user&actions=tampil');</script>";
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
