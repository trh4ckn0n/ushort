<?php
// admin.php
$db = new PDO("sqlite:urls.db");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Récupérer les URLs
$urls = $db->query("SELECT * FROM urls ORDER BY created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin - trhacknon URL Tracker</title>
    <style>
        body {
            background: #0f0f0f;
            color: #00ffcc;
            font-family: 'Courier New', monospace;
            padding: 20px;
        }
        h1, h2, h3 {
            color: #00ffaa;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }
        th, td {
            border: 1px solid #00ffcc;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #111;
        }
        a {
            color: #00ffff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .shorturl {
            font-weight: bold;
            color: #0ff;
        }
    </style>
</head>
<body>

<h1>Interface Admin - trhacknon URL Shortener</h1>

<?php foreach ($urls as $u): ?>
    <h2><span class="shorturl"><?= htmlspecialchars($u['code']) ?></span> → <?= htmlspecialchars($u['url']) ?> (<?= $u['hits'] ?> clics)</h2>
    <small>Créé le <?= htmlspecialchars($u['created_at']) ?></small>

    <?php
    $clicks = $db->prepare("SELECT * FROM clicks WHERE url_id = ? ORDER BY clicked_at DESC");
    $clicks->execute([$u['id']]);
    $clickData = $clicks->fetchAll();
    ?>

    <?php if ($clickData): ?>
        <table>
            <tr>
                <th>Date</th>
                <th>IP</th>
                <th>Pays</th>
                <th>Ville</th>
                <th>User-Agent</th>
            </tr>
            <?php foreach ($clickData as $c): ?>
                <tr>
                    <td><?= htmlspecialchars($c['clicked_at']) ?></td>
                    <td><?= htmlspecialchars($c['ip']) ?></td>
                    <td><?= htmlspecialchars($c['country']) ?></td>
                    <td><?= htmlspecialchars($c['city']) ?></td>
                    <td><?= htmlspecialchars($c['user_agent']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>Aucun clic enregistré.</p>
    <?php endif; ?>
<?php endforeach; ?>

</body>
</html>
