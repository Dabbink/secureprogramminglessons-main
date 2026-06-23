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

    $insertUser = $pdo->prepare("INSERT INTO `user` (`id`, `username`, `password`, `balance`, `isAdmin`) VALUES (?, ?, ?, ?, ?)");
    $insertUser->execute([1, 'Admin', password_hash('OmanidoAdmin!2026#Secure', PASSWORD_DEFAULT), 1000.00, 0]);
    $insertUser->execute([2, 'FerryKuhlman', password_hash('Ferry-Kuhlman!2026#Secure', PASSWORD_DEFAULT), 1255.36, 0]);
    $insertUser->execute([5, 'Han2002', password_hash('Han-2002!2026#Secure', PASSWORD_DEFAULT), 23424.84, 0]);
    $insertUser->execute([6, 'RoyBos', password_hash('Roy-Bos!2026#Secure', PASSWORD_DEFAULT), 9.23, 0]);
}
