<?php
require_once('koneksi/koneksi.php');
function getPaket()
{
	global $koneksi;
	$sql = "SELECT * FROM tbl_paket";
	$data = mysqli_query($koneksi, $sql);
	$value = mysqli_fetch_all($data, MYSQLI_ASSOC);

	$response = array();
	if ($data) {
		$response['status'] = true;
		$response['message'] = "Data berhasil di dapatkan";
		$response['result'] = $value;
	} else {
		$response['status'] = false;
		$response['message'] = "Data gagal di dapatkan";
		$response['result'] = $value;
	}
	mysqli_close($koneksi);
	echo json_encode($response);
}

function upload($img)
{
	global $koneksi;
	$target = "img/";
	$response = array();

	//memberikan izin file upload
	$allowedFormats = ['image/jpeg', 'image/png'];
	//tentukan ukuran max file
	$maxFileSize = 1024 * 1024; // 1 MB
	// Mendapatkan timestamp saat ini
	$timestamp = time();

	$sumber = $img['tmp_name'];
	//mendapatkan ukuran image
	$imageSize = $img['size'];
	//mendapatkan ext image
	$ext = explode('/', $img['type']);
	//meberikan nama baru pada image yang ingin di upload
	$nama_gambar = 'permohonan_' . $timestamp . '.' . $ext[1];

	//logika upload
	if ($imageSize <= $maxFileSize) {
		if (in_array($img['type'], $allowedFormats)) {
			if (move_uploaded_file($sumber, $target . $nama_gambar)) {

				$response['status'] = true;
				$response['message'] = 'Berhasil upload data';
				$response['nama_file'] = $nama_gambar;
			} else {

				$response['status'] = false;
				$response['message'] = 'Gagal upload data';
				$response['nama_file'] = $nama_gambar;
			}
		} else {
			$response['status'] = false;
			$response['message'] = 'Gambar yang di upload harus berupa png atau jpeg';
			$response['nama_file'] = $nama_gambar;
		}
	} else {
		$response['status'] = false;
		$response['message'] = 'File yang di upload harus kurang dari 1MB';
		$response['nama_file'] = $nama_gambar;
	}
	// Pindahkan gambar ke direktori tujuan
	mysqli_close($koneksi);
	echo json_encode($response);
}

function getPermohonan()
{

	$response = array();

	global $koneksi;

	$id_user = $_GET['id_user'];

	$sql = "SELECT * FROM tbl_pelanggan where id_user = '$id_user'";
	//var_dump($sql);die;

	$data = mysqli_query($koneksi, $sql);
	$value = mysqli_fetch_all($data, MYSQLI_ASSOC);

	$response = array();

	if ($value) {
		$response['status'] = true;
		$response['message'] = "Data berhasil didapatkan";
		$response['result'] = $value;
	} else {
		$response['status'] = false;
		$response['message'] = "Data gagal didapatkan";
		$response['result'] = $value;
	}
	mysqli_close($koneksi);
	echo json_encode($response);
}

