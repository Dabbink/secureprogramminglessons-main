<?php
// PDO db connection
$host = 'db';  // Dit moet overeenkomen met de servicenaam van MySQL in docker-compose.yml
$db   = 'mydb'; // De naam van je database
$user = 'user'; // Je MySQL-gebruikersnaam
$pass = 'test'; // Je MySQL-wachtwoord
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

function isWeakPassword(string $password): bool
{
    $commonPasswords = [
        '12345678',
        'abcdefg',
        'geheim',
        'password',
        'qwerty',
        'wachtwoord',
        '123456',
        '123456789',
        'welkom',
        'admin',
    ];

    return in_array(strtolower($password), $commonPasswords, true);
}

function validatePasswordStrength(string $password): ?string
{
    if (strlen($password) < 12) {
        return "Het wachtwoord moet minimaal 12 tekens lang zijn";
    }

    if (!preg_match('/[a-z]/', $password) || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[^a-zA-Z0-9]/', $password)) {
        return "Gebruik minimaal een kleine letter, hoofdletter, cijfer en speciaal teken";
    }

    if (isWeakPassword($password)) {
        return "Dit wachtwoord is te vaak gebruikt of eerder gelekt";
    }

    return null;
}

function ensureAuthColumns(PDO $pdo): void
{
    $columns = $pdo->query("SHOW COLUMNS FROM `user`")->fetchAll(PDO::FETCH_COLUMN);

    if (!in_array('failed_login_attempts', $columns, true)) {
        $pdo->exec("ALTER TABLE `user` ADD `failed_login_attempts` int NOT NULL DEFAULT 0");
    }

    if (!in_array('locked_until', $columns, true)) {
        $pdo->exec("ALTER TABLE `user` ADD `locked_until` datetime NULL DEFAULT NULL");
    }
}

function secureDefaultAccounts(PDO $pdo): void
{
    $securePasswords = [
        'Admin' => 'OmanidoAdmin!2026#Secure',
        'FerryKuhlman' => 'Ferry-Kuhlman!2026#Secure',
        'Han2002' => 'Han-2002!2026#Secure',
        'RoyBos' => 'Roy-Bos!2026#Secure',
    ];

    $stmt = $pdo->query("SELECT id, username, password FROM `user`");
    $users = $stmt->fetchAll();

    foreach ($users as $user) {
        $storedPassword = $user['password'];
        $needsHash = !password_get_info($storedPassword)['algo'];
        $isKnownWeakPassword = isWeakPassword($storedPassword);

        if ($needsHash || $isKnownWeakPassword) {
            $newPassword = $securePasswords[$user['username']] ?? bin2hex(random_bytes(24));
            $update = $pdo->prepare("UPDATE `user` SET password = ?, failed_login_attempts = 0, locked_until = NULL WHERE id = ?");
            $update->execute([password_hash($newPassword, PASSWORD_DEFAULT), $user['id']]);
        }
    }
}
?>
