<?php
$db = new SQLite3('data.db');
$url = $_POST['url'];
$password = $_POST['password'] ?? '';
$code = substr(md5($url . time()), 0, 6);
$db->exec("INSERT INTO urls (code, url, password) VALUES ('$code', '$url', '$password')");
echo "<p style='color:#0f0;'>URL raccourcie : <a style='color:#0ff;' href='/$code'>https://ushort-b3uz.onrender.com/$code</a></p>";
?>
<a href="index.php" style="color:#0f0;">Retour</a>
