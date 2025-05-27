<?php
// redirect.php
$db = new PDO("sqlite:urls.db");

$code = $_GET['code'] ?? '';
$stmt = $db->prepare("SELECT * FROM urls WHERE code = ?");
$stmt->execute([$code]);
$urlData = $stmt->fetch();

if ($urlData) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

    // Géolocalisation via IP-API
    $geo = @json_decode(file_get_contents("http://ip-api.com/json/{$ip}?fields=status,country,city"));
    $country = ($geo && $geo->status === "success") ? $geo->country : 'Unknown';
    $city = ($geo && $geo->status === "success") ? $geo->city : 'Unknown';

    // Log du clic
    $stmt = $db->prepare("INSERT INTO clicks (url_id, ip, country, city, user_agent) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$urlData['id'], $ip, $country, $city, $ua]);

    // Incrémenter les vues
    $db->prepare("UPDATE urls SET hits = hits + 1 WHERE id = ?")->execute([$urlData['id']]);

    header("Location: " . $urlData['url']);
    exit;
} else {
    http_response_code(404);
    echo "<h2>Erreur : Lien inexistant</h2>";
}
