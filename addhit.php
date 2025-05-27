<?php
// add_hits_column.php (script à lancer une fois)
$db = new PDO("sqlite:urls.db");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Vérifier si la colonne hits existe déjà
$columns = $db->query("PRAGMA table_info(urls)")->fetchAll(PDO::FETCH_ASSOC);
$hasHits = false;
foreach ($columns as $col) {
    if ($col['name'] === 'hits') {
        $hasHits = true;
        break;
    }
}

if (!$hasHits) {
    $db->exec("ALTER TABLE urls ADD COLUMN hits INTEGER DEFAULT 0");
    echo "Colonne hits ajoutée.\n";
} else {
    echo "Colonne hits existe déjà.\n";
}
