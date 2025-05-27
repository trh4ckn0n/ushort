<?php
session_start();

$db_file = __DIR__ . '/urls.db';
$db = new PDO("sqlite:$db_file");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Table déjà créée ailleurs

function generateCode($length = 6, $prefix = 'tr') {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $code .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $prefix . $code;
}

function isCodeExists($db, $code) {
    $stmt = $db->prepare("SELECT 1 FROM urls WHERE code = :code");
    $stmt->execute([':code' => $code]);
    return $stmt->fetchColumn() !== false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['last_submit']) || time() - $_SESSION['last_submit'] >= 5) {
        $_SESSION['last_submit'] = time();
    } else {
        die("Trop rapide. Merci d'attendre quelques secondes.");
    }

    $url = trim($_POST['url'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (filter_var($url, FILTER_VALIDATE_URL) === false) {
        die("URL invalide.");
    }

    $host = parse_url($url, PHP_URL_HOST);
    if (in_array($host, ['localhost', '127.0.0.1']) || preg_match('/^(\d{1,3}\.){3}\d{1,3}$/', $host)) {
        die("Hôte interdit.");
    }

    // Éviter les doublons si aucun mot de passe
    $stmt = $db->prepare("SELECT code FROM urls WHERE url = :url AND password IS NULL");
    $stmt->execute([':url' => $url]);
    $existing = $stmt->fetchColumn();
    if ($existing) {
        header("Location: index.php?short=$existing");
        exit;
    }

    do {
        $code = generateCode(6, 'tr');
    } while (isCodeExists($db, $code));

    $pwd_hash = $password !== '' ? password_hash($password, PASSWORD_DEFAULT) : null;

    $stmt = $db->prepare("INSERT INTO urls (code, url, password) VALUES (:code, :url, :password)");
    $stmt->execute([
        ':code' => $code,
        ':url' => $url,
        ':password' => $pwd_hash
    ]);

    header("Location: index.php?short=$code");
    exit;
}

header("Location: index.php");
exit;
