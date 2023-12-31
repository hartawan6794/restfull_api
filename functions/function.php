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
	$target = "img/pelanggan/";
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
	$nama_gambar = 'pelanggan_' . $timestamp . '.' . $ext[1];

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

	$sql = "SELECT * FROM tbl_pelanggan tp inner join tbl_paket tpak on tpak.id_paket = tp.id_paket where id_user = '$id_user' ";
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
	$sql = "INSERT INTO tbl_pelanggan VALUE(null,'$id_user','$nik','$nm_user','$tgl_lahir','$tmp_lahir','$telpon','$alamat','$gender','$img',$langganan,'$id_alamat','$id_paket','$created_at',null)";
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

function getPembayaranInvoice(){
	global $koneksi;

	$response = array();

	$invoice = $_GET['invoice'];

	$sql = "SELECT invoice,pembayaran,tp.created_at,nm_paket,jns_pembayaran,nm_user,telpon FROM tbl_pembayaran tp INNER JOIN tbl_paket tpaket on tp.id_paket = tpaket.id_paket inner join tbl_pelanggan tpeh on tp.id_pelanggan = tpeh.id_pelanggan 
		where tp.invoice = '$invoice'";


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
function postLogin()
{

	global $koneksi;
	$response = array();

	$requestPayload = file_get_contents('php://input');
	$data = json_decode($requestPayload, true);

	$email_user = $data['email'];
	$pass = $data['password'];
	$token = $data['token_notification'];
	// var_dump($email_user);die;
	//$pass_hash = password_hash($pass, PASSWORD_BCRYPT);
	$sql = "SELECT id_user,emaiL_user,tu.password,tud.nm_user as device_id FROM tbl_user tu inner join tbl_user_detail tud on tu.id_user = tud.id_user_detail 
			WHERE email_user='$email_user'";
	$data = mysqli_query($koneksi, $sql);
	$value = mysqli_fetch_all($data, MYSQLI_ASSOC);

	$id_user = $value[0]['id_user'];

	if ($value) {
		if ((password_verify($pass, $value[0]['password']))) {
			$sqlUpdate = "UPDATE tbl_user SET token_notification = '$token' WHERE id_user='$id_user'";
			if ($koneksi->query($sqlUpdate)) {
				$response['status'] = true;
				$response['message'] = "Data berhasil didapatkan";
				$response['result'] = $value;
			} else {
				$response['status'] = false;
				$response['message'] = "Gagal update FCM token";
			}
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

function postRegister()
{

	global $koneksi;
	$response = array();

	$requestPayload = file_get_contents('php://input');
	$data = json_decode($requestPayload, true);

	$email_user = $data['modelUser']['email_user'];
	$pass = password_hash($data['modelUser']['password'], PASSWORD_BCRYPT);
	$device_id = $data['modelUser']['device_id'];
	$token_notification = $data['modelUser']['token_notification'];
	$telpon = $data['modelUser']['telpon'];
	$created_at = date('Y-m-d H:i:s');

	$nm_user = $data['modelUserDetail']['nm_user'];

	// Mulai transaksi
	$koneksi->autocommit(FALSE);

	$sqlInsertUser = "INSERT INTO tbl_user VALUES(null,'$email_user','$pass','$device_id','$token_notification','$telpon','1')";

	//cek email atau user
	$sqlCekEmail = "SELECT email_user from tbl_user WHERE email_user = '$email_user'";
	$data = mysqli_query($koneksi, $sqlCekEmail);
	$value = mysqli_fetch_all($data, MYSQLI_ASSOC);

	if ($value == null) {
		if ($koneksi->query($sqlInsertUser) === TRUE) {
			$insert_id = $koneksi->insert_id;
			$sqlInsertUserDetail = "INSERT INTO tbl_user_detail(id_user_detail,nm_user,tgl_lahir,created_at) VALUES($insert_id,'$nm_user','0000-00-00','$created_at')";
			if ($koneksi->query($sqlInsertUserDetail) === TRUE) {
				$koneksi->commit();
				$response['status'] = true;
				$response['message'] = "Berhasil menambahkan data";
			} else {
				// Jika pernyataan kedua gagal, rollback transaksi
				$koneksi->rollback();
				$response['status'] = false;
				$response['message'] = "Error user Detail: " . $sqlInsertUserDetail . "<br>" . $koneksi->error;
			}
		} else {
			// Jika pernyataan kedua gagal, rollback transaksi
			$koneksi->rollback();
			$response['status'] = false;
			$response['message'] = "Error User: " . $sqlInsertUser . "<br>" . $koneksi->error;
		}
	} else {
		$response['status'] = false;
		$response['message'] = "Email sudah terdaftar";
	}

	mysqli_close($koneksi);
	echo json_encode($response);
}

function get_user_profile()
{
	global $koneksi;

	$response = array();

	$id_user = $_GET['id_user'];
	$type = $_GET['type'];

	if ($type == 'user_bio') {
		$sql = "SELECT * FROM tbl_user tu INNER JOIN tbl_user_detail tud ON tu.id_user = tud.id_user_detail WHERE tu.id_user = '$id_user'";
	} else if ($type == 'pelanggan') {
		$sql = "SELECT * FROM tbl_pelanggan tp inner join tbl_paket tpak on tpak.id_paket = tp.id_paket where id_user = '$id_user' ";
	}

	$data = mysqli_query($koneksi, $sql);
	$value = mysqli_fetch_all($data, MYSQLI_ASSOC);

	if ($value) {
		$response['status'] = true;
		$response['message'] = "Data berhasil didapatkan";
		$response['result'] = $value;
	} else {
		$response['status'] = false;
		$response['message'] = "Data gagal didapatkan";
	}


	mysqli_close($koneksi);
	echo json_encode($response);
}

function get_banner()
{
	global $koneksi;

	$response = array();


	$sql = "SELECT * FROM tbl_banner_informasi limit 8";

	$data = mysqli_query($koneksi, $sql);
	$value = mysqli_fetch_all($data, MYSQLI_ASSOC);

	if ($value) {
		$response['status'] = true;
		$response['message'] = "Data berhasil didapatkan";
		$response['result'] = $value;
	} else {
		$response['status'] = false;
		$response['message'] = "Data gagal didapatkan";
	}


	mysqli_close($koneksi);
	echo json_encode($response);
}

function get_faq()
{
	global $koneksi;

	$response = array();


	$sql = "SELECT * FROM tbl_faq";

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

function post_callback()
{
	$apiKey = '1e69910d4338feaa6fcd80a86d21581c'; // API key anda
	$merchantCode = isset($_POST['merchantCode']) ? $_POST['merchantCode'] : null;
	$amount = isset($_POST['amount']) ? $_POST['amount'] : null;
	$merchantOrderId = isset($_POST['merchantOrderId']) ? $_POST['merchantOrderId'] : null;
	$productDetail = isset($_POST['productDetail']) ? $_POST['productDetail'] : null;
	$additionalParam = isset($_POST['additionalParam']) ? $_POST['additionalParam'] : null;
	$paymentMethod = isset($_POST['paymentCode']) ? $_POST['paymentCode'] : null;
	$resultCode = isset($_POST['resultCode']) ? $_POST['resultCode'] : null;
	$merchantUserId = isset($_POST['merchantUserId']) ? $_POST['merchantUserId'] : null;
	$reference = isset($_POST['reference']) ? $_POST['reference'] : null;
	$signature = isset($_POST['signature']) ? $_POST['signature'] : null;
	$publisherOrderId = isset($_POST['publisherOrderId']) ? $_POST['publisherOrderId'] : null;
	$spUserHash = isset($_POST['spUserHash']) ? $_POST['spUserHash'] : null;
	$settlementDate = isset($_POST['settlementDate']) ? $_POST['settlementDate'] : null;
	$issuerCode = isset($_POST['issuerCode']) ? $_POST['issuerCode'] : null;

	//log callback untuk debug 
	// file_put_contents('callback.txt', "* Callback *\r\n", FILE_APPEND | LOCK_EX);

	if (!empty($merchantCode) && !empty($amount) && !empty($merchantOrderId) && !empty($signature)) {
		$params = $merchantCode . $amount . $merchantOrderId . $apiKey;
		$calcSignature = md5($params);

		if ($signature == $calcSignature) {
			//Callback tervalidasi
			//Silahkan rubah status transaksi anda disini
			file_put_contents('callback.txt', "* Success *\r\n\r\n", FILE_APPEND | LOCK_EX);
		} else {
			// file_put_contents('callback.txt', "* Bad Signature *\r\n\r\n", FILE_APPEND | LOCK_EX);
			throw new Exception('Bad Signature');
		}
	} else {
		// file_put_contents('callback.txt', "* Bad Parameter *\r\n\r\n", FILE_APPEND | LOCK_EX);
		throw new Exception('Bad Parameter');
	}
}

function get_user()
{
	global $koneksi;
	$response = array();

	$id_user_detail = $_GET['id_user_detail'];

	$sql = "SELECT * FROM tbl_user_detail WHERE id_user_detail = '$id_user_detail'";
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

function post_user()
{

	global $koneksi;
	$response = array();

	$target = "img/user/";

	$id_user_detail = $_POST['id_user_detail'] ? $_POST['id_user_detail'] : '';
	$nik = $_POST['nik'] ? $_POST['nik'] : '';
	$nm_user = $_POST['nm_user'] ? $_POST['nm_user'] : '';
	$tgl_lahir = $_POST['tgl_lahir'] ? $_POST['tgl_lahir'] : '';
	$tmp_lahir = $_POST['tmp_lahir'] ? $_POST['tmp_lahir'] : '';
	$jns_kelamin = $_POST['jns_kelamin'] ? $_POST['jns_kelamin'] : '';
	$updated_at = date('Y-m-d H:i:s');
	$img = $_FILES['img'];
	$nama_gambar = '';

	if ($img != null) {
		//memberikan izin file upload
		$allowedFormats = ['image/jpeg', 'image/png'];
		//tentukan ukuran max file
		$maxFileSize = 2 * 1024 * 1024; // 2 MB
		// Mendapatkan timestamp saat ini
		$timestamp = time();

		$sumber = $img['tmp_name'];
		//mendapatkan ukuran image
		$imageSize = $img['size'];
		//mendapatkan ext image
		$ext = explode('/', $img['type']);
		//meberikan nama baru pada image yang ingin di upload
		$nama_gambar = 'user_' . $timestamp . '.' . $ext[1];
		//logika upload
		$sql = "UPDATE tbl_user_detail SET nik = '$nik', nm_user = '$nm_user', tgl_lahir = '$tgl_lahir', tmp_lahir = '$tmp_lahir', updated_at = '$updated_at', jns_kelamin = '$jns_kelamin', img_user = '$nama_gambar' WHERE id_user_detail = '$id_user_detail'";
		if ($imageSize <= $maxFileSize) {
			if (in_array($img['type'], $allowedFormats)) {
				$sqlDataImg = "SELECT img_user FROM tbl_user_detail WHERE id_user_detail='$id_user_detail'";
				$data = mysqli_query($koneksi, $sqlDataImg);
				$value = mysqli_fetch_all($data, MYSQLI_ASSOC);
				if (move_uploaded_file($sumber, $target . $nama_gambar)) {
					if ($sqlDataImg) {
						unlink($target . $value[0]['img_user']);
					}
					if ($koneksi->query($sql) === TRUE) {
						$response['status'] = true;
						$response['message'] = 'Berhasil ubah data profile';
					} else {
						$response['status'] = true;
						$response['message'] = 'Gagal ubah data profile';
					}
				} else {
					$response['status'] = false;
					$response['message'] = 'Gagal upload photo profile hubungi admin';
				}
			} else {
				$response['status'] = false;
				$response['message'] = 'Gambar yang di upload harus berupa png atau jpeg';
			}
		} else {
			$response['status'] = false;
			$response['message'] = 'File yang di upload harus kurang dari 2MB';
		}
	} else {
		$sql = "UPDATE tbl_user_detail SET nik = '$nik', nm_user = '$nm_user', tgl_lahir = '$tgl_lahir', tmp_lahir = '$tmp_lahir', updated_at = '$updated_at', jns_kelamin = '$jns_kelamin' WHERE id_user_detail = '$id_user_detail'";

		if ($koneksi->query($sql) === TRUE) {
			$response['status'] = true;
			$response['message'] = 'Berhasil ubah data profile';
		} else {
			$response['status'] = true;
			$response['message'] = 'Gagal ubah data profile';
		}
	}


	mysqli_close($koneksi);
	echo json_encode($response);
}

function get_check_email()
{

	global $koneksi;

	$response = array();

	$email = $_GET['email'];

	$sql = "SELECT * FROM tbl_user WHERE email_user = '$email'";

	$data = mysqli_query($koneksi, $sql);
	$value = mysqli_fetch_all($data, MYSQLI_ASSOC);

	if ($value) {
		$response["status"] = true;
		$response["message"] = "Berhasil mendapatkan data";
		$response['result'] = $value;
	} else {
		$response["status"] = false;
		$response["message"] = "Gagal mendapatkan data";
		$response['result'] = $value;
	}

	mysqli_close($koneksi);
	echo json_encode($response);
}


function post_reset_password()
{
	global $koneksi;
	$response = array();

	$requestPayload = file_get_contents('php://input');
	$data = json_decode($requestPayload, true);

	$id_user = $data['id_user'];
	$pass = password_hash($data['password'], PASSWORD_BCRYPT);

	$sql = "UPDATE tbl_user SET password = '$pass' WHERE id_user = '$id_user'";

	if ($koneksi->query($sql)) {
		$response["status"] = true;
		$response["message"] = "Berhasil update password";
	} else {
		$response["status"] = false;
		$response["message"] = "Gagal update password";
	}

	mysqli_close($koneksi);
	echo json_encode($response);
}

function batal_pelanggan(){

	global $koneksi;

	$response = array();

	$requestPayload = file_get_contents("php://input");
	$data = json_decode($requestPayload, true);

	$id_pelanggan = $data['id_pelanggan'];

	$sql = "DELETE FROM tbl_pelanggan WHERE id_pelanggan = '$id_pelanggan'";

	if ($koneksi->query($sql)) {
		$response["status"] = true;
		$response["message"] = "Berhasil membatalkan langganan";
	} else {
		$response["status"] = false;
		$response["message"] = "Terjadi error hubungi admin";
	}

	mysqli_close($koneksi);
	echo json_encode($response);
}

function get_data_tagihan(){
	global $koneksi;
	$response = array();
	$id_user = $_GET['id_user'];

	$sql = "SELECT id_tagihan,tpem.invoice,keterangan,nm_user,nm_paket,jatuh_tempo,periode,pembayaran,tpem.status FROM tbl_tagihan ttag INNER JOIN tbl_pelanggan tpel on ttag.id_pelanggan = tpel.id_pelanggan INNER JOIN tbl_user tu ON tu.id_user = ttag.id_user INNER JOIN tbl_paket tpak ON tpak.id_paket = ttag.id_paket INNER JOIN tbl_periode tper ON ttag.id_periode = tper.id_periode INNER JOIN tbl_pembayaran tpem ON ttag.invoice = tpem.invoice AND tpem.status < 1 WHERE tu.id_user = '$id_user' ORDER by id_tagihan DESC";
	
	$data = mysqli_query($koneksi, $sql);
	$value = mysqli_fetch_all($data, MYSQLI_ASSOC);
	
	if ($value) {
		$response["status"] = true;
		$response["message"] = "Berhasil mendapatkan data";
		$response['result'] = $value;
	} else {
		$response["status"] = false;
		$response["message"] = "Gagal mendapatkan data";
		$response['result'] = $value;
	}

	mysqli_close($koneksi);
	echo json_encode($response);
	
}