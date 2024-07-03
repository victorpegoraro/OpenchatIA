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

$data = json_decode(file_get_contents('php://input'), true);
if (isset($data['msg'])) {
    $msg = "Em poucas palavras : " .  $data['msg'];

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "http://localhost:11434/api/chat",
        CURLOPT_RETURNTRANSFER => false,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 60,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode(
            [
                'model' => 'llama3',
                'stream' => true,
                "messages" => [
                    [
                        "role" => "user",
                        "content" => $msg
                    ]
                ]
            ]
        ),
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/json"
        ],
        CURLOPT_WRITEFUNCTION => function ($curl, $data) {
            echo $data;
            ob_flush();
            flush();
            return strlen($data);
        }
    ]);

    $response = curl_exec($curl);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        if ($http_code !== 200) {
            http_response_code($http_code);
        }
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'wrong values']);
}
?>
