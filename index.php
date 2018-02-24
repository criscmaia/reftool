<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags. -->
    <title>refTool</title>
</head>

<body>
    <h1>RefTool</h1>

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


 if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'dbconnect.php';    // connect to DB
    $projectName = $description = "";
    $Astar = "3.5";
    $A = "3";
    $B = "2.75";
    $C = "2.5";
    $projectName =  mysqli_real_escape_string($conn, $_POST["projectName"]);
    $description = mysqli_real_escape_string($conn, $_POST["description"]);
    $Astar = mysqli_real_escape_string($conn, $_POST["Astar"]);
    $A = mysqli_real_escape_string($conn, $_POST["A"]);
    $B = mysqli_real_escape_string($conn, $_POST["B"]);
    $C = mysqli_real_escape_string($conn, $_POST["C"]);

    $sql = "INSERT INTO project (projectName, description, Astar, A, B, C) VALUES ('$projectName', '$description', '$Astar', '$A', '$B', '$C')";
    $sql = "INSERT INTO `project` (`projectName`, `description`, `Astar`, `A`, `B`, `C`) VALUES ('$projectName', '$description', '$Astar', '$A', '$B', '$C')";

    if ($conn->query($sql) === TRUE) {
        echo "<p>New record created successfully</p>";
        header('Location: /reftool/excelUpload.php');
    } else {
        echo "<p>Error: " . $conn->error . "</p>";
    }
    $conn->close();
}

?>

    <form method="post" action="<?=$_SERVER['PHP_SELF']?>" method="post">
        <p><label>Project Name*: <input type="text" autofocus size="25" required maxlength="150" name="projectName"></label></p>
        <p><label>Description: <textarea type="text" cols="25" rows="2" maxlength="250" name="description"></textarea></p>
        <p><strong>Scores</strong>:</p>
        <p><label>A*: <input type="number" step="any" required name="Astar" value="3.5"></label></p>
        <p><label>A: <input type="number" step="any" required name="A" value="3"></label></p>
        <p><label>B: <input type="number" step="any" required name="B" value="2.75"></label></p>
        <p><label>C: <input type="number" step="any" required name="C" value="2.5"></label></p>
        <p><button type="submit" name="insertProject">Insert project</button></p>
    </form>
</body>



</html>
