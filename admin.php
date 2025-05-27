<?php
// admin.php

$db = new PDO("sqlite:urls.db");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Protection admin simple par mot de passe
$admin_password = "trkn";
if (!isset($_GET['auth']) || $_GET['auth'] !== $admin_password) {
    die("Accès refusé. Ajoute ?auth=trkn à l'URL.");
}

// Liste des URLs
$urls = $db->query("SELECT * FROM urls ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin - trhacknon URL Shortener</title>
    <style>
        body { background: #0f0f0f; color: #0ff; font-family: monospace; padding: 20px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #0ff; padding: 8px; text-align: left; }
        th { background: #111; }
        h1 { color: #0f0; }
    </style>
</head>
<body>
    <h1>trhacknon URL Shortener - Interface Admin</h1>
    <table>
        <tr>
            <th>ID</th>
            <th>Code</th>
            <th>URL</th>
            <th>Hits</th>
            <th>Mot de passe</th>
            <th>Clicks</th>
        </tr>
        <?php foreach ($urls as $url): ?>
            <tr>
                <td><?= $url['id'] ?></td>
                <td><a href="<?= $url['code'] ?>" target="_blank"><?= $url['code'] ?></a></td>
                <td><?= htmlspecialchars($url['url']) ?></td>
                <td><?= $url['hits'] ?></td>
                <td><?= $url['password'] ? 'Oui' : 'Non' ?></td>
                <td>
                    <?php
                    $clicks = $db->prepare("SELECT * FROM clicks WHERE url_id = ?");
                    $clicks->execute([$url['id']]);
                    foreach ($clicks as $click) {
                        echo "IP: {$click['ip']} ({$click['city']}, {$click['country']})<br>UA: {$click['user_agent']}<hr>";
                    }
                    ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
