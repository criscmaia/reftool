<p><label>REF Unit:
    <select name="refUnitID">
    <?php
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
            echo "<option value=\"0\">No REF Unit assigned</option>";
            while($rowAssignedRef = $resultAssignedRef->fetch_assoc()) {
                echo "<option value=\"". $rowAssignedRef['refUnitID'] ."\">" . $rowAssignedRef['assignedID'] . " - " . $rowAssignedRef['name'] . "</option>";
            }
        } else {
            echo "<option value=\"\">No REF Units registered</option>";
        }
        $conn->close();
    ?>
    </select>
</label></p>
