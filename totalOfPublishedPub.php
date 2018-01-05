<div class="selectTable">
    <!--    List table    -->
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
                include 'dbconnect.php';    // connect to DB

                $sql = "SELECT COUNT(mdxAuthorID) as total, firstName, lastName, email, mdxAuthorID, currentEmployee
                        FROM reftool.publication, reftool.mdxAuthor
                        where publication.author = mdxAuthor.mdxAuthorID
                        GROUP BY mdxAuthorID
                        ORDER BY total DESC;";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td style='text-align: center;'><a href='#listAllPubs'>" . $row["total"] . "</a></td>";
                        echo "<td>" . $row["firstName"] . "</td>";
                        echo "<td>" . $row["lastName"] . "</td>";
                        echo "<td>" . $row["email"] . "</td>";
                        echo "<td>" .(($row["currentEmployee"]=='1')?'yes':""). "<br>";
                        echo "<td><a href=\"authorEdit.php?mdxAuthorID=$row[mdxAuthorID]\">Edit</a></td>";
                        echo "<td><a href=\"authorDelete.php?mdxAuthorID=$row[mdxAuthorID]\">Delete</a></td>";
                        echo "</tr>";


                    }
                } else {
                    echo "<h2>0 results</h2>";
                }

                $conn->close();
            ?>
    </table>
</div>
