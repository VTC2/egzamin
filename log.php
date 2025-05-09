<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Logowanie</title>
    <link rel="stylesheet" href="">
</head>
<body>
<?php 
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameInput = $_POST['username'] ?? '';
    $passwordInput = $_POST['password'] ?? '';

    $username1 = "Przeglądający";
    $password1 = "password";

    $username2 = "Badacze";
    $password2 = "password";

    if ($usernameInput === $username1 && $passwordInput === $password1) {
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $usernameInput;
        $_SESSION['grupa'] = 'Przeglądający';
        header("Location: /main/index.php"); 
        exit;
    } elseif ($usernameInput === $username2 && $passwordInput === $password2) {
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $usernameInput;
        $_SESSION['grupa'] = 'Badacze';
        header("Location: /main/index.php"); 
        exit;
    } else {
        $error = "Niepoprawne dane logowania.";
    }
}
?>
    <div id="logIn">
        <div class="headerLogin">
            <h2>Logowanie</h2>
        </div>
        <div class="formLogin">
            <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
            <form method="post" action="log.php">
                <div class="usernameLogin">
                    <label for="username">Nazwa użytkownika:</label><br>
                    <input type="text" id="username" name="username" required><br>
                </div>
                <div class="passwordLogin">    
                    <label for="password">Hasło:</label><br>
                    <input type="password" id="password" name="password" required><br><br>
                </div>
                <div class="confirmLogin">
                    <input type="submit" value="Zaloguj" id="submitLogin">
                </div>
            </form>
        </div>
    </div>
</body>
</html>
