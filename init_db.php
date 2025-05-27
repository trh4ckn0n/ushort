<?php
// init_db.php

$dbFile = __DIR__ . '/urls.db';

if (file_exists($dbFile)) {
    echo "La base de données existe déjà. Aucune action nécessaire.\n";
    exit;
}

try {
    $db = new PDO("sqlite:$dbFile");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Table des URLs
    $db->exec("
        CREATE TABLE IF NOT EXISTS urls (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            code TEXT UNIQUE,
            url TEXT NOT NULL,
            password TEXT,
            hits INTEGER DEFAULT 0
        );
    ");

    // Table des clics
    $db->exec("
        CREATE TABLE IF NOT EXISTS clicks (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            url_id INTEGER NOT NULL,
            ip TEXT,
            user_agent TEXT,
            country TEXT,
            city TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY(url_id) REFERENCES urls(id) ON DELETE CASCADE
        );
    ");

    echo "Base de données initialisée.\n";
} catch (PDOException $e) {
    echo "Erreur lors de l'initialisation : " . $e->getMessage() . "\n";
    exit(1);
}
