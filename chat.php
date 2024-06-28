<?php

header('Access-Control-Allow-Origin: *');
header ("Access-Control-Allow-Methods: POST, OPTIONS");
header('Cache-Control: no-cache, must-revalidate'); 
header ("Access-Control-Allow-Headers: Content-Type, Authorization, Accept, Accept-Language, X-Authorization");
header('Access-Control-Max-Age: 86400');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // The request is using the POST method
    header("HTTP/1.1 200 OK");
    return;
}

include_once('../rsc/config.php');
$data = json_decode(file_get_contents('php://input'), true);
if( isset( $data['msg'] ) ){

    $curl = curl_init();
    curl_setopt_array($curl, [
    CURLOPT_PORT => "11434",
    CURLOPT_URL => "http://localhost:11434/api/generate",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => json_encode( [ 'model' => 'openchat', 'stream' => false, "prompt" => "Olá" ] ),
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "User-Agent: insomnia/9.3.0"
    ],
    ]);

    $response = curl_exec($curl);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        //echo $response;
        $code = $http_code;
        $json = json_decode( $response, true );
    }
}else{
    $code = 400;
    $json = array( 'error'=> 'wrong values');
}
header('Content-Type: application/json; charset=utf-8');
http_response_code($code);
echo json_encode($json);
