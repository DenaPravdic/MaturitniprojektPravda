<?php
session_start();

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("Location: pologinu.php");
    exit;
}

$servername = "dbs.spskladno.cz";
$username = "student18";
$password = "spsnet";
$database = "vyuka18";

$conn = new mysqli($servername, $username, $password, $database);




if ($conn->connect_error) {
    die("Chyba připojení: " . $conn->connect_error);
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["register"])) {
    $uzivatel = $_POST["username"];
    $heslo = password_hash($_POST["password"], PASSWORD_BCRYPT);

    
    $sql_check = "SELECT * FROM 1registrace WHERE username = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("s", $uzivatel);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Toto uživatelské jméno už existuje!'); window.history.back();</script>";
    } else {
        
        $sql = "INSERT INTO 1registrace (username, password) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $uzivatel, $heslo);

        if ($stmt->execute()) {
            echo "<script>alert('Registrace úspěšná! Teď se přihlaste.'); window.location.href='rezervace.php';</script>";
        } else {
            echo "<script>alert('Chyba při registraci!'); window.history.back();</script>";
        }
        $stmt->close();
    }
    $stmt_check->close();
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login"])) {
    $uzivatel = $_POST["login_username"];
    $heslo = $_POST["login_password"];

    
    $sql = "SELECT password FROM 1registrace WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $uzivatel);
    $stmt->execute();
    $stmt->bind_result($hashed_password);
    $stmt->fetch();
    $stmt->close();

    
    if (password_verify($heslo, $hashed_password)) {
        $_SESSION["username"] = $uzivatel;
        $_SESSION["loggedin"] = true;
        echo "<script>alert('Přihlášení úspěšné!'); window.location.href='pologinu.php';</script>";
        
    } else {
        echo "<script>alert('Špatné uživatelské jméno nebo heslo!'); window.history.back();</script>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Přihlášení / Registrace</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 20px;
        }
        .nav {
            background-color: #333;
            overflow: hidden;
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .nav h1 {
            color: rgb(222, 195, 50);
            margin-left: 20px;
        }
        .nav a {
            color: white;
            text-decoration: none;
            padding: 14px 20px;
        }
        .nav a:hover {
            background-color: #575757;
            border-radius: 5px;
        }
        .container {
            width: 40%;
            margin: auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 2px 2px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .input-group {
            margin: 10px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        label {
            margin-bottom: 5px;
        }
        input {
            width: 80%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            margin-top: 15px;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="nav">
        <h1>Pneuservis Kladno</h1>
        <div>
            <a href="strana.html">Hlavní strana</a>
            <a href="rezervace.php">Rezervace termínu</a>
            <a href="Kontakt.html">Kontakt</a>
        </div>
    </div>

    <header>
        <img src="header.jpg" alt="Kontakt Header" style="width:100%; max-height:200px; object-fit:cover;">
    </header>

    <div class="container">
        <h2>Registrace</h2>
        <form method="POST">
            <div class="input-group">
                <label for="username">Uživatelské jméno:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="input-group">
                <label for="password">Heslo:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" name="register">Registrovat</button>
        </form>
    </div>

    <div class="container">
        <h2>Přihlášení</h2>
        <form method="POST">
            <div class="input-group">
                <label for="login_username">Uživatelské jméno:</label>
                <input type="text" id="login_username" name="login_username" required>
            </div>
            <div class="input-group">
                <label for="login_password">Heslo:</label>
                <input type="password" id="login_password" name="login_password" required>
            </div>
            <button type="submit" name="login">Přihlásit</button>
        </form>
    </div>
</body>
</html>

