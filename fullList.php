<?php
include 'menu.php';
?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <!-- datatable plugin -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css" />
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>

    <style>
        /* Have the SEARCH button to the left, and the PRINT button on the right side */
        div.dt-buttons {
            float: right;
        }
        .dataTables_wrapper .dataTables_filter {
            float: left;
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

$publicationDetails = "";
$date = "";
$era = "";
$isPub = "";
$moreDetails = "";
$authors = "";
$refUnitDropdown = "";

    // get total amount of authors per publication
    $ePrintIdTotal = "";
    $sql = "SELECT projectID, ePrintID, COUNT(ePrintID) as total FROM reftool.publication
            GROUP BY ePrintID, projectID
            ORDER BY ePrintID;";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $ePrintIdTotal[$row["ePrintID"]] = $row["total"];
        }
    }

    // get all the publications + authors for the current Project
    $sql = "SELECT publicationID, ePrintID, uri, title, abstract, date, eraRating, isPublished, presType, publication, publisher, eventTitle, author, firstName, lastName, email
            FROM publication, mdxAuthor
            where publication.author = mdxAuthor.mdxAuthorID
            and publication.projectID = $projectDetails[0]
            ORDER BY ePrintID;";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {                                                                                // if any results from query
        $currentEprintID;
        $publicationID = '';
        $currentAuthor = 0;

        while($row = $result->fetch_assoc()) {
            $amountOfAuthors = $ePrintIdTotal[$row["ePrintID"]];                                                // total amount of authors for this specific eprint

            if (empty($currentEprintID) || $currentEprintID != $row["ePrintID"]) {                              // if first eprint id ever OR if new eprint
                $currentEprintID = $row["ePrintID"];                                                            // associate it with the current eprint

                $firstAuthorId = $row["author"];
                $publicationID = $row["publicationID"];

                // column 1
                $publicationDetails  = '<tr>';
                $publicationDetails .= '<td style="">';
                $publicationDetails .= '<a href="#">'.$currentEprintID.'</a> - '.$row["title"];
//                $publicationDetails .= '<p class="ellipse"><strong>Abstract: </strong>'.$row["abstract"].'</p>';
                $publicationDetails .= '</td>';

                // column 2
                $date = '<td style="width:80px;">'.(!empty($row["date"]) ? $row["date"] : '').'</td>';

                // column 3
                $era = '<td>'.(!empty($row["eraRating"]) ? $row["eraRating"] : '').'</td>';

                // column 4
                $isPub = '<td>'.(!empty($row["isPublished"]) ? $row["isPublished"] : '').'<br>'.(!empty($row["presType"]) ? $row["presType"] : '').'</td>';

                // column 5
                $moreDetails = '<td>'.(!empty($row["publication"]) ? $row["publication"] : '').'<br>'.(!empty($row["publisher"]) ? $row["publisher"] : '').'</td>';

                // column 6
                $authors = '<td id="authors">';
                $authors .= (!empty($row["firstName"]) ? $row["firstName"] : '').' '.(!empty($row["lastName"]) ? $row["lastName"] : '').' ('.(!empty($row["email"]) ? $row["email"] : '').'); <br>';    // saves 1st author details
                $currentAuthor++;

                // create REF dropdown
                $refUnitDropdown = '<td>';
                getAssignedRef($projectDetails, $publicationID, $firstAuthorId);
                $refUnitDropdown .= '</td>';
                $refUnitDropdown .= '</tr>';

            } else {                                                                                            //  if is the same eprint, keep saving authors data
                $authors .= (!empty($row["firstName"]) ? $row["firstName"] : '').' '.(!empty($row["lastName"]) ? $row["lastName"] : '').' ('.(!empty($row["email"]) ? $row["email"] : '').'); <br>';    // saves 2nd-onwards author details
                $currentAuthor++;

                if ($currentAuthor == $amountOfAuthors) {                                                       // that was the last author. print everything
                    $authors .= "</td>";

                    echo $publicationDetails;
                    echo $date;
                    echo $era;
                    echo $isPub;
                    echo $moreDetails;
                    echo $authors;
                    echo $refUnitDropdown;

                    $currentAuthor = 0;
                }
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
//            "dom": '<f',
            "dom": '<Bf>',
            "autoWidth": true,
            "ordering": true,
            "paging": false,
            "searching": true,
            "info": true,
            responsive: true,
            stateSave: true,
            buttons: [
                'print'
            ]
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
