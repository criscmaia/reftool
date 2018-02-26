<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'dbconnect.php';

session_start();
$projectDetails = $_SESSION["projectDetails"];

$authorid = isset($_POST['authorid']) ? $_POST['authorid'] : 'NULL';
$sql = "SELECT title, date FROM publication
        WHERE author = $authorid
        AND publication.projectID = $projectDetails[0]
        ORDER BY date DESC;";
$result = $conn->query($sql);

if (!$result) {
    trigger_error('Invalid query: ' . $conn->error);
} else if ($result->num_rows > 0) {
    echo '<table>
            <tr style="text-align: left">
                <th>Title</th>
                <th>Publication</th>
            </tr>';
    while($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . $row["title"] . '</td>';
        echo '<td>' . $row["date"] . '</td>';
        echo '</tr>';
    }
    echo '</table>
    </div>';
} else {
    echo '<h2>0 results</h2>';
}
$conn->close();
?>