function insertPermohonan()
{
	$id_user = $_POST['id_user'] ? $_POST['id_user'] : 1;
	$nik = $_POST['nik'] ? $_POST['nik'] : '';
	$nm_user = $_POST['nm_user'] ? $_POST['nm_user'] : '';
	$tgl_lahir = $_POST['tgl_lahir'] ? $_POST['tgl_lahir'] : date('Y-m-d');
	$tmp_lahir = $_POST['tmp_lahir'] ? $_POST['tmp_lahir'] : '';
	$telpon = $_POST['telpon'] ? $_POST['telpon'] : '';
	$alamat = $_POST['alamat'] ? $_POST['alamat'] : '';
	$img = $_POST['img'] ? $_POST['img'] : '';
	$created_at = date('Y-m-d H:i:s', time());
	$id_paket = $_POST['id_paket'] ? $_POST['id_paket'] : 1;
	$gender = $_POST['gender'] ? $_POST['gender'] : '';
	$id_alamat = $_POST['id_alamat'] ? $_POST['id_alamat'] : 2;
	$langganan = $_POST['langganan'] ? $_POST['langganan'] : 0;

	$response = array();

	global $koneksi;
	// "INSERT INTO `tbl_pelanggan` (`id_pelanggan`, `id_user`, `nik_user`, `nm_user`, `tanggal_lahir`, `tempat_lahir`, `telpon`, `alamat`, `img_identitas`, `created_at`, `updated_at`, `id_paket`, `gender`, `status`, `id_alamat`) VALUE(null,'$id_user','$nik','$nm_lengkap','$tgl_lahir','$tmp_lahir','$telpon','$alamat','$img','$created_at',null,'$id_paket','$gender','0','$id_alamat')";
	$sql = $langganan == 0 ? "INSERT INTO tbl_pelanggan VALUE(null,'$id_user','$nik','$nm_user','$tgl_lahir','$tmp_lahir','$telpon','$alamat','$img','$created_at',null,'$id_paket','$gender','0','$id_alamat')" : "INSERT INTO tbl_pelanggan VALUE(null,'$id_user','$nik','$nm_user','$tgl_lahir','$tmp_lahir','$telpon','$alamat','$img','$created_at',null,'$id_paket','$gender','4','$id_alamat')";

	if ($koneksi->query($sql)) {
		$response['status'] = true;
		$response['message'] = "Berhasil menambahkan data";
	} else {
		$response['status'] = false;
		$response['message'] = $koneksi->error;
	}
	mysqli_close($koneksi);
	echo json_encode($response);
}

function postAlamat()
{
	$id_user = $_POST['id_user'] ? $_POST['id_user'] : 1;
	$latitude = $_POST['latitude'] ? $_POST['latitude'] : '';
	$longitude = $_POST['longitude'] ? $_POST['longitude'] : '';
	$alamat = $_POST['alamat'] ? $_POST['alamat'] : '';
	$url_alamat = $_POST['url_alamat'] ? $_POST['url_alamat'] : '';

	$response = array();

	global $koneksi;
	$sql = "INSERT INTO tbl_alamat VALUE(null,'$id_user','$alamat','$latitude','$longitude','$url_alamat')";

	if ($koneksi->query($sql)) {
		$response['status'] = true;
		$response['message'] = "Berhasil menambahkan data";
		$response['id_alamat'] = mysqli_insert_id($koneksi);
	} else {
		$response['status'] = false;
		$response['message'] = $koneksi->error;
		$response['id_alamat'] = mysqli_insert_id($koneksi);
	}
	mysqli_close($koneksi);
	echo json_encode($response);
}

function getAlamat()
{
	$id_user = $_GET['id_user'];

	global $koneksi;
	$sql = $id_user != null ? "SELECT * FROM tbl_alamat where id_user = '$id_iuser'" : "SELECT * FROM tbl_alamat";
	$data = mysqli_query($koneksi, $sql);
	$value = mysqli_fetch_all($data, MYSQLI_ASSOC);

	$response = array();

	if ($value) {
		$response['status'] = true;
		$response['message'] = "Data berhasil didapatkan";
		$response['result'] = $value;
	} else {
		$response['status'] = false;
		$response['message'] = "Data gagal didapatkan";
		$response['result'] = $value;
	}
	mysqli_close($koneksi);
	echo json_encode($response);
}

function postToken()
{
	$id_user = $_POST['id_user'];
	$token = $_POST['token'];

	global $koneksi;

	$sql = "UPDATE tbl_user SET token_notification = '$token' WHERE id_user = '$id_user'";
	$cekData = "SELECT * from tbl_user where id_user = '$id_user'";
	$data = mysqli_query($koneksi, $cekData);
	$value = mysqli_fetch_assoc($data);
	$response = array();

	// var_dump($valu['token_notification']);die;
	if ($value['token_notification'] != null || $value['token_notification'] != '') {
		$response['status'] = true;
		$response['message'] = "Berhasil mendapatkan token";
	} else {
		if ($koneksi->query($sql)) {
			$response['status'] = true;
			$response['message'] = "Token telah dibuat";
		} else {
			$response['status'] = false;
			$response['message'] = "Token gagal dikirim";
		}
	}
	mysqli_close($koneksi);
	echo json_encode($response);
}

