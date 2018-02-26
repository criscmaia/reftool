<?php
include 'menu.php';
?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <!-- datatable plugin -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" />
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>

    <style>
        /* START - show only the begin of the abstract until you hover it */

        .ellipse {
            width: 400px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            margin: 0;
            padding: 0;
            font-size: 90%;
        }

        .ellipse:hover {
            /*        padding: 2px;*/
            white-space: normal;
            word-break: break-word;
            z-index: 5;
        }

        /* END - show only the begin of the abstract until you hover it */

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

$authorLoop = 1;
$publicationDetails = "";
$date = "";
$era = "";
$isPub = "";
$moreDetails = "";
$authors = "";
$refUnitDropdown = "";

    // get total amount of authors per publication
    $ePrintIdTotalAuthors = array();
    $sql = "SELECT projectID, ePrintID, COUNT(ePrintID) as total FROM reftool.publication
            GROUP BY ePrintID, projectID
            ORDER BY ePrintID;";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            array_push($ePrintIdTotalAuthors, $row["total"]);
        }
    } else {
//        echo '<h2>0 results</h2>';
    }


    echo "<pre>";
//    var_dump(array_values($ePrintIdTotalAuthors));
    var_dump($ePrintIdTotalAuthors);
    echo "</pre>";


    $sql = "SELECT publicationID, ePrintID, uri, title, abstract, date, eraRating, isPublished, presType, publication, publisher, eventTitle, author, firstName, lastName, email
            FROM publication, mdxAuthor
            where publication.author = mdxAuthor.mdxAuthorID
            and publication.projectID = $projectDetails[0]
            ORDER BY ePrintID;";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {                                                                                // if any results from query

        for ($i = 0; $i <= count($ePrintIdTotalAuthors); $i++) {
            echo "i: $i <br>";
            for ($j = 0; $j <= $ePrintIdTotalAuthors[$i]; $j++) {
                echo "j: $j <br>";
            }
        }

        $currentEprintID;                                                                                       // initialise var
        $publicationID = '';
        while($row = $result->fetch_assoc()) {
//            echo "<pre>";
//            var_dump($ePrintIdTotalAuthors[$row["ePrintID"]][0]);
//            echo "</pre>";


            if (empty($currentEprintID)) {                                                                      // if first eprint id
                $currentEprintID = $row["ePrintID"];                                                            // needs to set up as current
            }
            // echo "currentEprintID: $currentEprintID <br>";

            $nextEprintID = $row["ePrintID"];                                                                   // current row eprint id
            // echo "nextEprintID: $nextEprintID <br>";

            if (empty($currentEprintID) || $currentEprintID!=$nextEprintID) {                                   // is it a new publication id?
                // echo "it's a new publication <br>";

                $currentEprintID=$row["ePrintID"];                                                              // it is! what is the new id?
                // echo "currentEprintID: $currentEprintID <br>";
            } else {
                // echo "still printing the same publication <br>";
                if ($authorLoop == 1) {                                                                         // first time that is getting this publication details
                    // echo "first time that is getting this publication details <br>";
                    $firstAuthorId = $row["author"];
                    $publicationID = $row["publicationID"];

                    // column 1
                    $publicationDetails .= '<td style="">';
                    $publicationDetails .= '<a href="#">'.$currentEprintID.'</a> - '.$row["title"];
                    $publicationDetails .= '<p class="ellipse"><strong>Abstract: </strong>'.$row["abstract"].'</p>';
                    $publicationDetails .= '</td>';
                    // echo "<script>console.log('".$publicationDetails."');</script>";

                    // column 2
                    $date .= '<td style="width:80px;">'.(!empty($row["date"]) ? $row["date"] : '').'</td>';

                    // column 3
                    $era .= '<td>'.(!empty($row["eraRating"]) ? $row["eraRating"] : '').'</td>';

                    // column 4
                    $isPub .= '<td>'.(!empty($row["isPublished"]) ? $row["isPublished"] : '').'<br>'.(!empty($row["presType"]) ? $row["presType"] : '').'</td>';

                    // column 5
                    $moreDetails .= '<td>'.(!empty($row["publication"]) ? $row["publication"] : '').'<br>'.(!empty($row["publisher"]) ? $row["publisher"] : '').'</td>';

                    // column 6
                    $authors .= '<td id="authors">';
                    $authors .= (!empty($row["firstName"]) ? $row["firstName"] : '').' '.(!empty($row["lastName"]) ? $row["lastName"] : '').' ('.(!empty($row["email"]) ? $row["email"] : '').'); <br>';    // saves 1st author details

                    // create REF dropdown
                    $refUnitDropdown .= '<td>';
                    getAssignedRef($projectDetails, $publicationID, $firstAuthorId);
                    $refUnitDropdown .= '</td>';
                } else {
                    // echo "getting the other authors details <br>";
                    $authors .= (!empty($row["firstName"]) ? $row["firstName"] : '').' '.(!empty($row["lastName"]) ? $row["lastName"] : '').' ('.(!empty($row["email"]) ? $row["email"] : '').'); <br>';    // saves 2nd-onwards author details
                }
                $authorLoop++;

//                if ($authorLoop==$ePrintIdTotalAuthors[$row["ePrintID"]]) {
//                    $authors .= "</td>";                                                                            // close AUTHOR columns AND prints everything from PREVIOUS publication ID
//
//                    echo $publicationDetails;
//                    echo $date;
//                    echo $era;
//                    echo $isPub;
//                    echo $moreDetails;
//                    echo $authors;
//                    echo $refUnitDropdown;
//
//
//                    // echo "<script>alert(".$publicationDetails.")</script>";
//                    // echo "finish printing one publication <br>";
//                    // echo "<hr>";
//
//                    // clear variables
//                    $authorLoop = 1;
//                    $publicationDetails = "";
//                    $date = "";
//                    $era = "";
//                    $isPub = "";
//                    $moreDetails = "";
//                    $authors = "";
//                    $refUnitDropdown = "";
//
//                    // echo "all variables are now clear <br>";
//                }
            }
        }
    } else {
        echo '<h2>0 results</h2>';
    }

    echo '</tbody>';
