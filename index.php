<?php 
session_start();
include 'includes/db.php';

// Tables aanmaken
include 'includes/userTable.php';
include 'includes/transactionTable.php';

// Controleer of post is geset
if($_SERVER["REQUEST_METHOD"] == "POST") {
    // Gebruikersnaam en wachtwoord uit post halen
    $username = $_POST['username'];
    $password = $_POST['password'];

    // VEILIG: Prepared statement tegen SQL-injectie
    $sql = "SELECT * FROM user WHERE username = :username AND password = :password";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':username' => $username, 
        ':password' => $password
    ]);
    $user = $stmt->fetch();

    // Controleer of er een rij is gevonden
    if($user) {
        // Gebruiker is ingelogd
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['user'] = $user;

        header("location: dashboard.php");
        exit; // Zorg dat het script direct stopt na een redirect
    } else {
        // Gebruiker is niet ingelogd
        $error = "Gebruikersnaam of wachtwoord is onjuist";
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Omanido</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <?php include 'includes/header.php'; ?>

    <div class="container mx-auto mt-20 p-6 bg-white max-w-sm shadow-md rounded-md">
        <div class="flex justify-center">
            <img src="img/Omanido1.png" alt="Omanido Logo" class="mb-6 w-1/2">
        </div>
        <h2 class="text-lg text-center font-bold mb-6">Inloggen bij Omanido</h2>
        
        <?php if(isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4 text-sm">
                <?= htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="mb-4">
                <label for="username" class="block text-sm font-medium text-gray-700">Gebruikersnaam:</label>
                <input type="text" id="username" name="username" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700">Wachtwoord:</label>
                <input type="password" id="password" name="password" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <input type="submit" value="Inloggen" class="w-full bg-blue-600 text-white font-bold py-2 px-4 rounded hover:bg-blue-700 focus:outline-none focus:shadow-outline">
        </form>
        <a href="register.php" class="block text-center text-sm text-blue-600 hover:underline mt-4">Nog geen account? Registreer hier</a>
    </div>

    </body>
</html>