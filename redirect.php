<?php
// redirect.php

$db = new PDO("sqlite:urls.db");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$code = $_GET['code'] ?? '';
if (!$code) {
    http_response_code(400);
    exit("Code manquant.");
}

// Récupération de l'URL
$stmt = $db->prepare("SELECT * FROM urls WHERE code = ?");
$stmt->execute([$code]);
$urlData = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$urlData) {
    http_response_code(404);
    exit("URL non trouvée.");
}

// Infos sur le visiteur
$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$ua = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

// Géolocalisation via ip-api.com
$geo = @json_decode(file_get_contents("http://ip-api.com/json/{$ip}?fields=status,country,city"));
$country = ($geo && $geo->status === "success") ? $geo->country : 'Unknown';
$city = ($geo && $geo->status === "success") ? $geo->city : 'Unknown';

// Enregistrer le clic
$stmt = $db->prepare("INSERT INTO clicks (url_id, ip, country, city, user_agent) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$urlData['id'], $ip, $country, $city, $ua]);

// Incrémenter le compteur de vues
$db->prepare("UPDATE urls SET hits = hits + 1 WHERE id = ?")->execute([$urlData['id']]);

// Redirection
header("Location: " . $urlData['url']);
exit;
