<?php
// index.php
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>trhacknon URL Shortener</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <h1>trhacknon URL Shortener</h1>
    <form method="POST" action="shorten.php">
        <label>URL à raccourcir :</label><br/>
        <input type="url" name="url" required placeholder="https://exemple.com" style="width: 400px" /><br/><br/>

        <label>Mot de passe (facultatif) :</label><br/>
        <input type="text" name="password" placeholder="Protéger l'URL par un mot de passe" style="width: 400px" /><br/><br/>

        <button type="submit">Raccourcir</button>
    </form>

    <?php if (!empty($_GET['short'])): ?>
        <hr/>
        <p>URL raccourcie : <a href="redirect.php?c=<?=htmlspecialchars($_GET['short'])?>" target="_blank">
            <?=htmlspecialchars($_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/redirect.php?c=' . $_GET['short'])?>
        </a></p>
    <?php endif; ?>

    <hr/>
    <p><a href="admin.php" style="color:#0f0;">Accéder à l'interface Admin</a></p>
</body>
</html>
