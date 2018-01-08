<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!-- datatable plugin -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" />
<script type="text/javascript" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<table id="refUnits">
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
                echo '<td>'.(!empty($row["assignedID"]) ? $row["assignedID"] : '').'</td>';
                echo '<td>'.(!empty($row["name"]) ? $row["name"] : '').'</td>';
                echo '<td>'.(!empty($row["title"]) ? $row["title"] : '').'</td>';
                echo '<td>'.(!empty($row["author"]) ? $row["author"] : '').'</td>';
                echo '<td>'.(!empty($row["eraRating"]) ? $row["eraRating"] : '').'</td>';
            echo '</tr>';
        }
    } else {
        echo '<h2>0 results</h2>';
    }
    $conn->close();
?>
    </tbody>
</table>
<script>
    $(document).ready(function() {
        $('#refUnits').DataTable({
            "dom": '<f',
            "autoWidth": true,
            "ordering": true,
            "paging": false,
            "searching": true,
            "info": true,
            responsive: true,
            stateSave: true
        });

    });

</script>
