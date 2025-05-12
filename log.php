<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Logowanie</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php 
session_start();

$error = '';

$host = 'localhost';      
$dbname = 'rzeki';    
$dbuser = 'root';          
$dbpass = '';              

$conn = new mysqli($host, $dbuser, $dbpass, $dbname);
if ($conn->connect_error) {
    die("Błąd połączenia z bazą danych: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameInput = $_POST['username'] ?? '';
    $passwordInput = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT haslo, grupa FROM uzytkownicy WHERE nazwa = ?");
    $stmt->bind_param("s", $usernameInput);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($hashedPassword, $grupa);
        $stmt->fetch();
        var_dump($usernameInput);
        var_dump($hashedPassword);
        var_dump($passwordInput);

        if ($passwordInput === $hashedPassword) {
            $_SESSION['logged_in'] = true;
            $_SESSION['username'] = $usernameInput;
            $_SESSION['grupa'] = $grupa;
            header("Location: index.php");
            exit;
        } else {
            $error = "Niepoprawne hasło.";
        }
    } else {
        $error = "Nie znaleziono użytkownika.";
    }

    $stmt->close();
}

$conn->close();
?>

    <div id="logIn">
        <div class="headerLogin">
            <h2>Logowanie</h2>
        </div>
        <div class="formLogin">
            <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
            <form method="post" action="log.php">
                <div class="usernameLogin">
                    <label for="username">Nazwa użytkownika:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="passwordLogin">    
                    <label for="password">Hasło:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="confirmLogin">
                    <input type="submit" value="Zaloguj" id="submitLogin">
                </div>
            </form>
        </div>
    </div>
</body>
</html>