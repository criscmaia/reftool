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
        margin: 70px auto;
        padding: 20px;
        background: #fff;
        border-radius: 5px;
        width: 30%;
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

</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<div class="selectTable">
    <h1>List of authors with total amount of publications since 2014</h1>
    <table>
        <tr style="text-align: left;">
            <th># of publications</th>
            <th>First name</th>
            <th>Last name</th>
            <th>email</th>
            <th>Current employee</th>
            <th>Edit</th>
            <th>Delete</th>
        </tr>
        <?php
    include 'dbconnect.php';

    $sql = "SELECT COUNT(mdxAuthorID) as total, firstName, lastName, email, mdxAuthorID, currentEmployee
            FROM reftool.publication, reftool.mdxAuthor
            where publication.author = mdxAuthor.mdxAuthorID
            GROUP BY mdxAuthorID
            ORDER BY total DESC;";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td style="text-align: center;"><a href="#popUp" id="showPubs" data-mdxauthorid='.$row[mdxAuthorID].'>' . $row["total"] . '</a></td>';
            echo '<td>' . $row["firstName"] . '</td>';
            echo '<td>' . $row["lastName"] . '</td>';
            echo '<td>' . $row["email"] . '</td>';
            echo '<td>' .(($row["currentEmployee"]=="1")?"yes":""). '<br>';
            echo '<td><a href="#">Edit</a></td>';
            echo '<td><a href="#">Delete</a></td>';
            echo '</tr>';
        }
    } else {
        echo '<h2>0 results</h2>';
    }
    $conn->close();
?>
    </table>
</div>
<div id="popUp" class="overlay">
    <div class="popup">
        <h2>List of published papers by...</h2>
        <a class="close" href="#">&times;</a>
        <div id="content">
            Put table here
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $("#showPubs").on('click', function() {
            $authorid = $(this).data("mdxauthorid");
            $('#content').text('authorid : ' + $authorid);

            //            $.ajax({
            //                url: 'ajaxfile.php',
            //                type: 'post',
            //                data: {
            //                    mdxAuthorID: value
            //                },
            //                success: function(response) {
            //                    echo 'it works! <br>';
            //                }
            //            });
        });
    });

</script>
