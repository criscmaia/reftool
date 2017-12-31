<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'dbconnect.php';

$formFName = $_POST["firstName"];
$formLName = $_POST["lastName"];
$search = $formFName . " " . $formLName;
//$search = "Cristiano Maia";
$link="http://eprints.mdx.ac.uk/cgi/search/archive/simple/export_mdx_JSON.js?output=JSON&exp=0|1|-|q3:creators_name/editors_name:ALL:EQ:".rawurlencode($search);
$result = mb_convert_encoding(file_get_contents($link), 'HTML-ENTITIES', "UTF-8");
$json_str = $result;
$json = json_decode($json_str);
$jsonData = json_encode($json, JSON_PRETTY_PRINT);
//echo "<pre>" . $jsonData . "</pre><hr>";

echo "<h3>Searching for: $search <hr>";

foreach($json as $indjson){
    $paper = $indjson;

    // GET TITLE AND DATE 1st BECAUSE IF IT IS EMPTY OR <2014, JUST SKIP
    if (isset($paper->date))         { $date = $paper->date; } else { $date = "NULL";}
    if (isset($paper->title))        { $title = '"'.$paper->title.'"'; } else { $title = "NULL";}
    if ($date != "NULL") {
        if (strlen($date)==4) {              // only year
            $date = $date . "-01-01";
        } else if (strlen($date)==7) {       // only year and month
            $date = $date . "-01";
        }

        $split_date = explode('-',$date);
        $year = $split_date[0];
        if ($title!="NULL" && $year>=2014){
            $date = '"'.$date.'"';           // add quotes for DB INSERT


            if (isset($paper->type))         { $type = '"'.str_replace('"', "'", $paper->type).'"'; }
            if (isset($paper->creators))     { $allcreators = $paper->creators; }
            if (isset($paper->succeeds))     { $succeeds = $paper->succeeds; } else { $succeeds = "NULL";}
            if (isset($paper->ispublished))  { $ispublished = '"'.str_replace('"', "'", $paper->ispublished).'"'; } else { $ispublished = "NULL";}
            if (isset($paper->pres_type))    { $presType = '"'.str_replace('"', "'", $paper->pres_type).'"'; } else { $presType = "NULL";}
            if (isset($paper->keywords))     { $keywords = '"'.str_replace('"', "'", $paper->keywords).'"'; } else { $keywords = "NULL";}
            if (isset($paper->publication))  { $publication = '"'.str_replace('"', "'", $paper->publication).'"'; } else { $publication = "NULL";}
            if (isset($paper->volume))       { $volume = '"'.str_replace('"', "'", $paper->volume).'"'; } else { $volume = "NULL";}
            if (isset($paper->number))       { $number = '"'.str_replace('"', "'", $paper->number).'"'; } else { $number = "NULL";}
            if (isset($paper->publisher))    { $publisher = '"'.str_replace('"', "'", $paper->publisher).'"'; } else { $publisher = "NULL";}
            if (isset($paper->event_title))  { $eventTitle = '"'.str_replace('"', "'", $paper->event_title).'"'; } else { $eventTitle = "NULL";}
            if (isset($paper->event_type))   { $eventType = '"'.str_replace('"', "'", $paper->event_type).'"'; } else { $eventType = "NULL";}
            if (isset($paper->isbn))         { $isbn = '"'.str_replace('"', "'", $paper->isbn).'"'; } else { $isbn = "NULL";}
            if (isset($paper->issn))         { $issn = '"'.str_replace('"', "'", $paper->issn).'"'; } else { $issn = "NULL";}
            if (isset($paper->book_title))   { $bookTitle = '"'.str_replace('"', "'", $paper->book_title).'"'; } else { $bookTitle = "NULL";}
            if (isset($paper->eprintid))     { $eprintid = $paper->eprintid; } else { $eprintid = "NULL";}
            if (isset($paper->official_url)) { $doi = '"'.str_replace('"', "'", $paper->official_url).'"'; } else { $doi = "NULL";}
            if (isset($paper->uri))          { $uri = '"'.str_replace('"', "'", $paper->uri).'"'; } else { $uri = "NULL";}
            if (isset($paper->abstract))     { $abstract = '"'.str_replace('"', "'", $paper->abstract).'"'; } else { $abstract = "NULL";}

            if ($issn != "NULL") {
                $eraRating = checkEra2010rank($issn);       // check ERA2010 rank based on ISSN
            }


            // ONLY ADD TO DB IF IT HAS AN AUTHOR
            if(sizeof($allcreators)>0){
                foreach($allcreators as $eachcreator){
                    $fName = $eachcreator->name->given;
                    $lName = $eachcreator->name->family;
                    $email = $eachcreator->id;
                    echo "For each author: fName: '$fName', lName: '$lName', email: '$email'<br>";
                    $mdxAuthorId = getMdxAuthorId($fName, $lName, $email);

                    // CHECK IF PUBLICATION + AUTHOR ALREADY IN DB
                    $publicationAlreadyInDB = checkPublicationAlreadyInDB ($mdxAuthorId, $eprintid);
                    echo "Publication + Author already in the DB? '$publicationAlreadyInDB'. Should show nothing if FALSE and 1 if true <br>";
                    if (!$publicationAlreadyInDB){
                        $sql = "INSERT INTO `publication` (`type`,`author`,`succeeds`,`title`,`isPublished`,`presType`,`keywords`,`publication`,`volume`,`number`,`publisher`,`eventTitle`,`eventType`,`isbn`,`issn`,`bookTitle`,`ePrintID`,`doi`,`uri`, `abstract`,`date`,`eraRating`) VALUES ($type, $mdxAuthorId, $succeeds, $title, $ispublished, $presType, $keywords, $publication, $volume, $number, $publisher, $eventTitle, $eventType, $isbn, $issn, $bookTitle, $eprintid, $doi, $uri, $abstract, $date, $eraRating);";
                        if ($conn->query($sql) === TRUE) {
                            echo "New record created successfully. Publication added. Author ID: " . $mdxAuthorId." - Publication ID: ".$eprintid."<br>";
                        } else {
                            echo "<p>Error: " . $sql . "<br>" . $conn->error . "</p>";
                        }
                    } else {
                        echo "Publication + Author already in the DB. Nothing changed. Author ID: " . $mdxAuthorId." -  Publication ID: ".$eprintid."<br>";
                    }
                    echo "<hr>";
                }
            }
        } else {
            echo "Either Title is null or no publications after 2014. Date: $date . Title: $title <br>";
        }
    } else {
        echo "Date is null. Date: $date . Title: $title<br>";
    }
}
$conn->close();

