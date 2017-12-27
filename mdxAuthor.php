<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <!-- † -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags. -->
    <title>refTool</title>
    <!--    <link rel="stylesheet" href="style.css">-->
</head>

<body>
    <h1>reftool</h1>
    <form method="post" action="<?=$_SERVER['PHP_SELF']?>#results" method="post">
        <p><label>First Name:     <input type="text" required maxlength=100 name="firstName"></label></p>
        <p><label>Last Name:      <input type="text" required maxlength=200 name="lastName"></label></p>
        <p><button type="submit" name="searchMdxAuthor">SEARCH FOR MDX AUTHOR</button></p>
    </form>


    <?php
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        include 'dbconnect.php';


//        if ($_SERVER["REQUEST_METHOD"] == "POST") {
//            include 'dbconnect.php';
//
//            // CHECK IF USER ALREADY EXISTS
//            if ($checkMdxAuthorExistence = $checkMdxAuthorExistenceConn->query("SELECT * FROM mdxAuthor WHERE firstName = '".mysqli_real_escape_string($conn,  $_POST['firstName'])."' AND lastName = '".mysqli_real_escape_string($conn,  $_POST['lastName'])."';")) {
//                /* determine number of rows result set */
//                $row_cnt = $checkMdxAuthorExistence->num_rows;
//                echo "<p>Result set has $row_cnt rows.</p>";            // WORKS
//
//                // IF CAN'T FIND THE STUDENT ON THE USER TABLE
//                if($row_cnt==0) {
//                    echo 'MDX author not found';
//                } else {
//                    echo 'MDX author found';
//                }
//
//                /* close result set */
//                $checkMdxAuthorExistence->close();
//            }
    ?>

</body>

</html>
