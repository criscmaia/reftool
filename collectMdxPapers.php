<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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


// check paper grade
function checkEra2010rank($issn) {
    include 'dbconnect.php';

    if ($checkEraRank = $conn->query("SELECT rank FROM era2010JournalTitleList WHERE CONCAT(ISSN1, ISSN2, ISSN3, ISSN4) LIKE '%$issn%' LIMIT 1;")) {
        $row_cnt = $checkEraRank->num_rows;
        if($row_cnt>0) {
            $resultsArray = $checkEraRank->fetch_assoc();
            echo $issn . " issn RANK is: " . $resultsArray['rank'] . " <br>";
        }
        $checkEraRank->close();
    }
}

$search = "Cristiano Maia";
$link="http://eprints.mdx.ac.uk/cgi/search/archive/simple/export_mdx_JSON.js?screen=Search&dataset=archive&_action_export=1&output=JSON&exp=0|1|-date%2Fcreators_name%2Ftitle|archive|-|q3%3Acreators_name%2Feditors_name%3AALL%3AEQ%3A".$search."|-|eprint_status%3Aeprint_status%3AANY%3AEQ%3Aarchive|metadata_visibility%3Ametadata_visibility%3AANY%3AEQ%3Ashow&n=&cache=1377950";
$result = mb_convert_encoding(file_get_contents($link), 'HTML-ENTITIES', "UTF-8");

//echo $result;
//echo "<hr>";

$json_str = $result;
$json = json_decode($json_str);

$jsonData = json_encode($json, JSON_PRETTY_PRINT);
echo "<pre>" . $jsonData . "</pre>";
echo "<hr>";

foreach($json as $indjson){
    $paper = $indjson;
    $type = $paper->type;
    $abstract = "";
    if(isset($paper->abstract)){
        $abstract = $paper->abstract;
    }

    $extra_info = "";

    if($type=='conference_item'){
         $extra_info = $paper->event_title;
    }

    if($type=='article'){
        $extra_info = $paper->publication;
    }

    if($type=='book_section'){
        $extra_info = $paper->book_title;
    }

    // Where the paper was published. So if it was an article, it will be the name of the journal. If it was a conference item then the name of the conference, and it was  a book section, the name of the book.
    $eprintid = $paper->eprintid;
    if (isset($paper->succeeds))     { $succeeds = $paper->succeeds; }
    if (isset($paper->creators))     { $allcreators = $paper->creators; }
    if (isset($paper->title))        { $title =  $paper->title; }
    if (isset($paper->date))         { $date =  $paper->date; }
    if (isset($paper->ispublished))  { $ispublished = $paper->ispublished; }
    if (isset($paper->pres_type))    { $pres_type = $paper->pres_type; }
    if (isset($paper->keywords))     { $keywords = $paper->keywords; }
    if (isset($paper->publication))  { $publication = $paper->publication; }
    if (isset($paper->volume))       { $volume = $paper->volume; }
    if (isset($paper->number))       { $number =  $paper->number; }
    if (isset($paper->publisher))    { $publisher = $paper->publisher; }
    if (isset($paper->uri))          { $uri =  $paper->uri; }
    if (isset($paper->official_url)) { $doi =  $paper->official_url; }
    if (isset($paper->event_title))  { $event_title = $paper->event_title; }
    if (isset($paper->event_type))   { $event_type =  $paper->event_type; }
    if (isset($paper->isbn))         { $isbn =  $paper->isbn; }
    if (isset($paper->issn))         { $issn = $paper->issn; }

    if(sizeof($allcreators)>0){
        foreach($allcreators as $eachcreator){
            $fName = $eachcreator->name->given;
            $lName = $eachcreator->name->family;
            check_mdxAuthorExistence($fName, $lName);
        }
    }

    if(sizeof($issn)>0){
//        foreach($issn as $eachissn){
            echo "issn: " . $issn . "<br>";
            checkEra2010rank($issn);
//        }
    }



    $existing_eprints_array = array();
//    $book_title =  mysql_real_escape_string($paper->book_title);
//    $book_title =  $paper->book_title;
    $split_date = explode('-',$date);
    $year = $split_date[0];
    if($title!="" && $year>=2014){
        // Full texts 	collection_id 	type 	creators 	succeeds 	title 	ispublished 	pres_type 	keywords 	publication 	volume 	number 	publisher 	event_title 	event_type 	isbn 	issn 	book_title 	ePrintID 	doi 	uri 	additional_info 	abstract
        if(!array_key_exists($eprintid, $existing_eprints_array)){
            // $conn->query("INSERT INTO `collections` (`project_id`, `type`, `creators`, `succeeds`, `title`, `ispublished`, `pres_type`, `keywords`, `publication`, `volume`, `number`, `publisher`, `event_title`, `event_type`, `isbn`, `issn`, `book_title`, `ePrintID`, `doi`, `uri`, `date`,`additional_info`,`abstract`) VALUES ('$project_id', '$type', '$creator_ids', '$succeeds', '$title', '$ispublished', '$pres_type', '$keywords', '$publication', '$volume', '$number', '$publisher', '$event_title', '$event_type', '$isbn', '$issn', '$book_title','$eprintid','$doi','$uri','$date','$extra_info','$abstract')");
            $existing_eprints_array[$eprintid] = $title;
//            $collection_id = $conn->getLastId();
            $results_found = 0;
        }
        $results_found++;
    }
}

$dt = date('Y-m-d H:i:s');

//    $conn->query("update `collection_processes` set `finshed_date_time` = '$dt', results_found = '$results_found' where `process_id` = '$search_id'");
//    $get_next = $conn->query("select * from `collection_processes` where `process_id` > '$search_id' LIMIT 1");
//    if($get_next->num_rows>0){
//        $search_next_id = $get_next->row['process_id'];
//    } else{
//        $search_next_id = 0;
//    }
?>
