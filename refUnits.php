<table id="publications">
    <thead>
        <tr style="text-align: left">
            <th>refUnit assigned ID</th>
            <th>refUnit Name</th>
            <th>Publication title</th>
            <th>Author name</th>
            <th>ERA Rating</th>
        </tr>
    </thead>
    <tbody>

        <?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'dbconnect.php';

    $ePrintIdTotal = "";
    $sql = "SELECT refUnit.assignedID, refUnit.name, publication.title, concat(mdxAuthor.firstName, ' ',mdxAuthor.lastName) as author, eraRating
            FROM refUnit_publication, refUnit, publication, mdxAuthor
            WHERE refUnit_publication.refUnitID = refUnit.refUnitID
            AND refUnit_publication.publicationID = publication.publicationID
            AND publication.author = mdxAuthor.mdxAuthorID;";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo '<tr>';
                echo '<td>'.(!empty($row["assignedID"]) ? $row["assignedID"] : 'null').'</td>';
                echo '<td>'.(!empty($row["name"]) ? $row["name"] : 'null').'</td>';
                echo '<td>'.(!empty($row["title"]) ? $row["title"] : 'null').'</td>';
                echo '<td>'.(!empty($row["author"]) ? $row["author"] : 'null').'</td>';
                echo '<td>'.(!empty($row["eraRating"]) ? $row["eraRating"] : 'null').'</td>';
            echo '</tr>';
        }
    } else {
        echo '<h2>0 results</h2>';
    }
    $conn->close();
?>
    </tbody>
</table>
