<?php
// Set header untuk menangani respons JSON
header("Content-Type: application/json; charset=UTF-8");

// Ambil URL dari permintaan
$url = isset($_GET['url']) ? $_GET['url'] : '/';
$method = $_SERVER['REQUEST_METHOD'];
require_once('functions/function.php');

if($url === 'getPaket') getPaket();
else if($url === 'uploadFile'){
	$img = $_FILES['img'];
	if($method === 'POST')upload($img);
	else echo json_encode(array("message" => "Not Found"));
}else if($url === 'permohonan'){
	if($method === 'POST') insertPermohonan();
	else if($method === 'GET') getPermohonan();
	else echo json_encode(array("message" => "Not Found"));
}else if($url === 'tokenFcm'){
	if($method === 'POST') postToken();
	else if($method === 'GET') getToken();
	else echo json_encode(array("message" => "Not Found"));
}else if($url === 'alamat'){
	if($method === 'POST') postAlamat();
	else if ($method === 'GET') getAlamat();
	else echo json_encode(array("message" => "Not Found"));
}else if($url === 'notif'){
	if($method === 'POST') updatePesanTerbaca();
	else if ($method === 'GET') getNotif();
	else echo json_encode(array("message" => "Not Found"));
}else if($url === 'pembayaran'){
	 if($method === 'GET') getPembayaran();
	 else echo json_encode(array('message' => 'Not Found'));
}else if($url === 'login'){
	if($method === 'POST') getLogin();
	else echo json_encode(array('message' => 'Not Found'));

}
else {

    http_response_code(404);
    echo json_encode(array("message" => "Not Found"));
}
 