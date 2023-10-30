<?php

require_once('config/config.php'); // Mengimpor konstanta API_KEY
require_once('functions/function.php');

$headers = apache_request_headers();

// Set header untuk menangani respons JSON
header("Content-Type: application/json; charset=UTF-8");

// Ambil URL dari permintaan
$url = isset($_GET['url']) ? $_GET['url'] : '/';
$method = $_SERVER['REQUEST_METHOD'];
$access = $headers['token'] === API_KEY ? true : false;
if ($access) {
	if ($url === 'getPaket') getPaket();
	else if ($url === 'uploadFile') {
		$img = $_FILES['img'];
		if ($method === 'POST') upload($img);
		else echo json_encode(array("message" => "Not Found"));
	} else if ($url === 'permohonan') {
		if ($method === 'POST') insertPermohonan();
		else if ($method === 'GET') getPermohonan();
		else echo json_encode(array("message" => "Not Found"));
	} else if ($url === 'batal') {
		if ($method === 'POST') batal_pelanggan();
		else echo json_encode(array("message" => "Not Found"));
	}else if ($url === 'alamat') {
		if ($method === 'POST') postAlamat();
		else if ($method === 'GET') getAlamat();
		else echo json_encode(array("message" => "Not Found"));
	} else if ($url === 'notif') {
		if ($method === 'POST') updatePesanTerbaca();
		else if ($method === 'GET') getNotif();
		else echo json_encode(array("message" => "Not Found"));
	} else if ($url === 'pembayaran') {
		if ($method === 'GET') getPembayaran();
		else echo json_encode(array('message' => 'Not Found'));
	}else if ($url === 'pembayaranInvoice') {
		if ($method === 'GET') getPembayaranInvoice();
		else echo json_encode(array('message' => 'Not Found'));
	}else if ($url === 'login') {
		if ($method === 'POST') postLogin();
		else echo json_encode(array('message' => 'Not Found'));
	} else if ($url === 'register') {
		if ($method === 'POST') postRegister();
		else echo json_encode(array('message' => 'Not Found'));
	} else if ($url === 'user_profile') {
		if ($method === 'GET') get_user_profile();
		else echo json_encode(array('message' => 'Not Found'));
	} else if ($url === 'banner') {
		if ($method === 'GET') get_banner();
		else echo json_encode(array('message' => 'Not Found'));
	} else if ($url === 'faq') {
		if ($method === 'GET') get_faq();
		else echo json_encode(array('message' => 'Not Found'));
	} else if ($url === 'user') {
		if ($method === 'GET') get_user();
		else if ($method === 'POST') post_user();
		else echo json_encode(array('message' => 'Not Found'));
	}else if($url === 'forgot'){
		if($method === 'GET') get_check_email();
		else if($method === 'POST') post_reset_password();
		else echo json_encode(array('message'=> 'Not Found'));
	}else if($url === 'tagihan'){
		if($method === 'GET') get_data_tagihan();
		else echo json_encode(array('message'=> 'Not Found'));
	}else {
		http_response_code(404);
		echo json_encode(array("message" => "Not Found"));
	}
} else {
	if ($url === 'callback') {
		if ($method === 'POST') post_callback();
		else echo json_encode(array('message' => 'Not Found'));
	}else if ($url === 'callback') {
		if ($method === 'POST') post_callback();
		else echo json_encode(array('message' => 'Not Found'));
	}else {
		echo json_encode(array('message' => 'Permission Denied'));
	}
}
