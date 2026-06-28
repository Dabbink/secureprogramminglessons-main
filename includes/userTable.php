<?php
// Controleer of de 'user' tabel al bestaat
$checkTable = $pdo->query("SHOW TABLES LIKE 'user'");
if ($checkTable->rowCount() == 0) {
    // Maak de 'user' tabel als deze nog niet bestaat
   $pdo->exec("CREATE TABLE `user` (
        `id` int NOT NULL AUTO_INCREMENT,
        `username` varchar(50) NOT NULL,
        `password` varchar(255) NOT NULL,
        `balance` decimal(10,2) NOT NULL,
        `isAdmin` tinyint(1) NOT NULL DEFAULT '0',
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci");

// NIEUW: Maak de standaardwachtwoorden cryptografisch onleesbaar met password_hash
    $usersToInsert = [
        [1, 'Admin', password_hash('AlfaBankAdminAccount', PASSWORD_DEFAULT), 1000.00, 1], // Admin op 1 gezet voor juiste autorisatie
        [2, 'FerryKuhlman', password_hash('12345678', PASSWORD_DEFAULT), 1255.36, 0],
        [5, 'Han2002', password_hash('password', PASSWORD_DEFAULT), 23424.84, 0],
        [6, 'RoyBos', password_hash('qwerty', PASSWORD_DEFAULT), 9.23, 0]
    ];

    // NIEUW: Bereid de query veilig voor via een Prepared Statement
    $stmt = $pdo->prepare("INSERT INTO `user` (`id`, `username`, `password`, `balance`, `isAdmin`) VALUES (?, ?, ?, ?, ?)");

    // Voer de query uit voor elke gebruiker afzonderlijk
    foreach ($usersToInsert as $user) {
        $stmt->execute($user);
    }
}