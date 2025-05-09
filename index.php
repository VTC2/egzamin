<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: /logowanie/log.php");
    exit;
}

$polaczenie = new mysqli("localhost", "root", "", "rzeki");
if ($polaczenie->connect_error) {
    die("Błąd połączenia z bazą: " . $polaczenie->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['lista']) && isset($_SESSION['grupa']) && $_SESSION['grupa'] === 'Badacze') {
    $idWodomierza = $_POST['lista'];
    $dataPomiaru = date('Y-m-d', strtotime($_POST['data']));
    $stanWody = $_POST['stanOdpadow'];
    $sqlInsert = "INSERT INTO pomiary (wodowskazy_id, dataPomiaru, stanWody) VALUES ($idWodomierza, '$dataPomiaru', $stanWody)";
    $polaczenie->query($sqlInsert);
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Poziomy rzek</title>
    <link rel="stylesheet" href="styl.css">
</head>
<body>

<header>
    <div>
        <img src="obraz1.png" alt="Mapa Polski">
    </div>
    <div>
        <h1>Rzeki w województwie dolnośląskim</h1>

        <?php if (isset($_SESSION['grupa'])): ?>
            <p>Zalogowano jako: <?= htmlspecialchars($_SESSION['grupa']) ?></p>
            <form method="post" action="">
                <input type="submit" name="logout" value="Wyloguj">
            </form>
        <?php else: ?>
            <p>Nie jesteś zalogowany.</p>
        <?php endif; ?>
    </div>
</header>

<main>
    <section id="leftBlock">
        <h3>Stany na dzień 2022-05-05</h3>

        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
            <?php
            $sqlSelect = "SELECT W.nazwa, W.rzeka, W.stanOstrzegawczy, W.stanAlarmowy, P.stanWody 
                        FROM Wodowskazy AS W 
                        INNER JOIN Pomiary AS P ON W.id = P.wodowskazy_id 
                        WHERE P.dataPomiaru = '2022-05-05'";
            $wynik = $polaczenie->query($sqlSelect);
            ?>
            <table>
                <tr>
                    <th>Wodomierz</th>
                    <th>Rzeka</th>
                    <th>Ostrzegawczy</th>
                    <th>Alarmowy</th>
                    <th>Aktualny</th>
                </tr>
                <?php while ($wiersz = $wynik->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($wiersz['nazwa']) ?></td>
                        <td><?= htmlspecialchars($wiersz['rzeka']) ?></td>
                        <td><?= $wiersz['stanOstrzegawczy'] ?></td>
                        <td><?= $wiersz['stanAlarmowy'] ?></td>
                        <td><?= $wiersz['stanWody'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>

            <?php if ($_SESSION['grupa'] === 'Badacze'): ?>
                <form method="post" action="index.php">
                    <label for="lista">Wodomierz</label>
                    <select id="lista" name="lista" required>
                        <?php
                        $opcjaWodomierze = $polaczenie->query("SELECT id, nazwa FROM Wodowskazy");
                        while ($opcja = $opcjaWodomierze->fetch_assoc()) {
                            echo "<option value='{$opcja['id']}'>" . htmlspecialchars($opcja['nazwa']) . "</option>";
                        }
                        ?>
                    </select>

                    <label for="data">Data</label>
                    <input type="date" id="data" name="data" required>

                    <label for="stanOdpadow">Pomiar</label>
                    <input type="number" id="stanOdpadow" name="stanOdpadow" required>

                    <input type="submit" value="Dodaj pomiar">
                </form>
            <?php else: ?>
                <p>Dodawanie dostępne tylko dla grupy Badacze.</p>
            <?php endif; ?>

        <?php else: ?>
            <p>Dane dostępne po zalogowaniu.</p>
        <?php endif; ?>
    </section>

    <section id="rightBlock">
        <h3>Informacje</h3>
        <ul>
            <li>Brak ostrzeżeń o burzach z gradem</li>
            <li>Smog w mieście Wrocław</li>
            <li>Silny wiatr w Karkonoszach</li>
        </ul>

        <h3>Średnie stany wód</h3>
        <a href="https://komunikaty.pl">Dowiedz się więcej</a>
        <img src="obraz2.jpg" alt="rzeka">
    </section>
</main>

<footer>
    <p>Stronę wykonał: Abdul, ..., ..., ...,</p>
</footer>

<?php
$polaczenie->close(); 
?>

</body>
</html>
