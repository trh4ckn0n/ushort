<?php
// shorten.php

// Configuration SQLite
$db_file = __DIR__ . '/urls.db';
$db = new PDO("sqlite:$db_file");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Création de la table si nécessaire
$db->exec("CREATE TABLE IF NOT EXISTS urls (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    code TEXT UNIQUE,
    url TEXT NOT NULL,
    password TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

function generateCode($length = 6) {
    return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, $length);
}

function isCodeExists($db, $code) {
    $stmt = $db->prepare("SELECT 1 FROM urls WHERE code = :code");
    $stmt->execute([':code' => $code]);
    return $stmt->fetchColumn() !== false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $url = trim($_POST['url'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (filter_var($url, FILTER_VALIDATE_URL) === false) {
        die("URL invalide.");
    }

    // On génère un code unique
    do {
        $code = generateCode(6);
    } while (isCodeExists($db, $code));

    // Hash du mot de passe s'il est défini
    $pwd_hash = $password !== '' ? password_hash($password, PASSWORD_DEFAULT) : null;

    // Insert dans la BDD
    $stmt = $db->prepare("INSERT INTO urls (code, url, password) VALUES (:code, :url, :password)");
    $stmt->execute([
        ':code' => $code,
        ':url' => $url,
        ':password' => $pwd_hash
    ]);

    // Redirection vers index avec code pour affichage
    header("Location: index.php?short=$code");
    exit;
}

header("Location: index.php");
exit;
