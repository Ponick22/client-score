<?php
header('Content-Type: application/json');

$uri     = $_SERVER['REQUEST_URI'];
$uriPath = parse_url($uri, PHP_URL_PATH);
$parts   = explode('/', trim($uriPath, '/'));

$phone = end($parts);

$phone = preg_replace('/\D+/', '', $phone);

if (empty($phone)) {
    http_response_code(400);
    echo json_encode(['error' => 'Phone required']);
    exit;
}

if (strlen($phone) !== 11) {
    http_response_code(400);
    echo json_encode(['error' => 'Phone invalid']);
    exit;
}

$prefix = substr($phone, 1, 3);

if ($prefix >= 900 && $prefix <= 905 || $prefix >= 955 && $prefix <= 960) {
    $operator = "МТС";
} else if ($prefix >= 920 && $prefix <= 925 || $prefix >= 960 && $prefix <= 965) {
    $operator = "Билайн";
} else if ($prefix >= 940 && $prefix <= 945 || $prefix >= 970 && $prefix <= 975) {
    $operator = "Мегафон";
} else if ($prefix >= 990 && $prefix <= 995 || $prefix >= 905 && $prefix <= 910) {
    $operator = "Теле2";
} else {
    $operator = "Yota";
}

echo json_encode([
    'phone' => $phone,
    'oper'  => [
        'brand' => $operator,
    ],
]);
