<?php
  $servername = "localhost"; 
  $username = "root";        
  $password = "";            
  $dbname = "rzeki";    

  $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Błąd połączenia: " . $conn->connect_error);
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $wodomierz = $_POST['lista'];
        $data = $_POST['data'];
        $pomiar = $_POST['stanOdpadow'];
    
        $sql_insert = "INSERT INTO Pomiary (wodowskazy_id, dataPomiaru, stanWody)
        VALUES (12, '2022-05-07', 350);";
        $stmt = $conn->prepare($sql_insert);
        $stmt->bind_param("iss", $wodomierz, $data, $pomiar);
        $stmt->execute();
        $stmt->close();
    }
    
    $sql_select = "SELECT W.nazwa, W.rzeka, W.stanOstrzegawczy, W.stanAlarmowy, P.stanWody FROM Wodowskazy AS W INNER JOIN Pomiary AS P ON W.id = P.wodowskazy_id WHERE P.dataPomiaru = '2022-05-05';
    ";
    $result = $conn->query($sql_select);

    $mysql_close($conn)
    ?>