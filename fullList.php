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

$publicationDetails = "";
$date = "";
$era = "";
$isPub = "";
$moreDetails = "";
$authors = "";
$refUnitDropdown = "";

    $ePrintIdTotal = "";
    $sql = "SELECT projectID, ePrintID, COUNT(ePrintID) as total FROM reftool.publication
            GROUP BY ePrintID, projectID
            ORDER BY ePrintID;";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $ePrintIdTotal[$row["ePrintID"]] = $row["total"];
        }
    } else {
        echo '<h2>0 results</h2>';
    }


    $sql = "SELECT publicationID, ePrintID, uri, title, abstract, date, eraRating, isPublished, presType, publication, publisher, eventTitle, author, firstName, lastName, email
            FROM publication, mdxAuthor
            where publication.author = mdxAuthor.mdxAuthorID
            and publication.projectID = $projectDetails[0]
            ORDER BY ePrintID;";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {                                                                                // if any results from query

        $currentEprintID;                                                                                       // initialise var
        $authorCounter = 0;
        $publicationDescDone = false;                                                                           // haven't printed publication details yet
        $author1id = '';
        $publicationID = '';
        while($row = $result->fetch_assoc()) {
            $nextEprintID = $row["ePrintID"];                                                                   // current row eprint id
            if (empty($currentEprintID) || $currentEprintID!=$nextEprintID) {                                   // is it a new publication id?
                $currentEprintID=$row["ePrintID"];                                                              // it is! what is the new id?

                // close AUTHOR columns AND prints everything from PREVIOUS publication ID
                $authors += "</td>";

                echo $publicationDetails;
                echo $date;
                echo $era;
                echo $isPub;
                echo $moreDetails;
                echo $authors;
                echo $refUnitDropdown;

                // clear all the variables to start collecting the details for the next ePrint
                $publicationDescDone = false;

                // clear variables
                $authorLoop = 1;
                $publicationDetails = "";
                $date = "";
                $era = "";
                $isPub = "";
                $moreDetails = "";
                $authors = "";
                $refUnitDropdown = "";
            } else {

                if ($authorLoop < 1) {              // first time that is getting this publication details
                    $firstAuthorId = $row["author"];

                    // column 1
                    $publicationDetails += '<td style="">';
                    $publicationDetails += '<a href="#">'.$currentEprintID.'</a> - '.$row["title"];
                    $publicationDetails += '<p class="ellipse"><strong>Abstract: </strong>'.$row["abstract"].'</p>';
                    $publicationDetails += '</td>';

                    // column 2
                    $date += '<td style="width:80px;">'.(!empty($row["date"]) ? $row["date"] : '').'</td>';

                    // column 3
                    $era += '<td>'.(!empty($row["eraRating"]) ? $row["eraRating"] : '').'</td>';

                    // column 4
                    $isPub += '<td>'.(!empty($row["isPublished"]) ? $row["isPublished"] : '').'<br>'.(!empty($row["presType"]) ? $row["presType"] : '').'</td>';

                    // column 5
                    $moreDetails += '<td>'.(!empty($row["publication"]) ? $row["publication"] : '').'<br>'.(!empty($row["publisher"]) ? $row["publisher"] : '').'</td>';

                    // column 6
                    $authors += '<td id="authors">';
                    $authors += (!empty($row["firstName"]) ? $row["firstName"] : '').' '.(!empty($row["lastName"]) ? $row["lastName"] : '').' ('.(!empty($row["email"]) ? $row["email"] : '').'); <br>';    // saves 1st author details

                    // create REF dropdown
                    $refUnitDropdown += '<td>';
                    getAssignedRef($projectDetails, $publicationID, $firstAuthorId);
                    $refUnitDropdown += '</td>';
                } else {
                    $authors += (!empty($row["firstName"]) ? $row["firstName"] : '').' '.(!empty($row["lastName"]) ? $row["lastName"] : '').' ('.(!empty($row["email"]) ? $row["email"] : '').'); <br>';    // saves 2nd-onwards author details
                }
                $authorLoop++;
            }
        }

