<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: log.php");
    exit;
}

$polaczenie = new mysqli("localhost", "root", "", "rzeki");
if ($polaczenie->connect_error) {
    die("Błąd połączenia z bazą: " . $polaczenie->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['lista'], $_POST['data'], $_POST['stanOdpadow']) && isset($_SESSION['grupa']) && $_SESSION['grupa'] === 'Badacze') {
    $idWodomierza = $_POST['lista'];
    $dataPomiaru = $_POST['data'];
    $stanWody = $_POST['stanOdpadow'];

    $stmt = $polaczenie->prepare("INSERT INTO pomiary (wodowskazy_id, dataPomiaru, stanWody) VALUES (?, ?, ?)");
    $stmt->bind_param("isi", $idWodomierza, $dataPomiaru, $stanWody);
    $stmt->execute();
    $stmt->close();
}

$datyWynik = $polaczenie->query("SELECT DISTINCT dataPomiaru FROM Pomiary ORDER BY dataPomiaru");
$daty = [];
while ($wiersz = $datyWynik->fetch_assoc()) {
    $daty[] = $wiersz['dataPomiaru'];
}

$indexDaty = count($daty) - 1;
if (isset($_GET['data'])) {
    $klucz = array_search($_GET['data'], $daty);
    if ($klucz !== false) {
        $indexDaty = $klucz;
    }
}
$aktualnaData = $daty[$indexDaty] ?? null;

$poprzedniaData = ($indexDaty > 0) ? $daty[$indexDaty - 1] : null;
$nastepnaData = ($indexDaty < count($daty) - 1) ? $daty[$indexDaty + 1] : null;

$wynik = null;
if ($aktualnaData) {
    $zapytanie = "
        SELECT w.nazwa, w.rzeka, w.stanOstrzegawczy, w.stanAlarmowy, p.stanWody
        FROM Wodowskazy w
        JOIN Pomiary p ON w.id = p.wodowskazy_id
        WHERE p.dataPomiaru = '$aktualnaData'
    ";
    $wynik = $polaczenie->query($zapytanie);
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
    <div id="ledtHeader">
        <img src="obraz1.png" alt="Mapa Polski">
    </div>
    <div>
        <h1>Rzeki w województwie dolnośląskim</h1>

        <?php if (isset($_SESSION['grupa'])): ?>
            <p>Zalogowano jako: <?= htmlspecialchars($_SESSION['grupa']) ?></p>
            <form method="post">
                <input type="submit" name="logout" value="Wyloguj">
            </form>
        <?php else: ?>
            <p>Nie jesteś zalogowany.</p>
        <?php endif; ?>
    </div>
</header>

<main>
<?php if (isset($_SESSION['grupa'])): ?>
    <section id="leftBlock">
        <div id="daty">
            <?php if ($poprzedniaData): ?>
                <a href="?data=<?= $poprzedniaData ?>">← poprzednia</a>
            <?php endif; ?>

            <form method="get" action="index.php" style="display: inline;">
                <select name="data" onchange="this.form.submit()">
                    <?php foreach ($daty as $data): ?>
                        <option value="<?= $data ?>" <?= $data == $aktualnaData ? 'selected' : '' ?>>
                            <?= $data ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>

            <?php if ($nastepnaData): ?>
                <a href="?data=<?= $nastepnaData ?>">następna →</a>
            <?php endif; ?>
        </div>

        <h3>Stany na dzień <?= htmlspecialchars($aktualnaData) ?></h3>
        <table>
            <tr>
                <th>Wodomierz</th>
                <th>Rzeka</th>
                <th>Ostrzegawczy</th>
                <th>Alarmowy</th>
                <th>Aktualny</th>
            </tr>
            <?php if ($wynik && $wynik->num_rows > 0): ?>
                <?php while ($wiersz = $wynik->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($wiersz['nazwa']) ?></td>
                        <td><?= htmlspecialchars($wiersz['rzeka']) ?></td>
                        <td><?= $wiersz['stanOstrzegawczy'] ?></td>
                        <td><?= $wiersz['stanAlarmowy'] ?></td>
                        <td><?= $wiersz['stanWody'] ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5">Brak danych dla tej daty.</td></tr>
            <?php endif; ?>
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
    </section>
<?php else: ?>
    <section><p>Dane dostępne po zalogowaniu.</p></section>
<?php endif; ?>

    <section id="rightBlock">
        <h3>Informacje</h3>
        <ul>
            <li>Brak ostrzeżeń o burzach z gradem</li>
            <li>Smog w mieście Wrocław</li>
            <li>Silny wiatr w Karkonoszach</li>
        </ul>

        <h3>Średnie stany wód</h3>
        <?php if (isset($_SESSION['grupa'])): ?>
    <table>
        <tr>
            <td>data</td>
            <td>stan</td>
        </tr>
        <?php 
        $wynik = $polaczenie->query("
            SELECT dataPomiaru, AVG(stanWody) as sredniStanWody 
            FROM Pomiary 
            GROUP BY dataPomiaru 
            ORDER BY dataPomiaru
        ");
        while ($wiersz = $wynik->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($wiersz['dataPomiaru'])  ?></td>
            <td><?= number_format($wiersz['sredniStanWody'], 1) ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p>Średnie stany wód dostępne po zalogowaniu.</p>
<?php endif; ?>
        <a href="https://komunikaty.pl">Dowiedz się więcej</a>
        <img src="obraz2.jpg" alt="rzeka">
    </section>
</main>

<footer>
    <p>Stronę wykonał: Dzmitry, Michał, Abdul, Antek</p>
</footer>

<?php $polaczenie->close(); ?>
</body>
</html>