// check if author is on the DB
function getMdxAuthorId($fname, $lname, $email){
    echo "Get MDX author ID: fName: '$fname', lName: '$lname', email: '$email' <br>";

    include 'dbconnect.php';

    $fullName = $fname . ' ' . $lname;

    // check if email is MDX
    $found = strpos($email, "@mdx.ac.uk");
    if ($found === false) {
        echo "Checking if email finishes with '@mdx.ac.uk'. Not found. Current email: '$email' <br>";
        $query = "SELECT * FROM mdxAuthor WHERE CONCAT(firstName, ' ', lastName) LIKE '%$fullName%';";      // does not search by email because many authors with '[ex-mdx]' email.
        $currentEmployee = 0;
    } else {
        echo "Checking if email finishes with '@mdx.ac.uk'. Found. Current email: '$email' <br>";
        $query = "SELECT * FROM mdxAuthor WHERE CONCAT(firstName, ' ', lastName) LIKE '%$fullName%' OR email LIKE '%$email%';";
        $currentEmployee = 1;
    }


    if ($checkMdxAuthorExistence = $conn->query($query)) {   // search author find by name or email TODO: do only email so there is no risk of duplicate names?
        $row_cnt = $checkMdxAuthorExistence->num_rows;

        if($row_cnt>0) {
            echo "found by either name or email. <br>";

            $resultsArray = $checkMdxAuthorExistence->fetch_assoc();
            $mdxAuthorID = $resultsArray['mdxAuthorID'];
            echo $fname . " " . $lname. " is in the DB. ID: " . $resultsArray['mdxAuthorID'] . " <br>";

            if ($email != $resultsArray['email'] || $fullName != $resultsArray['repositoryName']) {     // check if email or full name is different from DB
                echo "email or full name is different. current email: '$email', new email: ".$resultsArray['email'].". current repository name: '$fullName', new name:".$resultsArray['repositoryName']."<br>";
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
            echo "author does not exist on db. <br>";
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



// check paper rank
function checkEra2010rank($issn) {
    include 'dbconnect.php';

    //remove quotes from ISSN
    $issn = trim($issn, '"');
    if ($checkEraRank = $conn->query("SELECT rank FROM era2010JournalTitleList WHERE CONCAT(ISSN1, ISSN2, ISSN3, ISSN4) LIKE '%$issn%' LIMIT 1;")) {
        $row_cnt = $checkEraRank->num_rows;
        if($row_cnt>0) {
            $resultsArray = $checkEraRank->fetch_assoc();
            return '"'.$resultsArray['rank'].'"';
        } else {
            return "NULL";
        }
        $checkEraRank->close();
    }
}
?>
