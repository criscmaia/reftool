<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'dbconnect.php';

    $authorid = isset($_POST['authorid']) ? $_POST['authorid'] : null;
    $sql = "SELECT title, date FROM publication WHERE author = ".$authorid." ORDER BY date DESC;";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
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
