<?php
// init_db.php

$db = new PDO("sqlite:urls.db");

$db->exec("
    CREATE TABLE IF NOT EXISTS urls (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        code TEXT UNIQUE,
        url TEXT,
        password TEXT,
        hits INTEGER DEFAULT 0
    );
");

$db->exec("
    CREATE TABLE IF NOT EXISTS clicks (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        url_id INTEGER,
        ip TEXT,
        user_agent TEXT,
        country TEXT,
        city TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );
");

echo "Base de données initialisée.";
