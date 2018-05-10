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
    if ($_POST['submitProject'] == 'deleteProject') {
        $selectedProject = $_POST['projects'];
        $projectDetails = explode("|", $selectedProject);
        $projectID = $projectDetails[0];

        $sqlDeleteRefUnit_publication = "DELETE refUnit_publication
                                        FROM refUnit_publication
                                        INNER JOIN publication ON publication.publicationID = refUnit_publication.publicationID
                                        WHERE publication.publicationID = refUnit_publication.publicationID
                                        AND projectID = '$projectID';";

        $sqlDeletePublication = "DELETE FROM publication WHERE projectID = '$projectID';";

        $sqlDeleteMdxAuthor = "DELETE FROM mdxAuthor WHERE projectID = '$projectID';";

        $sqlDeleteProject = "DELETE FROM project WHERE projectID = '$projectID';";

        include 'dbconnect.php';
        $error = false;

        if ($conn->query($sqlDeleteRefUnit_publication) === TRUE) {         // delete refUnit_publication
            echo "refUnit_publication deleted successfully. <br>";

            if ($conn->query($sqlDeletePublication) === TRUE) {             // delete publication
                echo "publication deleted successfully. <br>";

                if ($conn->query($sqlDeleteMdxAuthor) === TRUE) {           // delete mdxAuthor
                    echo "mdxAuthor deleted successfully. <br>";

                    if ($conn->query($sqlDeleteProject) === TRUE) {         // delete project
                        echo "project deleted successfully. <br>";
                    } else {
                        echo "Error deleting 'project' record: " . $conn->error;
                        $error = true;
                    }
                } else {
                    echo "Error deleting 'mdxAuthor' record: " . $conn->error;
                    $error = true;
                }
            } else {
                echo "Error deleting 'publication' record: " . $conn->error;
                $error = true;
            }
        } else {
            echo "Error deleting 'refUnit_publication' record: " . $conn->error;
            $error = true;
        }

        $conn->close();

        if ($error) {
            echo "<script>
                alert(\"ERROR deleting Project '$projectDetails[1]'\");
              </script>";
        } else {
            echo "<script>
                alert(\"Project '$projectDetails[1]' deleted successfully!\");
                location.href = '/reftool/v2/';
              </script>";
        }

    } else if ($_POST['submitProject'] == 'continueProject') {
        $selectedProject = $_POST['projects'];
        $projectDetails = explode("|", $selectedProject);
        $_SESSION['projectDetails'] = $projectDetails;
        echo "<script>
                alert(\"Project '$projectDetails[1]' selected successfully. \\n\\nPress OK to proceed. \");
                location.href = '/reftool/v2/excelUpload.php';
              </script>";
    } else if ($_POST['submitProject'] == 'insertProject') {
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
                        location.href = '/reftool/v2/excelUpload.php';
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
                        echo '<option value="'. $row['projectID'].'|'.$row['projectName'].'|'.$row['description'].'">'
                            . $row['projectID'] . ': ' . $row['projectName']
                            . ' (' . $row['description'] . ') Scores: ' . $row['Astar'] . ', ' . $row['A'] . ', ' . $row['B'] . ', ' . $row['C']
                            . '</option>';
                    }
                } else {
                    echo '<option value="">No previous projects created</option>';
                }
                echo '</select>';
                $conn->close();
            ?>
        <button type="submit" name="submitProject" value="deleteProject" onclick="return confirm('\nDeleting the project will remove EVERYTHING related to it: \n - publications imported;\n - authors imported; \n - ERA/start ratings for these publications; \n - and so on... \n\n There is NO undo after the process is done! \n\n Are you sure you want to proceed?');">delete</button>
        <p><button type="submit" name="submitProject" value="continueProject">Work on this Project</button></p>
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
        <p><button type="submit" name="submitProject" value="insertProject">Start new project</button></p>
    </form>
    <hr>
</body>



</html>
