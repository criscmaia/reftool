<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if(!isset($_SESSION["projectDetails"]) && empty($_SESSION["projectDetails"])) {
    echo "<script>
            alert(\"You have to create or select a Project first.\");
            setTimeout(\"location.href = '/reftool/';\",1000);
          </script>";
    die();
} else {
    $projectDetails = $_SESSION["projectDetails"];
    echo "<strong>Project: $projectDetails[1] <a href='/reftool/' title='Change Project'>[x]</a></strong> | ";
//    print_r($projectDetails);
}
?>
<a href="/reftool/excelUpload.php">Upload Excel</a> |
<a href="/reftool/readExcel.php">Reading from Excel</a> |
<a href="/reftool/collectMdxPapers.php">Process Imported users</a> |
<a href="/reftool/fullList.php">Show full list</a> |
<a href="/reftool/publicationsPerAuthor.php">Publications per author</a> |
<a href="/reftool/refUnits.php">All ref units and the papers assigned to it.</a> |
<hr>
