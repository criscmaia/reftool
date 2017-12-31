<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$search = "Almaas Ali";
//$search = "Cristiano Maia";
$link="http://eprints.mdx.ac.uk/cgi/search/archive/simple/export_mdx_JSON.js?screen=Search&dataset=archive&_action_export=1&output=JSON&exp=0|1|-date%2Fcreators_name%2Ftitle|archive|-|q3%3Acreators_name%2Feditors_name%3AALL%3AEQ%3A".$search."|-|eprint_status%3Aeprint_status%3AANY%3AEQ%3Aarchive|metadata_visibility%3Ametadata_visibility%3AANY%3AEQ%3Ashow&n=&cache=1377950";
$result = mb_convert_encoding(file_get_contents($link), 'HTML-ENTITIES', "UTF-8");

//echo $result;
//echo "<hr>";

$json_str = $result;
$json = json_decode($json_str);

$jsonData = json_encode($json, JSON_PRETTY_PRINT);
//echo "<pre>" . $jsonData . "</pre><hr>";

foreach($json as $indjson){
    $paper = $indjson;

    // GET TITLE AND DATE 1st BECAUSE IF IS EMPTY OR <2014, JUST SKIP
    if (isset($paper->date))         { $date = $paper->date; } else { $date = 'NULL';}
    if (isset($paper->title))        { $title = "'".$paper->title."'"; } else { $title = 'NULL';}
    if ($date != 'NULL') {
        $split_date = explode('-',$date);
        $year = $split_date[0];
        if ($title!="NULL" && $year>=2014){
            $date = "'".$date."'";      // add quotes for DB INSERT

            if (isset($paper->type))        { $type = "'".$paper->type."'"; }
            if (isset($paper->creators))     { $allcreators = $paper->creators; }
            // if it has the authors, check if in the DB
            if(sizeof($allcreators)>0){
                foreach($allcreators as $eachcreator){
                    $fName = $eachcreator->name->given;
                    $lName = $eachcreator->name->family;
                    check_mdxAuthorExistence($fName, $lName);
                }
            }
            if (isset($paper->succeeds))     { $succeeds = $paper->succeeds; } else { $succeeds = 'NULL';}
            if (isset($paper->ispublished))  { $ispublished = "'".$paper->ispublished."'"; } else { $ispublished = 'NULL';}
            if (isset($paper->pres_type))    { $presType = "'".$paper->pres_type."'"; } else { $presType = 'NULL';}
            if (isset($paper->keywords))     { $keywords = "'".$paper->keywords."'"; } else { $keywords = 'NULL';}
            if (isset($paper->publication))  { $publication = "'".$paper->publication."'"; } else { $publication = 'NULL';}
            if (isset($paper->volume))       { $volume = "'".$paper->volume."'"; } else { $volume = 'NULL';}
            if (isset($paper->number))       { $number = "'".$paper->number."'"; } else { $number = 'NULL';}
            if (isset($paper->publisher))    { $publisher = "'".$paper->publisher."'"; } else { $publisher = 'NULL';}
            if (isset($paper->event_title))  { $eventTitle = "'".$paper->event_title."'"; } else { $eventTitle = 'NULL';}
            if (isset($paper->event_type))   { $eventType = "'".$paper->event_type."'"; } else { $eventType = 'NULL';}
            if (isset($paper->isbn))         { $isbn = "'".$paper->isbn."'"; } else { $isbn = 'NULL';}
            if (isset($paper->issn))         { $issn = "'".$paper->issn."'"; } else { $issn = 'NULL';}
            if (isset($paper->book_title))   { $bookTitle = "'".$paper->book_title."'"; } else { $bookTitle = 'NULL';}
            if (isset($paper->eprintid))     { $eprintid = $paper->eprintid; } else { $eprintid = 'NULL';}
            if (isset($paper->official_url)) { $doi = "'".$paper->official_url."'"; } else { $doi = 'NULL';}
            if (isset($paper->uri))          { $uri = "'".$paper->uri."'"; } else { $uri = 'NULL';}
            if (isset($paper->abstract))     { $abstract = "'".$paper->abstract."'"; } else { $abstract = 'NULL';}

            if ($issn != 'NULL') {
                $eraRating = checkEra2010rank($issn);       // check ERA2010 rank based on ISSN
            }

            $sql = "INSERT INTO `publication` (`type`,`authors`,`succeeds`,`title`,`isPublished`,`presType`,`keywords`,`publication`,`volume`,`number`,`publisher`,`eventTitle`,`eventType`,`isbn`,`issn`,`bookTitle`,`ePrintID`,`doi`,`uri`, `abstract`,`date`,`eraRating`) VALUES ($type, 1, $succeeds, $title, $ispublished, $presType, $keywords, $publication, $volume, $number, $publisher, $eventTitle, $eventType, $isbn, $issn, $bookTitle, $eprintid, $doi, $uri, $abstract, $date, $eraRating);";

            echo $sql;
            echo "<br><br>";
        }
    }
}


// check if author is on the DB
function check_mdxAuthorExistence($fname, $lname){
    include 'dbconnect.php';

    $fullName = $fname . ' ' . $lname;
    if ($checkMdxAuthorExistence = $conn->query("SELECT * FROM mdxAuthor WHERE CONCAT(firstName, ' ', lastName) like '$fullName';")) {
        $row_cnt = $checkMdxAuthorExistence->num_rows;
        if($row_cnt>0) {
            $resultsArray = $checkMdxAuthorExistence->fetch_assoc();
            echo $fname . " " . $lname. " is IN THE DB. ID: " . $resultsArray['mdxAuthorID'] . " <br>";
        }
        $checkMdxAuthorExistence->close();
    }
}


// check paper rank
function checkEra2010rank($issn) {
    include 'dbconnect.php';

    //remove quotes from ISSN
    $issn = trim($issn, "'");
    if ($checkEraRank = $conn->query("SELECT rank FROM era2010JournalTitleList WHERE CONCAT(ISSN1, ISSN2, ISSN3, ISSN4) LIKE '%$issn%' LIMIT 1;")) {
        $row_cnt = $checkEraRank->num_rows;
        if($row_cnt>0) {
            $resultsArray = $checkEraRank->fetch_assoc();
            return "'".$resultsArray['rank']."'";
        } else {
            return 'NULL';
        }
        $checkEraRank->close();
    }
}
?>
