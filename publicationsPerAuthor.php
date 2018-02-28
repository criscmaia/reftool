<?php
include 'menu.php';
?>
<style>
    .overlay {
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0, 0, 0, 0.7);
        transition: opacity 500ms;
        visibility: hidden;
        opacity: 0;
    }

    .overlay:target {
        visibility: visible;
        opacity: 1;
    }

    .popup {
        margin: 20px auto;
        padding: 20px;
        background: #fff;
        border-radius: 5px;
        width: 80%;
        position: relative;
        transition: all 5s ease-in-out;
    }

    .popup .close {
        position: absolute;
        top: 20px;
        right: 30px;
        transition: all 200ms;
        font-size: 30px;
        font-weight: bold;
        text-decoration: none;
        color: #333;
    }

    .popup .close:hover {
        color: #06D85F;
    }

    .popup .content {
        max-height: 30%;
        overflow: auto;
    }

    /* Have the SEARCH button to the left, and the PRINT button on the right side */
    div.dt-buttons {
        float: right;
    }
    .dataTables_wrapper .dataTables_filter {
        float: left;
    }

</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!-- datatable plugin -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" />
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css" />
<script type="text/javascript" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>

<table id="publications">
    <thead>
        <tr style="text-align: left">
            <th># of publications</th>
            <th>First name</th>
            <th>Last name</th>
            <th>email</th>
            <th>Current employee</th>
        </tr>
    </thead>
    <tbody>
    <?php
include 'dbconnect.php';

$sql = "SELECT COUNT(mdxAuthorID) as total, firstName, lastName, email, mdxAuthorID, currentEmployee
        FROM reftool.publication, reftool.mdxAuthor
        WHERE publication.author = mdxAuthor.mdxAuthorID
        AND publication.projectID = $projectDetails[0]
        GROUP BY mdxAuthorID
        ORDER BY total;";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td style="text-align: center;"><a href="#popUp" class="showPubs" data-mdxauthorid="'.$row["mdxAuthorID"].'">' . $row["total"] . '</a></td>';
        echo '<td>' . $row["firstName"] . '</td>';
        echo '<td>' . $row["lastName"] . '</td>';
        echo '<td>' . $row["email"] . '</td>';
        echo '<td>' .(($row["currentEmployee"]=="1")?"yes":""). '</td>';
        echo '</tr>';
    }
} else {
    echo '<h2>0 results</h2>';
}
$conn->close();
?>
<tbody>
</table>
<div id="popUp" class="overlay">
    <div class="popup">
        <h3>List of published papers by...</h3>
        <a class="close" href="#">&times;</a>
        <div id="content">
            <div id="tableResult"></div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#publications').DataTable({
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

        $(".showPubs").on('click', function() {
            $authorid = $(this).data("mdxauthorid");
            $.ajax({
                url: '/reftool/getAuthorPubs.php',
                type: 'post',
                data: {
                    authorid: $authorid
                },
                success: function(response) {
                    console.log(response);
                    $('#tableResult').html(response);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });
        });
    });

</script>
