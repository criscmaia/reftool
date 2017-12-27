<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'dbconnect.php';

function check_mdxAuthorExistence($fname, $lname){
    include 'dbconnect.php';
    $chk = $conn->query("SELECT * FROM mdxAuthor WHERE firstName = '$fname' AND lastName = '$lname';") ;
    if($chk->num_rows==0){
        echo $fname . " " . $lname . " not in the DB.";
        //$conn->query("INSERT INTO `creators` (  `creator_family_name`, `creator_given_name`, `project_id`) VALUES ('$fname', '$gname', '$project_id')");
        //return $conn->getLastId();
    } else{
        echo $fname . " " . $lname . " already in the DB. (id: " . $chk->row['id'] . ")";
        //$a = $chk->row;
        //return $a['creator_id'];
    }
}

$search = "Almaas Ali";
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
    $te = $indjson;
    $type = $te->type;
    $abstract = "";
    if($te->abstract){
        $abstract = $te->abstract;
        echo $abstract . "<br>";
    }

    /*
    conference_item
    article
    book_section
    other
    book
    monograph
    thesis
    video
    */

    $extra_info = "";

    if($type=='conference_item'){
//        $extra_info = mysql_real_escape_string($te->event_title);
         $extra_info = $te->event_title;
    }

    if($type=='article'){
//        $extra_info = mysql_real_escape_string($te->publication);
        $extra_info = $te->publication;
    }


    if($type=='book_section'){
//        $extra_info = mysql_real_escape_string($te->book_title);
        $extra_info = $te->book_title;
    }

    echo $extra_info;

    // Where the paper was published. So if it was an article, it will be the name of the journal. If it was a conference item then the name of the conference, and it was  a book section, the name of the book.

//    $eprintid = $te->eprintid;
//    $succeeds = mysql_real_escape_string($te->succeeds);
//    $allcreators = $te->creators ;
//    $title =  mysql_real_escape_string($te->title);
//    $date =  mysql_real_escape_string($te->date);
//    $ispublished =  mysql_real_escape_string($te->ispublished);
//    $pres_type = mysql_real_escape_string($te->pres_type);
//    $keywords = mysql_real_escape_string($te->keywords);
//    $publication = mysql_real_escape_string($te->publication);
//    $volume = mysql_real_escape_string($te->volume);
//    $number =  mysql_real_escape_string($te->number);
//    $publisher =  mysql_real_escape_string($te->publisher);
//    $uri =  mysql_real_escape_string($te->uri);
//    $doi =  mysql_real_escape_string($te->official_url);
//    $event_title = mysql_real_escape_string($te->event_title);
//    $event_type =  mysql_real_escape_string($te->event_type);
//    $isbn =  mysql_real_escape_string($te->isbn);
//    $issn =  mysql_real_escape_string($te->issn);

    $eprintid = $te->eprintid;
    $succeeds = $te->succeeds;
    $allcreators = $te->creators ;
    $title =  $te->title;
    $date =  $te->date;
    $ispublished = $te->ispublished;
    $pres_type = $te->pres_type;
    $keywords = $te->keywords;
    $publication = $te->publication;
    $volume = $te->volume;
    $number =  $te->number;
    $publisher = $te->publisher;
    $uri =  $te->uri;
    $doi =  $te->official_url;
    $event_title = $te->event_title;
    $event_type =  $te->event_type;
    $isbn =  $te->isbn;
    $issn =  $te->issn;

    if(sizeof($allcreators)>0){
        echo "allcreators > 0";
        foreach($allcreators as $eachcreator){
//            $g = mysql_real_escape_string($eachcreator->name->given);
//            $f = mysql_real_escape_string($eachcreator->name->family);

            $g = $eachcreator->name->given;
            $f = $eachcreator->name->family;
            $creator_ids .= '|'. check_mdxAuthorExistence($f, $g);
        }
        $creator_ids .='|0';
    }



    $existing_eprints_array = array();
//    $book_title =  mysql_real_escape_string($te->book_title);
//    $book_title =  $te->book_title;
    $split_date = explode('-',$date);
    $year = $split_date[0];
    if($title!="" && $year>=2014){
        // Full texts 	collection_id 	type 	creators 	succeeds 	title 	ispublished 	pres_type 	keywords 	publication 	volume 	number 	publisher 	event_title 	event_type 	isbn 	issn 	book_title 	ePrintID 	doi 	uri 	additional_info 	abstract
        if(!array_key_exists($eprintid, $existing_eprints_array)){
            // $conn->query("INSERT INTO `collections` (`project_id`, `type`, `creators`, `succeeds`, `title`, `ispublished`, `pres_type`, `keywords`, `publication`, `volume`, `number`, `publisher`, `event_title`, `event_type`, `isbn`, `issn`, `book_title`, `ePrintID`, `doi`, `uri`, `date`,`additional_info`,`abstract`) VALUES ('$project_id', '$type', '$creator_ids', '$succeeds', '$title', '$ispublished', '$pres_type', '$keywords', '$publication', '$volume', '$number', '$publisher', '$event_title', '$event_type', '$isbn', '$issn', '$book_title','$eprintid','$doi','$uri','$date','$extra_info','$abstract')");
            $existing_eprints_array[$eprintid] = $title;
//            $collection_id = $conn->getLastId();
            $creator_ids_arr = explode('|', $creator_ids);
            $results_found = 0;

            for($i=1;$i<sizeof($creator_ids_arr);$i++){
                //"INSERT INTO `creators_collection` ( `creator_id`, `collection_id`) VALUES ( '$creator_ids[$i]', '$collection_id')<br>";
                //mysql_query("INSERT INTO `creators_collection` ( `creator_id`, `collection_id`) VALUES ( '$creator_ids_arr[$i]', '$collection_id')");
            }
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
