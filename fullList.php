<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
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


    .ellipse {
        width: 400px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        margin: 0;
        padding: 0;
    }

    .ellipse:hover {
        /*        padding: 2px;*/
        white-space: normal;
        word-break: break-word;
        z-index: 5;
    }

</style>
<p id="notification">Notifications here</p>
<table id="publications">
    <thead>
        <tr style="text-align: left">
            <th>Publication details</th>
            <th>Date</th>
            <th>ERA</th>
            <th>isPub presType</th>
            <th>More details</th>
            <th>Authors</th>
            <th>REF Unit</th>
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
    $sql = "SELECT publicationID, ePrintID, uri, title, abstract, date, eraRating, isPublished, presType, publication, publisher, eventTitle, author, firstName, lastName, email
            FROM publication, mdxAuthor
            where publication.author = mdxAuthor.mdxAuthorID
            ORDER BY ePrintID;";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {                                                                                // if any results from query
        $currentEprintID;                                                                                       // initialise var
        $authorCounter = 1;
        $publicationDescDone = false;                                                                           // haven't printed publication details yet
        while($row = $result->fetch_assoc()) {
            $nextEprintID = $row["ePrintID"];                                                                   // current row eprint id
            if (empty($currentEprintID) || $currentEprintID!=$nextEprintID) {                                   // is it a new publication id?
                $currentEprintID=$row["ePrintID"];                                                              // it is! what is the new id?
                $publicationDescDone = false;
                $authorCounter = 1;
            }

            $totalAuthors = $ePrintIdTotal[$currentEprintID];                                                   // how many authors to print
            $rowspan = $ePrintIdTotal[$currentEprintID]+1;                                                      // description + amount of authors

            if (!$publicationDescDone) {                                                                        // if publication details for this id has been printed already
                echo '<tr>';
                    echo '<td rowspan="'.$rowspan.'" style="">';
                        echo '<a href="#">'.$currentEprintID.'</a> - '.$row["title"];
                    echo '<ul><li class="ellipse"><strong>Abstract: </strong>'.$row["abstract"].'</li></ul>';
                    echo '</td>';
                    echo '<td rowspan="'.$rowspan.'" style="width:80px;">'.(!empty($row["date"]) ? $row["date"] : '').'</td>';
                    echo '<td rowspan="'.$rowspan.'">'.(!empty($row["eraRating"]) ? $row["eraRating"] : '').'</td>';
                    echo '<td rowspan="'.$rowspan.'">'.(!empty($row["isPublished"]) ? $row["isPublished"] : '').'<br>'.(!empty($row["presType"]) ? $row["presType"] : '').'</td>';
                    echo '<td rowspan="'.$rowspan.'">'.(!empty($row["publication"]) ? $row["publication"] : '').'<br>'.(!empty($row["publisher"]) ? $row["publisher"] : '').'</td>';
                echo '</tr>';
                $publicationDescDone = true;
            }

            if ($authorCounter <= $totalAuthors) {                                                           // check if has printed all authors
                echo '<tr><td>'.(!empty($row["firstName"]) ? $row["firstName"] : '').' '.(!empty($row["lastName"]) ? $row["lastName"] : '').'<br>'.(!empty($row["email"]) ? $row["email"] : '').'</td>';    // continue printing the authors
                echo '<td>';
                    getAssignedRef($row["publicationID"], $row["author"]);
                echo '</td>';
                echo '</tr>';
                $authorCounter++;
            }
        }
    } else {
        echo '<h2>0 results</h2>';
    }
    $conn->close();

function getAssignedRef($publicationID, $authorID) {
    include 'dbconnect.php';    // connect to DB

    $assignedRef = "SELECT refUnit.refUnitID, refUnit.name, publication.publicationID, publication.author
                    FROM refUnit, refUnit_publication, publication
                    WHERE refUnit.refUnitID = refUnit_publication.refUnitID
                    AND refUnit_publication.publicationID = publication.publicationID
                    AND publication.publicationID = $publicationID
                    AND publication.author = $authorID;";
//    echo $assignedRef . "<br>";
    $resultAssignedRef = $conn->query($assignedRef);
    if ($resultAssignedRef->num_rows > 0) {
        while($rowAssignedRef = $resultAssignedRef->fetch_assoc()) {
            $assignedRef = $rowAssignedRef['refUnitID'];
//            echo "\$assignedRef: $assignedRef <br>";
        }
    } else {
//        echo "No REF Units registered";
        $assignedRef = 0;
    }
    $conn->close();
    printRefOptions($assignedRef, $publicationID);
}

function printRefOptions($assignedRef, $publicationID) {
//    echo "\$assignedRef: $assignedRef <br>";
    include 'dbconnect.php';
    $sql = "SELECT * FROM refUnit;";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        echo '<select class="refOptions" name="refUnits">';
        echo '<option value="0">No REF assigned</option>';
        while($row = $result->fetch_assoc()) {
            if ($row['refUnitID'] == $assignedRef) {
                echo '<option selected value="'. $row['refUnitID'] .'" data-refunitid="'.$row['refUnitID'].'" data-publicationid="'.$publicationID.'">' . $row['assignedID'] . ' - ' . $row['name'] . '</option>';
            } else {
                echo '<option value="'. $row['refUnitID'] .'" data-refunitid="'.$row['refUnitID'].'" data-publicationid="'.$publicationID.'">' . $row['assignedID'] . ' - ' . $row['name'] . '</option>';
            }
        }
    } else {
        echo '<option value="">No RefUnits found</option>';
    }
    echo '</select>';
    $conn->close();
}
?>
    </tbody>
</table>
<script>
    $(document).ready(function() {
        $(".refOptions").on('change', function() {
            $('#notification').text("Changing REF...");
            $refunitid = $(this).find(':selected').data('refunitid');
            $publicationid = $(this).find(':selected').data('publicationid');
            alert ($refunitid + " - " + $publicationid);
            $.ajax({
                url: '/reftool/updateRef.php',
                type: 'post',
                data: {
                    refunitid: $refunitid,
                    publicationid: $publicationid
                },
                success: function(response) {
                    $('#notification').text(response);
//                    alert(response);
//                    console.log(response);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $('#notification').text(textStatus, errorThrown);
                    console.log(textStatus, errorThrown);
                }
            });
        });
    });

</script>
