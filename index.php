<?php
$db = new SQLite3('data.db');
$db->exec("CREATE TABLE IF NOT EXISTS urls (id INTEGER PRIMARY KEY, code TEXT, url TEXT, password TEXT, clicks INTEGER DEFAULT 0)");
?>
<!DOCTYPE html>
<html>
<head>
    <title>trhacknon URL Shortener</title>
    <style>
        body { background: #111; color: #0f0; font-family: monospace; text-align: center; padding: 50px; }
        input, button { background: #000; color: #0f0; border: 1px solid #0f0; padding: 10px; margin: 10px; width: 80%%; }
    </style>
</head>
<body>
<h1># trhacknon URL Shortener</h1>
<form method="post" action="shorten.php">
    <input type="url" name="url" placeholder="Long URL" required><br>
    <input type="text" name="password" placeholder="Mot de passe (optionnel)"><br>
    <button type="submit">Raccourcir</button>
</form>
</body>
</html>
