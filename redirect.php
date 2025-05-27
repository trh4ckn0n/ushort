<?php
// redirect.php

try {
    $db_file = __DIR__ . '/urls.db';
    $db = new PDO("sqlite:$db_file");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Création des tables si besoin
    $db->exec("CREATE TABLE IF NOT EXISTS urls (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        code TEXT UNIQUE,
        url TEXT NOT NULL,
        password TEXT,
        hits INTEGER DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS clicks (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        url_id INTEGER,
        click_time DATETIME DEFAULT CURRENT_TIMESTAMP,
        ip TEXT,
        user_agent TEXT,
        country TEXT,
        city TEXT,
        FOREIGN KEY(url_id) REFERENCES urls(id)
    )");

    function getClientIP() {
        foreach (['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'] as $key) {
            if (!empty($_SERVER[$key])) {
                $ips = explode(',', $_SERVER[$key]);
                return trim($ips[0]);
            }
        }
        return 'UNKNOWN';
    }

    function getGeoLocation($ip) {
        $url = "http://ip-api.com/json/".urlencode($ip);
        $json = @file_get_contents($url);
        if ($json === false) return ['country' => null, 'city' => null];
        $data = json_decode($json, true);
        if ($data && $data['status'] === 'success') {
            return ['country' => $data['country'] ?? null, 'city' => $data['city'] ?? null];
        }
        return ['country' => null, 'city' => null];
    }

    $code = $_GET['code'] ?? '';
    if ($code === '') {
        http_response_code(400);
        echo "Code manquant.";
        exit;
    }

    $stmt = $db->prepare("SELECT * FROM urls WHERE code = :code");
    $stmt->execute([':code' => $code]);
    $url_data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$url_data) {
        http_response_code(404);
        echo "URL non trouvée.";
        exit;
    }

    if ($url_data['password']) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pass = $_POST['password'] ?? '';
            if (!password_verify($pass, $url_data['password'])) {
                $error = "Mot de passe incorrect.";
            } else {
                $password_ok = true;
            }
        }
        if (empty($password_ok)) {
            ?>
            <!DOCTYPE html>
            <html lang="fr">
            <head><meta charset="UTF-8"><title>Protection par mot de passe</title></head>
            <body>
            <h2>Cette URL est protégée par mot de passe</h2>
            <?php if (!empty($error)) echo '<p style="color:red;">'.htmlspecialchars($error).'</p>'; ?>
            <form method="POST">
                <input type="password" name="password" placeholder="Mot de passe" required autofocus>
                <button type="submit">Valider</button>
            </form>
            </body>
            </html>
            <?php
            exit;
        }
    }

    $ip = getClientIP();
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    $geo = getGeoLocation($ip);

    $stmt = $db->prepare("INSERT INTO clicks (url_id, ip, user_agent, country, city) VALUES (:url_id, :ip, :ua, :country, :city)");
    $stmt->execute([
        ':url_id' => $url_data['id'],
        ':ip' => $ip,
        ':ua' => $user_agent,
        ':country' => $geo['country'],
        ':city' => $geo['city']
    ]);

    $updateHits = $db->prepare("UPDATE urls SET hits = hits + 1 WHERE id = :id");
    $updateHits->execute([':id' => $url_data['id']]);

    header("Location: " . $url_data['url']);
    exit;

} catch (Exception $e) {
    http_response_code(500);
    echo "Erreur serveur : " . htmlspecialchars($e->getMessage());
    exit;
}
