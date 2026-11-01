<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

$botToken = '8789856564:AAGjVTO-54kJn2yuBCZeMpPYwNR2Wq4kcvY'; // À remplacer par votre token
$chatId = '-5077491193'; // À remplacer par votre chat ID

$postData = json_decode(file_get_contents('php://input'), true);

if (!$postData || !isset($postData['message'])) {
    echo json_encode(['success' => false, 'error' => 'Message manquant']);
    exit;
}

$message = $postData['message'];
$url = "https://api.telegram.org/bot{$botToken}/sendMessage";

$data = [
    'chat_id' => $chatId,
    'text' => $message,
    'parse_mode' => 'HTML'
];

$options = [
    'http' => [
        'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
        'method' => 'POST',
        'content' => http_build_query($data)
    ]
];

$context = stream_context_create($options);
$result = file_get_contents($url, false, $context);

if ($result !== false) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Erreur envoi Telegram']);
}
?>