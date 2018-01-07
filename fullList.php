<style>
    #publications {
        font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    #publications td,
    #publications th {
        border: 1px solid #ddd;
        padding: 8px;
    }

    #publications tr:hover {
        background-color: #ddd;
    }

    #publications th {
        padding-top: 12px;
        padding-bottom: 12px;
        text-align: left;
        background-color: #4CAF50;
        color: white;
    }

/*
    .ellipse {
        width: 400px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        margin: 0;
        padding: 0;
    }
*/

    .ellipse:hover {
        padding: 2px;
        white-space: normal;
        word-break: break-word;
        z-index: 5;
    }

</style>
<table id="publications">
    <thead>
        <tr style="text-align: left">
            <th>Publication details</th>
            <th>Date</th>
            <th>ERA</th>
            <th>isPub presType</th>
            <th>More details</th>
            <th>Authors</th>
        </tr>
    </thead>
    <tbody>
        <?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'dbconnect.php';

    $ePrintIdTotal = "";
    $sql = "SELECT ePrintID, COUNT(ePrintID) as total FROM reftool.publication
            GROUP BY ePrintID
            ORDER BY ePrintID;";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $ePrintIdTotal[$row["ePrintID"]] = $row["total"];
        }
    } else {
        echo '<h2>0 results</h2>';
    }
//    echo '<pre>'; print_r($ePrintIdTotal); echo '</pre>';

    // ------------------------
    $sql = "SELECT ePrintID, uri, title, abstract, date, eraRating, isPublished, presType, publication, publisher, eventTitle, author, firstName, lastName, email
            FROM publication, mdxAuthor
            where publication.author = mdxAuthor.mdxAuthorID
            ORDER BY ePrintID;";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {                                                                                // if any resulst from query
        $currentEprintID;                                                                                       // initialise var
        $counter = 1;
        while($row = $result->fetch_assoc()) {
            $nextEprintID = $row["ePrintID"];                                                                   // current row eprint id
            if (empty($currentEprintID) || $currentEprintID==$nextEprintID) {                                   // still on same publication id, keep printing
                $currentEprintID=$row["ePrintID"];
                $totalAuthors = $ePrintIdTotal[$currentEprintID];                                               // how many authors to print
                if ($totalAuthors==1) {
                    echo '<tr>';
                        echo '\$ePrintIdTotal[$currentEprintID]: $ePrintIdTotal[$currentEprintID]';
                        echo '<td rowspan="'.$totalAuthors.'">';
                            echo '<a href="#">'.$currentEprintID.'</a> - '.$row["title"];
                        echo '<ul><li class="ellipse"><strong>Abstract: </strong>'.$row["abstract"].'</li></ul>';
                        echo '</td>';
                        echo '<td rowspan="'.$totalAuthors.'">'.(empty($row["date"]) ? $row["date"] : '').'</td>';
                        echo '<td rowspan="'.$totalAuthors.'">'.(empty($row["eraRating"]) ? $row["eraRating"] : '').'</td>';
                        echo '<td rowspan="'.$totalAuthors.'">'.(empty($row["isPublished"]) ? $row["isPublished"] : '').'<br>'.(empty($row["presType"]) ? $row["presType"] : '').'</td>';
                        echo '<td rowspan="'.$totalAuthors.'">'.(empty($row["publication"]) ? $row["publication"] : '').'<br>'.(empty($row["publisher"]) ? $row["publisher"] : '').'</td>';
                    echo '</tr>';
                    echo '<tr><td>'.$row["firstName"].' '.$row["lastName"].'<br>'.$row["email"].'</td></tr>';
                }
                if ($counter <= $totalAuthors) {                                                                // check if has printed all authors
                    if ($counter==1) {                                                                          // if first time printing, print pub details
                        echo '<tr>';
                            echo '<td rowspan="'.$totalAuthors.'">';
                                echo '<a href="#">'.$currentEprintID.'</a> - '.$row["title"];
                            echo '<ul><li class="ellipse"><strong>Abstract: </strong>'.$row["abstract"].'</li></ul>';
                            echo '</td>';
                            echo '<td rowspan="'.$totalAuthors.'">'.(empty($row["date"]) ? $row["date"] : '').'</td>';
                            echo '<td rowspan="'.$totalAuthors.'">'.(empty($row["eraRating"]) ? $row["eraRating"] : '').'</td>';
                            echo '<td rowspan="'.$totalAuthors.'">'.(empty($row["isPublished"]) ? $row["isPublished"] : '').'<br>'.(empty($row["presType"]) ? $row["presType"] : '').'</td>';
                            echo '<td rowspan="'.$totalAuthors.'">'.(empty($row["publication"]) ? $row["publication"] : '').'<br>'.(empty($row["publisher"]) ? $row["publisher"] : '').'</td>';
                        echo '</tr>';
                    } else {
                        echo '<tr><td>'.$row["firstName"].' '.$row["lastName"].'<br>'.$row["email"].'</td></tr>';   // continue printing the authors
                    }
                    $counter++;
                }
            }
            else {                                                                                            // if new pub eprint id
                $currentEprintID=$row["ePrintID"];
                $counter = 1;
            }
        }
    } else {
        echo '<h2>0 results</h2>';
    }
    $conn->close();
?>
    </tbody>
</table>
