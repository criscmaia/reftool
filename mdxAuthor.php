<h2>Search MDX author</h2>
<form method="post" action="<?=$_SERVER['PHP_SELF']?>#results" method="post">
    <p><label>First Name:     <input type="text" required maxlength=100 name="firstName"></label></p>
    <p><label>Last Name:      <input type="text" required maxlength=200 name="lastName"></label></p>
    <p><button type="submit" name="searchMdxAuthor">SEARCH FOR MDX AUTHOR</button></p>
</form>

<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        include 'dbconnect.php';

        // CHECK IF MDX AUTHOR ALREADY EXISTS
        $firstNameForm = mysqli_real_escape_string($conn,  $_POST['firstName']);
        $lastNameForm = mysqli_real_escape_string($conn,  $_POST['lastName']);

        if ($checkMdxAuthorExistence = $conn->query("SELECT * FROM mdxAuthor WHERE firstName = '$firstNameForm' AND lastName = '$lastNameForm';")) {
            $row_cnt = $checkMdxAuthorExistence->num_rows;
            echo "<p>Result set has $row_cnt row(s).</p>";

            // IF CAN'T FIND ANY AUTHOR
            if($row_cnt==0) {
                echo 'MDX author not found';
            } else {
                echo 'MDX author found';
            }

            /* close connection */
            $conn->close();
        }
    }
?>