function getToken()
{
	$id_user = $_GET['id_user'];

	global $koneksi;
	$sql = "SELECT * FROM tbl_user WHERE id_user= '$id_user'";
	$data = mysqli_query($koneksi, $sql);
	$value = mysqli_fetch_all($data, MYSQL_ASSOC);

	$response = array();

	if ($value) {
		$response['status'] = true;
		$response['message'] = "Data berhasil didapatkan";
	} else {
		$response['status'] = false;
		$response['message'] = "Data gagal didapatkan";
	}
	mysqli_close($koneksi);
	echo json_encode($response);
}

function updatePesanTerbaca()
{
	global $koneksi;
	$response = array();

	$requestPayload = file_get_contents('php://input');
	$data = json_decode($requestPayload, true);

	$id_notif = $data['id_notif'];

	$sql = "UPDATE tbl_notif set is_read = 1 where id_notif = '$id_notif'";

	if ($koneksi->query($sql)) {
		$response['status'] = true;
		$response['message'] = "Berhasil mengupdate data";
	} else {
		$response['status'] = false;
		$response['message'] = $koneksi->error;
	}
	mysqli_close($koneksi);
	echo json_encode($response);
}

function getNotif()
{

	global $koneksi;

	$response = array();

	$id_user = $_GET['id_user'];

	$sql = "SELECT * FROM tbl_notif WHERE id_user = '$id_user'";

	$data = mysqli_query($koneksi, $sql);
	$value = mysqli_fetch_all($data, MYSQLI_ASSOC);

	if ($value) {
		$response['status'] = true;
		$response['message'] = "Data berhasil didapatkan";
		$response['result'] = $value;
	} else {
		$response['status'] = false;
		$response['message'] = "Data gagal didapatkan";
		$response['result'] = $value;
	}
	mysqli_close($koneksi);
	echo json_encode($response);
}

function getPembayaran()
{
	global $koneksi;

	$response = array();

	$id_pelanggan = $_GET['id_pelanggan'];

	$sql = "SELECT invoice,pembayaran,tp.created_at,nm_paket,jns_pembayaran,nm_user,telpon FROM tbl_pembayaran tp INNER JOIN tbl_paket tpaket on tp.id_paket = tpaket.id_paket inner join tbl_pelanggan tpeh on tp.id_pelanggan = tpeh.id_pelanggan 
		where tp.id_pelanggan = '$id_pelanggan'";


	$data = mysqli_query($koneksi, $sql);
	$value = mysqli_fetch_all($data, MYSQLI_ASSOC);

	if ($value) {
		$response['status'] = true;
		$response['message'] = "Data berhasil didapatkan";
		$response['result'] = $value;
	} else {
		$response['status'] = false;
		$response['message'] = "Data gagal didapatkan";
		$response['result'] = $value;
	}
	mysqli_close($koneksi);
	echo json_encode($response);
}

function getLogin()
{

	global $koneksi;
	$response = array();

	$requestPayload = file_get_contents('php://input');
	$data = json_decode($requestPayload, true);

	$email_user = $data['email'];
	$pass = $data['password'];
	//$pass_hash = password_hash($pass, PASSWORD_BCRYPT);
	$sql = "SELECT * FROM tbl_user WHERE email_user='$email_user'";
	$data = mysqli_query($koneksi, $sql);
	$value = mysqli_fetch_all($data, MYSQLI_ASSOC);

	if ($value) {
		if ((password_verify($pass, $value[0]['password']))) {
			$response['status'] = true;
			$response['message'] = "Data berhasil didapatkan";
			$response['result'] = $value;
		} else {
			$response['status'] = false;
			$response['message'] = "Kata sandi yang anda masukan salah";
		}
	} else {
		$response['status'] = false;
		$response['message'] = "Email Anda belum terdaftar";
		$response['result'] = $value;
	}

	mysqli_close($koneksi);
	echo json_encode($response);
}