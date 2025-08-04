<?php
/*
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL
);
*/
$dsn = "mysql:host=dbs.spskladno.cz;dbname=vyuka1;charset=utf8mb4";
$username = "student1";
$password = "spsnet";

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Chyba připojení k databázi: " . $e->getMessage());
}


function insertUser($pdo, $name, $email)
{
    $sql = "INSERT INTO users (name, email) VALUES (:name, :email)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':name' => $name,
        ':email' => $email
    ]);
}


function fetchUsers($pdo)
{
    $sql = "SELECT * FROM users";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';

    if (!empty($name) && !empty($email)) {
        try {
            insertUser($pdo, $name, $email);
            $message = "Uživatel byl úspěšně přidán.";
        } catch (PDOException $e) {
            $message = "Chyba při přidávání uživatele: " . $e->getMessage();
        }
    } else {
        $message = "Vyplňte všechna pole.";
    }
}


$users = fetchUsers($pdo);
?>
<!DOCTYPE html>
<html lang="cs">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seznam uživatelů</title>
</head>

<body>
    <h1>Správa uživatelů</h1>
    <?php if (!empty($message)): ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <h2>Přidat nového uživatele</h2>
    <form method="POST" action="">
        <label for="name">Jméno:</label><br>
        <input type="text" id="name" name="name" required><br><br>
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br><br>
        <button type="submit">Přidat</button>
    </form>

    <h2>Seznam uživatelů</h2>
    <?php if (!empty($users)): ?>
        <ul>
            <?php foreach ($users as $user): ?>
                <li>ID: <?= htmlspecialchars($user['id']) ?>, Jméno: <?= htmlspecialchars($user['name']) ?>, Email: <?= htmlspecialchars($user['email']) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Žádní uživatelé nejsou k dispozici.</p>
    <?php endif; ?>
</body>


</html>
