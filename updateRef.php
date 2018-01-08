<?php
    include 'dbconnect.php';

    $refunitid = isset($_POST['refunitid']) ? $_POST['refunitid'] : null;
    $publicationid = isset($_POST['publicationid']) ? $_POST['publicationid'] : null;
    echo 'POST worked!'. refunitid.' - '. $refunitid;
    return '\$refunitid: $refunitid. - \$publicationid: $publicationid . <br>';
    $sql = "UPDATE refUnit_publication SET refUnitID='8' WHERE refUnitID='7' andpublicationID='3500';";
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
