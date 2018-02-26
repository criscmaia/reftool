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


session_start();    // must have as it doesn't have the menu to start automatically

// log out from previous project
if(isset($_SESSION["projectDetails"]) && !empty($_SESSION["projectDetails"])) {
    unset($_SESSION['projectDetails']);
}

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

     // only inserts if Project Name AND Description is not already in the DB
     $sql = "INSERT INTO `project` (`projectName`, `description`, `Astar`, `A`, `B`, `C`)
            SELECT '$projectName', '$description', '$Astar', '$A', '$B', '$C' FROM `project`
            WHERE NOT EXISTS (SELECT * FROM `project`
                  WHERE projectName='$projectName' AND description='$description')
            LIMIT 1 ";

    if ($conn->query($sql) === TRUE) {
        if ($conn->affected_rows>0) {                   // if INSERT worked
            $insertedId = $conn->insert_id;
            $projectDetails = array($insertedId, $projectName, $description);
            $_SESSION['projectDetails'] = $projectDetails;
            echo "<script>
                    alert(\"Project '$projectName' created successfully. \\n\\nPress OK to proceed. \");
                    location.href = '/reftool/excelUpload.php';
                  </script>";
        } else {                                        // if there is already a project with same name AND description
            echo "<script>
                    alert(\"Project '$projectName' already exists with the same description. \\n\\nChange the fields or select the project name from the list. \");
                  </script>";
        }
    } else {
        echo "<p>Error: " . $conn->error . "</p>";
    }
    $conn->close();
}

?>

<!--
  TODO:
  option to select previous one or start new
  update Activity Diagram
  -->

   <hr>
   <h2>Continue with a previous project: </h2>
   <form method="post" action="<?=$_SERVER['PHP_SELF']?>" method="post">
            <?php
                include 'dbconnect.php';
                $sql = "SELECT * FROM project;";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    echo '<select class="projectOptions" name="projects">';
                    echo '<option selected>No Project selected</option>';
                    while($row = $result->fetch_assoc()) {
                        echo '<option value="'. $row['projectID'] .'" data-projectID="'.$row['projectID'].'">'
                            . $row['projectID'] . ': ' . $row['projectName']
                            . ' (' . $row['description'] . ') Scores: ' . $row['Astar'] . ', ' . $row['A'] . ', ' . $row['B'] . ', ' . $row['C'] . '</option>';
                    }
                } else {
                    echo '<option value="">No previous projects created</option>';
                }
                echo '</select></td>';
                $conn->close();
            ?>
        <p><button type="submit" name="insertProject">Work on this Project</button></p>
   </form>

   <hr>

   <h2>Create a new project: </h2>
    <form method="post" action="<?=$_SERVER['PHP_SELF']?>" method="post">
        <p><label>Project Name*: <input type="text" autofocus size="25" required maxlength="150" name="projectName"></label></p>
        <p><label>Description: <textarea type="text" cols="25" rows="2" maxlength="250" name="description"></textarea></p>
        <p><strong>Scores</strong> (can be changed later):</p>
        <p><label>A*: <input type="number" step="any" required name="Astar" value="3.5"></label></p>
        <p><label>A: <input type="number" step="any" required name="A" value="3"></label></p>
        <p><label>B: <input type="number" step="any" required name="B" value="2.75"></label></p>
        <p><label>C: <input type="number" step="any" required name="C" value="2.5"></label></p>
        <p><button type="submit" name="insertProject">Start new project</button></p>
    </form>
    <hr>
</body>



</html>
