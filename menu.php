<div class="menu">
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if(!isset($_SESSION["projectDetails"]) && empty($_SESSION["projectDetails"])) {
    echo "<script>
            alert(\"You have to create or select a Project first.\");
            setTimeout(\"location.href = '/reftool/v2/';\",1000);
          </script>";
    die();
} else {
    $projectDetails = $_SESSION["projectDetails"];
    echo "<strong>Project: <abbr title=\"$projectDetails[2]\">($projectDetails[0]) $projectDetails[1]</abbr> <a href='/reftool/v2/' title='Change Project'>[x]</a></strong> | ";
//    print_r($projectDetails);
}
?>
<a href="/reftool/v2/excelUpload.php">Upload Excel</a> |
<a href="/reftool/v2/readExcel.php">Reading from Excel</a> |
<a href="/reftool/v2/collectMdxPapers.php">Process Imported users</a> |
<a href="/reftool/v2/fullList.php">Show full list</a> |
<a href="/reftool/v2/publicationsPerAuthor.php">Publications per author</a> |
<a href="/reftool/v2/refUnits.php">All ref units and the papers assigned to it.</a> |
</div>
<hr>