//            $totalAuthors = $ePrintIdTotal[$currentEprintID];                                                   // how many authors to print
////            echo "how many authors to print: $totalAuthors <br>";
//            if (!$publicationDescDone) {                                                                        // if publication details for this id has been printed already
//                $author1id = $row["author"];                                                                    // get author 1 id to link to the REF in the last column
//                $publicationID = $row["publicationID"];                                                         // same as author above
//                echo '<tr>';
//                    echo '<td style="">';
//                        echo '<a href="#">'.$currentEprintID.'</a> - '.$row["title"];
//                        echo '<p class="ellipse"><strong>Abstract: </strong>'.$row["abstract"].'</p>';
//                    echo '</td>';
//                    echo '<td style="width:80px;">'.(!empty($row["date"]) ? $row["date"] : '').'</td>';
//                    echo '<td>'.(!empty($row["eraRating"]) ? $row["eraRating"] : '').'</td>';
//                    echo '<td>'.(!empty($row["isPublished"]) ? $row["isPublished"] : '').'<br>'.(!empty($row["presType"]) ? $row["presType"] : '').'</td>';
//                    echo '<td>'.(!empty($row["publication"]) ? $row["publication"] : '').'<br>'.(!empty($row["publisher"]) ? $row["publisher"] : '').'</td>';
//                    echo '<td id="authors">';
//                $publicationDescDone = true;
//            }
//
//            echo "authorCounter: $authorCounter, how many authors to print: $totalAuthors <br>";
//            if ($authorCounter < $totalAuthors) {                                                               // check if has printed all authors
//                echo (!empty($row["firstName"]) ? $row["firstName"] : '').' '.(!empty($row["lastName"]) ? $row["lastName"] : '').' ('.(!empty($row["email"]) ? $row["email"] : '').'); <br>';    // continue printing the authors
//            } else {
//                echo '</td>';                                                                                   // close the authors column
//                echo '<td>';                                                                                    // start REF column
//                echo getAssignedRef($projectDetails, $publicationID, $author1id);                               // get author 1 ref link
//                echo '</td>';
//                echo '</tr>';
//                $authorCounter=0;                                                                               // start counting author again for next paper
//            }
//            $authorCounter++;
//        }
    } else {
        echo '<h2>0 results</h2>';
    }

    echo '</tbody>';
echo '</table>';
$conn->close();


function getAssignedRef($projectDetails, $publicationID, $authorID) {
    include 'dbconnect.php';    // connect to DB

    $assignedRef = "SELECT refUnit.refUnitID, refUnit.name, publication.publicationID, publication.author
                    FROM refUnit, refUnit_publication, publication
                    WHERE refUnit.refUnitID = refUnit_publication.refUnitID
                    AND refUnit_publication.publicationID = publication.publicationID
                    AND publication.publicationID = $publicationID
                    AND publication.author = $authorID
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

function printRefOptions($assignedRef, $publicationID, $refUnitDropdown) {
    include 'dbconnect.php';
    $sql = "SELECT * FROM refUnit;";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $refUnitDropdown += '<select class="refOptions" name="refUnits">';
        $refUnitDropdown += '<option data-publicationid="'.$publicationID.'">No REF assigned</option>';
        while($row = $result->fetch_assoc()) {
            if ($row['refUnitID'] == $assignedRef) {
                $refUnitDropdown += '<option selected value="'. $row['refUnitID'] .'" data-refunitid="'.$row['refUnitID'].'" data-publicationid="'.$publicationID.'">' . $row['assignedID'] . ' - ' . $row['name'] . '</option>';
            } else {
                $refUnitDropdown += '<option value="'. $row['refUnitID'] .'" data-refunitid="'.$row['refUnitID'].'" data-publicationid="'.$publicationID.'">' . $row['assignedID'] . ' - ' . $row['name'] . '</option>';
            }
        }
    } else {
        $refUnitDropdown += '<option value="">No RefUnits found</option>';
    }
    $refUnitDropdown += '</select>';
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
