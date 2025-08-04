<?php
session_start(); 

$servername = "dbs.spskladno.cz";
$username = "student18";
$password = "spsnet";
$database = "vyuka18";

$conn = new mysqli($servername, $username, $password, $database);


if ($conn->connect_error) {
    die("Chyba připojení: " . $conn->connect_error);
}


if (!isset($_SESSION["username"])) {
    echo "<script>alert('Musíte se nejprve přihlásit!'); window.location.href='rezervace.php';</script>";
    exit();
}

$logged_user = $_SESSION["username"]; 


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["spz"])) {
    $spz = $_POST["spz"];
    $barva = $_POST["barva"];
    $model = $_POST["model"];
    $datum = $_POST["datum_navstevy"];

    $sql = "INSERT INTO 1auta (username, spz, barva, model, datum_navstevy) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $logged_user, $spz, $barva, $model, $datum);

    if ($stmt->execute()) {
        echo "<script>alert('Vaše návštěva byla úspěšně zarezervována!'); window.location.href='pologinu.php';</script>";
    } else {
        echo "<script>alert('Chyba při rezervaci!'); window.history.back();</script>";
    }
    $stmt->close();
}


$navstevy = [];
$sql = "SELECT spz, barva, model, datum_navstevy, potvrzeno FROM 1auta WHERE username = ? ORDER BY datum_navstevy DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $logged_user);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $navstevy[] = $row;
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rezervace termínu</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; margin: 20px; }
        .nav { background-color: #333; overflow: hidden; padding: 10px; display: flex; justify-content: space-between; align-items: center; }
        .nav h1 { color: rgb(222, 195, 50); margin-left: 20px; }
        .nav a { color: white; text-decoration: none; padding: 14px 20px; }
        .nav a:hover { background-color: #575757; border-radius: 5px; }
        .container { width: 50%; margin: auto; padding: 20px; border: 1px solid #ccc; border-radius: 10px; box-shadow: 2px 2px 12px rgba(0, 0, 0, 0.1); }
        label { display: block; text-align: left; margin-top: 10px; }
        input { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 5px; }
        button { margin-top: 15px; padding: 10px 20px; background-color: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background-color: #218838; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background-color: #333; color: white; }
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
        <h3>Jste přihlášen jako <strong><?php echo htmlspecialchars($logged_user); ?></strong></h3>
        <form method="POST" action="logout.php">
            <button type="submit" style="background-color: red;">Odhlásit se</button>
        </form>
    </div>

    <div class="container">
        <h2>Zadejte informace o návštěvě</h2>
        <form method="POST">
            <label for="spz">SPZ vozidla:</label>
            <input type="text" id="spz" name="spz" required>

            <label for="barva">Barva vozidla:</label>
            <input type="text" id="barva" name="barva" required>

            <label for="model">Model vozidla:</label>
            <input type="text" id="model" name="model" required>

            <label for="datum_navstevy">Datum návštěvy:</label>
            <input type="date" id="datum_navstevy" name="datum_navstevy" required>

            <button type="submit">Potvrdit datum návštěvy</button>
        </form>
    </div>

    <div class="container">
    <h2>Vaše návštěvy</h2>
    <?php if (count($navstevy) > 0): ?>
        <table>
            <tr>
                <th>SPZ</th>
                <th>Barva</th>
                <th>Model</th>
                <th>Datum návštěvy</th>
                <th>Stav</th> 
            </tr>
            <?php foreach ($navstevy as $navsteva): ?>
                <tr>
                    <td><?php echo htmlspecialchars($navsteva["spz"]); ?></td>
                    <td><?php echo htmlspecialchars($navsteva["barva"]); ?></td>
                    <td><?php echo htmlspecialchars($navsteva["model"]); ?></td>
                    <td><?php echo htmlspecialchars($navsteva["datum_navstevy"]); ?></td>
                    <td style="text-align: center;">
                        <?php echo $navsteva["potvrzeno"] ? '✅' : '❌'; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>Zatím nemáte žádné návštěvy.</p>
    <?php endif; ?>
</div>
</body>
</html>

