<?php
// Stockage des redirections dans un message Telegram épinglé
// Le message contient le JSON complet, on le met à jour à chaque changement

$BOT_TOKEN = '8789856564:AAGjVTO-54kJn2yuBCZeMpPYwNR2Wq4kcvY';
$CHAT_ID = '-5077491193';

function tgRequest($method, $params) {
    global $BOT_TOKEN;
    $url = "https://api.telegram.org/bot{$BOT_TOKEN}/{$method}";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);
    curl_close($ch);
    return json_decode($result, true);
}

// Récupère la liste des redirections depuis Telegram
function getRedirections() {
    global $CHAT_ID;
    
    // Chercher le message épinglé
    $pinned = tgRequest('getChat', ['chat_id' => $CHAT_ID]);
    if (!isset($pinned['result']['pinned_message']['text'])) {
        return [];
    }
    
    $text = $pinned['result']['pinned_message']['text'];
    // Format attendu : "REDIRECTIONS_JSON:{...}"
    if (strpos($text, 'REDIRECTIONS_JSON:') === 0) {
        $json = substr($text, strlen('REDIRECTIONS_JSON:'));
        $data = json_decode($json, true);
        return is_array($data) ? $data : [];
    }
    return [];
}

// Sauvegarde les redirections dans Telegram
function saveRedirections($redirections) {
    global $CHAT_ID;
    
    $json = json_encode($redirections, JSON_UNESCAPED_UNICODE);
    $text = 'REDIRECTIONS_JSON:' . $json;
    
    // Récupérer le message épinglé actuel
    $pinned = tgRequest('getChat', ['chat_id' => $CHAT_ID]);
    $pinnedMsgId = $pinned['result']['pinned_message']['message_id'] ?? null;
    
    // Si un message épinglé existe, on l'édite
    if ($pinnedMsgId) {
        $edit = tgRequest('editMessageText', [
            'chat_id' => $CHAT_ID,
            'message_id' => $pinnedMsgId,
            'text' => $text
        ]);
        if (isset($edit['ok']) && $edit['ok']) {
            return true;
        }
    }
    
    // Sinon, on envoie un nouveau message et on l'épingle
    $send = tgRequest('sendMessage', [
        'chat_id' => $CHAT_ID,
        'text' => $text,
        'disable_notification' => 'true'
    ]);
    
    if (isset($send['ok']) && $send['ok']) {
        $msgId = $send['result']['message_id'];
        tgRequest('pinChatMessage', [
            'chat_id' => $CHAT_ID,
            'message_id' => $msgId,
            'disable_notification' => 'true'
        ]);
        return true;
    }
    
    return false;
}