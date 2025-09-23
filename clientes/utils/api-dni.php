<?php
// clientes/utils/api-dni.php

$dni = $_GET['dni'] ?? '';

if (!$dni || !preg_match('/^\d{8}$/', $dni)) {
    echo json_encode(['error' => 'DNI inválido']);
    exit;
}

// 🔑 Reemplaza con tu token real
$token = "7c9f1f8d02a2f71f58fa92c2a6d6adf1de9a48649cb6fbe9601f761c71fd44d4";

$body = json_encode([
    'token' => $token,
    'type_document' => 'dni',
    'document_number' => $dni
]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.consultasperu.com/api/v1/query');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

curl_close($ch);

if ($error) {
    echo json_encode(['error' => 'cURL Error: ' . $error]);
    exit;
}

if ($httpCode === 200) {
    $data = json_decode($response, true);
    if (isset($data['success']) && $data['success']) {
        echo json_encode([
            'nombre' => $data['data']['full_name'],
            'direccion' => $data['data']['address'] ?? '',
            'existe' => true
        ]);
    } else {
        echo json_encode([
            'error' => $data['message'] ?? 'No encontrado',
            'nombre' => null,
            'direccion' => ''
        ]);
    }
} else {
    echo json_encode([
        'error' => 'HTTP ' . $httpCode,
        'nombre' => null,
        'direccion' => ''
    ]);
}
exit;
?>