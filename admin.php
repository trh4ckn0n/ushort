<?php
$db = new SQLite3('data.db');
$res = $db->query("SELECT * FROM urls ORDER BY id DESC");
echo "<style>body{background:#111;color:#0f0;font-family:monospace;}table{width:100%%;}td{border-bottom:1px solid #0f0;padding:4px;}</style>";
echo "<h2># trhacknon - Admin Panel</h2><table>";
echo "<tr><th>Code</th><th>URL</th><th>MDP</th><th>Clics</th></tr>";
while ($row = $res->fetchArray()) {
    echo "<tr><td>{$row['code']}</td><td>{$row['url']}</td><td>{$row['password']}</td><td>{$row['clicks']}</td></tr>";
}
echo "</table><br><a href='index.php' style='color:#0f0;'>Retour</a>";
?>
