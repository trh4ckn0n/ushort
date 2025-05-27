<?php
$db = new PDO("sqlite:urls.db");
$db->exec("CREATE TABLE IF NOT EXISTS urls (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    code TEXT UNIQUE,
    url TEXT,
    password TEXT,
    hits INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");
echo "Base de données initialisée.";
?>
