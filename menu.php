<style>
/*
    TEMPORARY
    STYLE FOR
    THE TABLE
*/

#importedList {
    font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
    border-collapse: collapse;
    width: 100%;
}

#importedList td, #importedList th {
    border: 1px solid #ddd;
    padding: 8px;
}

#importedList tr:nth-child(even){background-color: #f2f2f2;}

#importedList tr:hover {background-color: #ddd;}

#importedList th {
    padding-top: 12px;
    padding-bottom: 12px;
    text-align: left;
    background-color: #4CAF50;
    color: white;
}
</style>

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


//if(!isset($_SESSION["publicationDetails"]) && empty($_SESSION["publicationDetails"])) {
//    echo "<script>
//            alert(\"You have to process the spreadsheet upload first.\");
//            setTimeout(\"location.href = '/reftool/v2/readExcel.php';\",1000);
//          </script>";
//    die();
//} else {
//    $publicationDetails = $_SESSION["publicationDetails"];
//    echo "<strong>Project: <abbr title=\"$publicationDetails[2]\">($publicationDetails[0]) $publicationDetails[1]</abbr> <a href='/reftool/v2/' title='Change Project'>[x]</a></strong> | ";
//    print_r($publicationDetails);
//}

?>
<a href="/reftool/v2/excelUpload.php">Upload Excel</a> |
<a href="/reftool/v2/readExcel.php">Reading from Excel</a> |
<a href="/reftool/v2/collectMdxPapers.php">Process Imported users</a> |
<a href="/reftool/v2/fullList.php">Show full list</a> |
<a href="/reftool/v2/publicationsPerAuthor.php">Publications per author</a> |
<a href="/reftool/v2/refUnits.php">All ref units and the papers assigned to it.</a> |
</div>
<hr>
