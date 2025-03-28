<?php
$servername = "dbs.spskladno.cz";
$username = "student18";
$password = "spsnet";
$database = "vyuka18";

// Vytvoření připojení
$conn = new mysqli($servername, $username, $password, $database);

// Kontrola připojení
if ($conn->connect_error) {
    die("Chyba připojení: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['spz'])) {
        $uzivatelske_jmeno = $_POST['username'];
        $heslo = $_POST['password'];
        $spz = $_POST['spz'];

        // Hashování hesla pro bezpečnost
        $hashed_heslo = password_hash($heslo, PASSWORD_DEFAULT);

        // Připravení SQL dotazu pro vložení dat
        $sql = "INSERT INTO uzivatele (uzivatelske_jmeno, heslo, spz) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $uzivatelske_jmeno, $hashed_heslo, $spz);

        if ($stmt->execute()) {
            
            // Přesměrování zpět na stránku rezervací
            header("Location: https://xeon.spskladno.cz/~pravdam/projekt%20maturitn%C3%AD/pologinu.php");
            exit();
        } else {
            echo "Chyba při ukládání: " . $stmt->error;
        }

        $stmt->close();
    }
}

$conn->close();
?>