echo '</table>';
$conn->close();


function getAssignedRef($projectDetails, $publicationID, $firstAuthorId) {
    include 'dbconnect.php';    // connect to DB

    $assignedRef = "SELECT refUnit.refUnitID, refUnit.name, publication.publicationID, publication.author
                    FROM refUnit, refUnit_publication, publication
                    WHERE refUnit.refUnitID = refUnit_publication.refUnitID
                    AND refUnit_publication.publicationID = publication.publicationID
                    AND publication.publicationID = $publicationID
                    AND publication.author = $firstAuthorId
                    AND publication.projectID = $projectDetails[0];";
    $resultAssignedRef = $conn->query($assignedRef);
    if ($resultAssignedRef->num_rows > 0) {
        while($rowAssignedRef = $resultAssignedRef->fetch_assoc()) {
            $assignedRef = $rowAssignedRef['refUnitID'];
        }
    } else {
        $assignedRef = 0;
    }
    $conn->close();
    echo printRefOptions($assignedRef, $publicationID);
}

function printRefOptions($assignedRef, $publicationID) {
    include 'dbconnect.php';
    global $refUnitDropdown;
    $sql = "SELECT * FROM refUnit;";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $refUnitDropdown .= '<select class="refOptions" name="refUnits">';
        $refUnitDropdown .= '<option data-publicationid="'.$publicationID.'">No REF assigned</option>';
        while($row = $result->fetch_assoc()) {
            if ($row['refUnitID'] == $assignedRef) {
                $refUnitDropdown .= '<option selected value="'. $row['refUnitID'] .'" data-refunitid="'.$row['refUnitID'].'" data-publicationid="'.$publicationID.'">' . $row['assignedID'] . ' - ' . $row['name'] . '</option>';
            } else {
                $refUnitDropdown .= '<option value="'. $row['refUnitID'] .'" data-refunitid="'.$row['refUnitID'].'" data-publicationid="'.$publicationID.'">' . $row['assignedID'] . ' - ' . $row['name'] . '</option>';
            }
        }
    } else {
        $refUnitDropdown .= '<option value="">No RefUnits found</option>';
    }
    $refUnitDropdown .= '</select>';
    $conn->close();
}
?>

<script>
    $(document).ready(function() {
        $('#publications').DataTable({
            "dom": '<f',
            "autoWidth": true,
            "ordering": true,
            "paging": false,
            "searching": true,
            "info": true,
            responsive: true,
            stateSave: true
        });


        $(".refOptions").on('focus', function() {
            $previousrefid = $(this).find(':selected').data('refunitid'); // previous selected REF
        }).change(function() {
            $('#notification').text("Changing REF...");
            $refunitid = $(this).find(':selected').data('refunitid');
            $publicationid = $(this).find(':selected').data('publicationid');

            $(".refOptions").blur();
            $.ajax({
                url: '/reftool/updateRef.php',
                type: 'post',
                data: {
                    previousrefid: $previousrefid,
                    refunitid: $refunitid,
                    publicationid: $publicationid
                },
                success: function(response) {
                    $('#notification').html(response);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $('#notification').html(textStatus, errorThrown);
                    console.log(textStatus, errorThrown);
                }
            });
        });
    });

</script>
