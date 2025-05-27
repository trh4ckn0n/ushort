<?php
$db = new SQLite3('data.db');
$path = trim($_SERVER['REQUEST_URI'], '/');
$res = $db->querySingle("SELECT * FROM urls WHERE code = '$path'", true);
if (!$res) { die("URL non trouvée."); }
if ($res['password']) {
    if (!isset($_POST['pwd']) || $_POST['pwd'] !== $res['password']) {
        echo "<form method='post'><input name='pwd' placeholder='Mot de passe'><button type='submit'>Accéder</button></form>";
        exit;
    }
}
$db->exec("UPDATE urls SET clicks = clicks + 1 WHERE code = '$path'");
header("Location: {$res['url']}");
exit;
?>
