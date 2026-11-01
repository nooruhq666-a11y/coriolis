<?php
function getRedirectionsFile() {
    $file = __DIR__ . '/redirections.json';
    if (!file_exists($file)) {
        file_put_contents($file, '[]');
        @chmod($file, 0666);
    }
    return $file;
}

function getRedirections() {
    $file = getRedirectionsFile();
    $content = @file_get_contents($file);
    if ($content === false) return [];
    $data = json_decode($content, true);
    return is_array($data) ? $data : [];
}

function saveRedirections($redirections) {
    $file = getRedirectionsFile();
    $json = json_encode($redirections, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    $result = @file_put_contents($file, $json, LOCK_EX);
    return $result !== false;
}