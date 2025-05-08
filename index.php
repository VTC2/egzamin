<?php
$serwer = "localhost";
$uzytkownik = "root";
$haslo = "";
$baza = "rzeki";

$polaczenie = new mysqli($serwer, $uzytkownik, $haslo, $baza);
if ($polaczenie->connect_error) {
    die("Błąd połączenia z bazą: " . $polaczenie->connect_error);
}

$datyWynik = $polaczenie->query("select distinct dataPomiaru from Pomiary order by dataPomiaru");
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
$aktualnaData = $daty[$indexDaty];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idWodomierza = $_POST['lista'];
    $dataPomiaru = date('Y-m-d', strtotime($_POST['data']));
    $stanWody = $_POST['stanOdpadow'];

    $sqlInsert = "INSERT INTO pomiary (wodowskazy_id, dataPomiaru, stanWody) 
                  VALUES ($idWodomierza, '$dataPomiaru', $stanWody)";
    $polaczenie->query($sqlInsert);
}

$sqlSelect = "SELECT W.nazwa, W.rzeka, W.stanOstrzegawczy, W.stanAlarmowy, P.stanWody 
              FROM Wodowskazy AS W 
              INNER JOIN Pomiary AS P ON W.id = P.wodowskazy_id 
              WHERE P.dataPomiaru = '$aktualnaData'";
$wynik = $polaczenie->query($sqlSelect);

$poprzedniaData = ($indexDaty > 0) ? $daty[$indexDaty - 1] : null;
$nastepnaData = ($indexDaty < count($daty) - 1) ? $daty[$indexDaty + 1] : null;
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
        </div>
    </header>

    <main>
        <section id="leftBlock">
            <div id="daty">
                <?php if ($poprzedniaData): ?>
                    <a href="?data=<?= $poprzedniaData ?>">poprzednia</a> <!-- dodaj styl do przycisku -->

                <?php endif; ?>
                <h3 style="margin: 0;">Stany na dzień <?= htmlspecialchars($aktualnaData) ?></h3>
                <?php if ($nastepnaData): ?>
                    <a href="?data=<?= $nastepnaData ?>">nastepna</a> <!-- dodaj styl do przycisku -->
                <?php endif; ?>
            </div>

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
<button id="dodaj" onclick="dodaj()">Dodaj pomiar</button>
            <form method="post" action="index.php" id="wprowadzanie">
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
        </section>

        <section id="rightBlock">
            <h3>Informacje</h3>
            <ul>
                <li>Brak ostrzeżeń o burzach z gradem</li>
                <li>Smog w mieście Wrocław</li>
                <li>Silny wiatr w Karkonoszach</li>
            </ul>

            <h3>Średnie stany wód</h3>
            <table>
                <tr>
                    <td>data</td>
                    <td>stan</td>
                    
                    <?php 
                    $wynik->data_seek(0); 
                    $wynik = $polaczenie->query("SELECT dataPomiaru, AVG(stanWody) as sredniStanWody 
                    FROM Pomiary 
                    GROUP BY dataPomiaru 
                    ORDER BY dataPomiaru");
                    while ($wiersz = $wynik->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($wiersz['dataPomiaru'])  ?></td>
                        <td><?= number_format($wiersz['sredniStanWody'], 1) ?></td>
                        
                    </tr>
                <?php endwhile; ?>
                </tr>
            </table>
            <a href="https://komunikaty.pl">Dowiedz się więcej</a>
            <img src="obraz2.jpg" alt="rzeka">
        </section>
    </main>

    <footer>
        <p>Stronę wykonał: Abdul, ..., ..., ...,</p>
        <Script src="script.js"></Script>
    </footer>

    <?php
    $polaczenie->close();
    ?>
</body>

</html>