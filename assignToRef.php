function getAssignedRef($publicationID, $authorID) {
    include 'dbconnect.php';    // connect to DB

    $publicationID = 3500;
    $authorID = 2060;
    $assignedRef = "SELECT refUnit.refUnitID, refUnit.name, publication.publicationID, publication.author
                    FROM refUnit, refUnit_publication, publication
                    WHERE refUnit.refUnitID = refUnit_publication.refUnitID
                    AND refUnit_publication.publicationID = publication.publicationID
                    AND publication.publicationID = $publicationID
                    AND publication.author = $authorID;";
    $resultAssignedRef = $conn->query($assignedRef);
    if ($resultAssignedRef->num_rows > 0) {
        while($rowAssignedRef = $resultAssignedRef->fetch_assoc()) {
            $assignedRef = $rowAssignedRef['refUnitID'];
        }
    } else {
//        echo "No REF Units registered";
        $assignedRef = 0;
    }
    $conn->close();
    printRefOptions($assignedRef);
}

function printRefOptions($assignedRef) {
    include 'dbconnect.php';
    $sql = "SELECT * FROM refUnit;";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        echo '<select name="refUnits">';
        echo '<option value="0">No REF assigned</option>';
        while($row = $result->fetch_assoc()) {
            if ($row['refUnitID'] == $assignedRef) {
                echo "<option selected value=\"". $row['refUnitID'] ."\">" . $row['assignedID'] . " - " . $row['name'] . "</option>";
            } else {
                echo "<option value=\"". $row['refUnitID'] ."\">" . $row['assignedID'] . " - " . $row['name'] . "</option>";
            }
        }
    } else {
        echo "<option value=\"\">No RefUnits found</option>";
    }
    echo '</select>';
    $conn->close();
}
