<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'dbconnect.php';

    $previousrefid = isset($_POST['previousrefid']) ? $_POST['previousrefid'] : null;
    $refunitid = isset($_POST['refunitid']) ? $_POST['refunitid'] : null;
    $publicationid = isset($_POST['publicationid']) ? $_POST['publicationid'] : null;


    if (!isset($refunitid) && isset($previousrefid)) {       // no refunit but had one before - chosen to remove REF
        $sql = "DELETE FROM refUnit_publication WHERE refUnitID=$previousrefid and publicationID=$publicationid;";
        $reponse = 'REF unit removed: '. $previousrefid. '. Publication id: '. $publicationid;
    }

    if (!isset($previousrefid) && isset($refunitid)) {      // no previous ref but selected one - no REF assigned
        $sql = "INSERT INTO refUnit_publication (refUnitID, publicationID) VALUES ($refunitid, $publicationid);";
        $reponse = 'REF unit added: ' . $refunitid.'. Publication id: '. $publicationid;
    }

    if (isset($previousrefid) && isset($refunitid)){         // it has previous but chose another one - update current REF
        $sql = "UPDATE refUnit_publication SET refUnitID=$refunitid WHERE refUnitID=$previousrefid and publicationID=$publicationid;";
        $reponse = 'REF unit changed:'. $previousrefid. '. New: ' . $refunitid.'. Publication id: '. $publicationid;
    }

    $result = $conn->query($sql);

    if ($result) {
        echo $response;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    $conn->close();
?>
