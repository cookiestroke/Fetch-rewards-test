<?php

require 'conn.php';

?>

<!DOCTYPE html>
<html>

<head>
    <title>Fetch User</title>
</head>

<body>

    <a href='adminpage.php' style="width:100%;border:10px;padding:10px;background:lightyellow;">Transactions Page</a>

    <h2>Total Points</h2>
    <?php

    $sql = "SELECT sum(points) as total from uwallet";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows >= 0) {
            while ($row = $result->fetch_assoc()) {
                $totalprice = $row["total"];
                if ($totalprice > 0) {
                    echo ("<h2>" . $totalprice . "</h2>");
                } else {
                    echo ("<h2> 0 </h2>");
                }
            }
            $result->close();
        }
    }

    mysqli_stmt_close($stmt);

    ?>

    <div class="container" style="display: flex; height: 100px; padding:5%;">
        <div style="width: 50%;">
            <h3>Add points</h3>
            <form method="POST">
                <label for="fname">Name:</label>
                <input type="text" id="fname" name="fname" pattern="[A-Z ]*" value="Name in Capital Letters"><br><br>
                <label for="points">Points:</label>
                <input type="number" id="points" name="points" min="0"><br><br>
                <button type="submit" name="getpoint">Add Points</button>
            </form>
        </div>
        <div style="flex-grow: 1;">
            <h3>Pay points</h3>
            <form method="POST">
                <label for="payername">Name:</label>
                <?php

                $sql = "SELECT distinct payer as pnames from uwallet";

                if ($stmt = mysqli_prepare($conn, $sql)) {
                    $stmt->execute();
                    $result = $stmt->get_result();

                    echo "<select name='payername'>";

                    if ($result->num_rows >= 0) {
                        while ($row = $result->fetch_assoc()) {
                            $pname = $row["pnames"];
                            echo '<option value="' . $pname . '">' . $pname . '</option>';
                        }
                        $result->close();
                    } else {
                        echo ("<h2> 0 </h2>");
                    }

                    echo "</select>";
                }

                mysqli_stmt_close($stmt);

                ?>
                <label for="points1">Points:</label>
                <input type="number" id="points1" name="points1" min="0"><br><br>
                <button type="submit" name="givepoint">Pay Points</button>
            </form>
        </div>
    </div>

    <div style="width: 100%; padding:5%;">
        <h2>Spend Points</h2>
        <form method="POST">
            <label for="points2">Points:</label>
            <input type="number" id="points2" name="points2" min="0"><br><br>
            <button type="submit" name="spendpoint">Spend Points</button>
        </form>
    </div>

    <!-- ADDING POINTS -->
    <?php

    if (isset($_POST["getpoint"])) {

        $sql1 = "INSERT INTO uwallet (payer, points) VALUES (?, ?)";

        $sql = "INSERT INTO tsaction (payer,points,tstamp) VALUES (?, ?, CURRENT_TIMESTAMP())";

        if (($stmt = mysqli_prepare($conn, $sql)) and ($stmt1 = mysqli_prepare($conn, $sql1))) {
            // Bind variables to the prepared statement as parameters

            $name = $_POST['fname'];
            $points = $_POST['points'];

            mysqli_stmt_bind_param($stmt, "ss", $name, $points);

            mysqli_stmt_bind_param($stmt1, "ss", $name, $points);

            if (mysqli_stmt_execute($stmt) and mysqli_stmt_execute($stmt1)) {

                echo ("<SCRIPT LANGUAGE='JavaScript'>
                      window.alert('Added $points points from $name')
                      window.location.href='index.php';
                      </SCRIPT>");
            } else {
                echo ("<SCRIPT LANGUAGE='JavaScript'>
                        window.alert('Points failed')
                        </SCRIPT>");
            }
        } else {

            echo ("<SCRIPT LANGUAGE='JavaScript'>
                    window.alert('Error connecting to database')
                    </SCRIPT>");
        }

        mysqli_stmt_close($stmt);
        mysqli_stmt_close($stmt1);
        $conn->close();
    }

    ?>

    <!-- SPENDING POINTS -->

    <?php

    if (isset($_POST["givepoint"]) or isset($_POST["spendpoint"])) {


        $sql = "INSERT INTO tsaction (payer,points,tstamp) VALUES (?, ?, CURRENT_TIMESTAMP())";

        $sql1 = "SELECT * from uwallet";

        if (isset($_POST["givepoint"])) {
            $name = $_POST['payername'];
            $points = $_POST['points1'];
        } else {
            $name = 'YOU';
            $points = $_POST['points2'];
        }
        $negpoints = -$points;

        if ($totalprice < $points) {
            echo ("<SCRIPT LANGUAGE='JavaScript'>
                            window.alert('You only have $totalprice points')
                            </SCRIPT>");
            exit;
        }


        if ($stmt1 = mysqli_prepare($conn, $sql1)) {
            $stmt1->execute();
            $result = $stmt1->get_result();
            if ($result->num_rows >= 0) {
                while ($row = $result->fetch_assoc() and $points > 0) {

                    $rid = $row["windex"];
                    $rpoints = $row["points"];

                    if ($rpoints < $points) {
                        $points = $points - $rpoints;
                        $rpoints = 0;
                    } else {
                        $rpoints = $rpoints - $points;
                        $points = 0;
                    }

                    $rowupdate = "UPDATE uwallet SET points = ? where windex = ?";
                    if ($updstat = mysqli_prepare($conn, $rowupdate)) {
                        mysqli_stmt_bind_param($updstat, "ss", $rpoints, $rid);
                        $updstat->execute();
                    }
                    mysqli_stmt_close($updstat);
                }
                $result->close();
            }
            mysqli_stmt_close($stmt2);
        }

        $points = -$negpoints;

        if ($stmt = mysqli_prepare($conn, $sql)) {

            mysqli_stmt_bind_param($stmt, "ss", $name, $negpoints);

            if (mysqli_stmt_execute($stmt)) {

                echo ("<SCRIPT LANGUAGE='JavaScript'>
                  window.alert('Spent $points points on $name')
                  window.location.href='index.php';
                  </SCRIPT>");
            } else {
                echo ("<SCRIPT LANGUAGE='JavaScript'>
                    window.alert('Points failed')
                    </SCRIPT>");
            }
        } else {

            echo ("<SCRIPT LANGUAGE='JavaScript'>
                window.alert('Error connecting to database')
                </SCRIPT>");
        }

        mysqli_stmt_close($stmt);
        $conn->close();
    }

    ?>

</body>

</html>