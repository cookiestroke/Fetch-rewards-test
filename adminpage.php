<?php

require 'conn.php';

?>

<!DOCTYPE html>
<html>

<head>
    <title>Fetch Transactions</title>
</head>

<body>
    <a href='index.php' style="width:100%;border:10px;padding:10px;background:lightyellow;">Customer Entry Page</a>
    <h2 style="width:100%; background:lightgrey;">Wallet</h2>
    <?php

    $sql = "SELECT payer, sum(points) as total from uwallet group by payer";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows >= 0) {
            while ($row = $result->fetch_assoc()) {
                echo ("<h4>" . $row['payer'] . '&nbsp;&nbsp;&nbsp;&nbsp;' . $row['total'] . "</h4>");
            }
            $result->close();
        }
    }

    mysqli_stmt_close($stmt);

    ?>
    <br><br>
    <h2 style="width:100%; background:lightgrey;">Transactions</h2>
    <?php

    $sql = "SELECT * from tsaction";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows >= 0) {
            while ($row = $result->fetch_assoc()) {
                echo ("<h4>" . $row['tindex'] . '&nbsp;&nbsp;&nbsp;&nbsp;' . $row['payer'] . '&nbsp;&nbsp;&nbsp;&nbsp;' . $row['points'] . '&nbsp;&nbsp;&nbsp;&nbsp;' . $row['tstamp'] . "</h4>");
            }
            $result->close();
        }
    }

    mysqli_stmt_close($stmt);

    ?>

    <br>
    <form method="POST">
        <button type="submit" name="treset">Reset Transactions</button>
    </form>

    <?php

    if (isset($_POST["treset"])) {
        $sql = "DELETE from tsaction";
        $sql1 = "DELETE from uwallet";

        if ($stmt = mysqli_prepare($conn, $sql) and $stmt1 = mysqli_prepare($conn, $sql1)) {
            $stmt->execute();
            $stmt1->execute();
        }

        mysqli_stmt_close($stmt);
        mysqli_stmt_close($stmt1);
        $conn->close();
        echo ("<SCRIPT LANGUAGE='JavaScript'>
                  window.alert('Reset all transactions')
                  window.location.href='index.php';
                  </SCRIPT>");
    }


    ?>


</body>

</html>