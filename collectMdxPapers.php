<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'dbconnect.php';

$search = "Almaas Ali";
//$search = "Cristiano Maia";
$link="http://eprints.mdx.ac.uk/cgi/search/archive/simple/export_mdx_JSON.js?screen=Search&dataset=archive&_action_export=1&output=JSON&exp=0|1|-date%2Fcreators_name%2Ftitle|archive|-|q3%3Acreators_name%2Feditors_name%3AALL%3AEQ%3A".$search."|-|eprint_status%3Aeprint_status%3AANY%3AEQ%3Aarchive|metadata_visibility%3Ametadata_visibility%3AANY%3AEQ%3Ashow&n=&cache=1377950";
$result = mb_convert_encoding(file_get_contents($link), 'HTML-ENTITIES', "UTF-8");

$json_str = $result;
$json = json_decode($json_str);

$jsonData = json_encode($json, JSON_PRETTY_PRINT);
//echo "<pre>" . $jsonData . "</pre><hr>";

foreach($json as $indjson){
    $paper = $indjson;

    // GET TITLE AND DATE 1st BECAUSE IF IT IS EMPTY OR <2014, JUST SKIP
    if (isset($paper->date))         { $date = $paper->date; } else { $date = 'NULL';}
    if (isset($paper->title))        { $title = "'".$paper->title."'"; } else { $title = 'NULL';}
    if ($date != 'NULL') {
        $split_date = explode('-',$date);
        $year = $split_date[0];
        if ($title!="NULL" && $year>=2014){
            $date = "'".$date."'";      // add quotes for DB INSERT

            if (isset($paper->type))         { $type = "'".$paper->type."'"; }
            if (isset($paper->creators))     { $allcreators = $paper->creators; }
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


            // ONLY ADD TO DB IF IT HAS AN AUTHOR
            if(sizeof($allcreators)>0){
                foreach($allcreators as $eachcreator){
                    $fName = $eachcreator->name->given;
                    $lName = $eachcreator->name->family;
                    $email = $eachcreator->id;
                    $mdxAuthorId = getMdxAuthorId($fName, $lName, $email);

                    // CHECK IF PUBLICATION + AUTHOR ALREADY IN DB
                    if (!checkPublicationAlreadyInDB ($mdxAuthorId, $eprintid)){
                        $sql = "INSERT INTO `publication` (`type`,`author`,`succeeds`,`title`,`isPublished`,`presType`,`keywords`,`publication`,`volume`,`number`,`publisher`,`eventTitle`,`eventType`,`isbn`,`issn`,`bookTitle`,`ePrintID`,`doi`,`uri`, `abstract`,`date`,`eraRating`) VALUES ($type, $mdxAuthorId, $succeeds, $title, $ispublished, $presType, $keywords, $publication, $volume, $number, $publisher, $eventTitle, $eventType, $isbn, $issn, $bookTitle, $eprintid, $doi, $uri, $abstract, $date, $eraRating);";
                        if ($conn->query($sql) === TRUE) {
                            echo "New record created successfully. Publication added. Author ID: " . $mdxAuthorId." - Publication ID: ".$eprintid."<br>";
                        } else {
                            echo "<p>Error: " . $sql . "<br>" . $conn->error . "</p>";
                        }
                    } else {
                        echo "PUBLICATION + AUTHOR already in DB. Nothing changed. Author ID: " . $mdxAuthorId." -  Publication ID: ".$eprintid."<br>";
                    }
                }
            }
        }
    }
}
$conn->close();

// check if publication + author already in the DB
function checkPublicationAlreadyInDB ($mdxAuthorId, $eprintid) {
    include 'dbconnect.php';

    if ($checkPublicationAlreadyInDB = $conn->query("SELECT * FROM reftool.publication WHERE author = $mdxAuthorId AND ePrintID = '$eprintid';")) {
        $row_cnt = $checkPublicationAlreadyInDB->num_rows;
        if($row_cnt>0) {
            return true;
        } else {
            return false;
        }
        $checkPublicationAlreadyInDB->close();
    }
}

// check if author is on the DB
function getMdxAuthorId($fname, $lname, $email){
    include 'dbconnect.php';

    $fullName = $fname . ' ' . $lname;
    if ($checkMdxAuthorExistence = $conn->query("SELECT * FROM mdxAuthor WHERE CONCAT(firstName, ' ', lastName) LIKE '%$fullName%' OR email LIKE '%$email%';")) {
        $row_cnt = $checkMdxAuthorExistence->num_rows;
        if($row_cnt>0) {
            $resultsArray = $checkMdxAuthorExistence->fetch_assoc();
            $mdxAuthorID = $resultsArray['mdxAuthorID'];
            echo $fname . " " . $lname. " is in the DB. ID: " . $resultsArray['mdxAuthorID'] . " <br>";
            if ($email != $resultsArray['email'] || $fullName != $resultsArray['repositoryName']) {     // check if email or full name is different from DB
                $sql = "UPDATE `mdxAuthor` SET `email`='$email', `repositoryName`='$fullName' WHERE `mdxAuthorID` = '$mdxAuthorID';";
                $result = $conn->query($sql);
                if ($result) {
                    echo "Values udpated: email: ".$email.", repository name: ".$fullName. "<br>";
                } else {
                    echo "<p>Error: " . $sql . "<br>" . $conn->error . "</p>";
                }
                $conn->close();
            }
            return $resultsArray['mdxAuthorID'];
        } else {
            $found = strpos($email, "@mdx.ac.uk");
            if ($found === false) {
                echo "The string '@mdx.ac.uk' was not found in the string '$email' <br>";
                $currentEmployee = 0;
            } else {
                echo "The string '@mdx.ac.uk' was found in the string '$email' and exists at position $found <br>";
                $currentEmployee = 1;
            }

            $sql = "INSERT INTO `mdxAuthor` (`firstName`,`lastName`,`email`,`repositoryName`,`currentEmployee`) VALUES('$fname','$lname','$email','$fullName','$currentEmployee');";
            if ($conn->query($sql) === TRUE) {
                $last_id = $conn->insert_id;
                echo "New record created successfully. ID: ". $last_id. " - fullName: ".$fullName. "<br>";
                return $last_id;
            } else {
                echo "<p>Error: " . $sql . "<br>" . $conn->error . "</p>";
//                echo "<p>Error: " . $conn->error . "</p>";
            }
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
