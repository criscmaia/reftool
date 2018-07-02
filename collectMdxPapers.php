<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'menu.php';
include 'dbconnect.php';

if(!isset($_SESSION["publicationDetails"]) && empty($_SESSION["publicationDetails"])) {
    header("Location: /reftool/v2/readExcel.php");
    die();
} else {
    $publicationDetails = $_SESSION["publicationDetails"];

    $papersObj = json_decode($publicationDetails, true);                                   // Takes a JSON encoded string and converts it into a PHP variable

    if (json_last_error() === JSON_ERROR_NONE) {                                           // if JSON is valid
        if (count($papersObj)>0) {                                                         // if at least one result is available
            echo "Valid JSON and not empty <br>";
            $eraRating = "NULL";
            /*
            highlight_string("<?php\n\$data =\n" . var_export($papersObj, true) . ";\n?>");
//            */

            foreach($papersObj as $papersObjKeys => $papersObjValues) {                    // go through each paper
//              GET TITLE AND DATE 1st BECAUSE IF IT IS EMPTY OR <2014, JUST SKIP
                if (isset($papersObj[$papersObjKeys]['date']))         { $date = $papersObj[$papersObjKeys]['date']; } else { $date = "NULL";}
                if (isset($papersObj[$papersObjKeys]['title']))        { $title = '"'.addslashes($papersObj[$papersObjKeys]['title']).'"'; } else { $title = "NULL";}

                if ($date != "NULL") {
                    if (strlen($date)==4) {              // only year
                        $date = $date . "-01-01";
                    } else if (strlen($date)==7) {       // only year and month
                        $date = $date . "-01";
                    }

                    $split_date = explode('-',$date);
                    $year = $split_date[0];
                    if ($title!="NULL" && $year>=2014){
//                        echo "valid paper <br>";
                    } else {
                        echo "Either TITLE is null or YEAR < 2014 -- ".$papersObj[$papersObjKeys]['eprintid'].": ".$papersObj[$papersObjKeys]['title'].". <br>";
                    }
                } else {
                    echo "Date is null -- ".$papersObj[$papersObjKeys]['eprintid'].": ".$papersObj[$papersObjKeys]['title'].".<br>";
                }
            }
        } else {
            echo "No valid publications collected.<br>";
            echo "<a href='/reftool/v2/'>go back</a><hr>";
        }
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
            $rank = $resultsArray['rank'];
            return '"'.$rank.'"';
        } else {
            return "NULL";
        }
        $checkEraRank->close();
    }
}



//            foreach($papersObj as $paper){

//          GET TITLE AND DATE 1st BECAUSE IF IT IS EMPTY OR <2014, JUST SKIP
            /*
            if (isset($paper->date))         { $date = $paper->date; } else { $date = "NULL";}
                echo $date . "<br>";
            if (isset($paper->title))        { $title = '"'.addslashes($paper->title).'"'; } else { $title = "NULL";}
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

                    if (isset($paper->type))         { $type = '"'.addslashes($paper->type).'"'; }
                    if (isset($paper->creators))     { $allcreators = $paper->creators; } else { $allcreators = "NULL";}        // minor scenarios where creator is null
                    if (isset($paper->succeeds))     { $succeeds = $paper->succeeds; } else { $succeeds = "NULL";}
                    if (isset($paper->ispublished))  { $ispublished = '"'.addslashes($paper->ispublished).'"'; } else { $ispublished = "NULL";}
                    if (isset($paper->pres_type))    { $presType = '"'.addslashes($paper->pres_type).'"'; } else { $presType = "NULL";}
                    if (isset($paper->keywords))     { $keywords = '"'.addslashes($paper->keywords).'"'; } else { $keywords = "NULL";}
                    if (isset($paper->publication))  { $publication = '"'.addslashes($paper->publication).'"'; } else { $publication = "NULL";}
                    if (isset($paper->volume))       { $volume = '"'.addslashes($paper->volume).'"'; } else { $volume = "NULL";}
                    if (isset($paper->number))       { $number = '"'.addslashes($paper->number).'"'; } else { $number = "NULL";}
                    if (isset($paper->publisher))    { $publisher = '"'.addslashes($paper->publisher).'"'; } else { $publisher = "NULL";}
                    if (isset($paper->event_title))  { $eventTitle = '"'.addslashes($paper->event_title).'"'; } else { $eventTitle = "NULL";}
                    if (isset($paper->event_type))   { $eventType = '"'.addslashes($paper->event_type).'"'; } else { $eventType = "NULL";}
                    if (isset($paper->isbn))         { $isbn = '"'.addslashes($paper->isbn).'"'; } else { $isbn = "NULL";}
                    if (isset($paper->issn))         { $issn = '"'.addslashes($paper->issn).'"'; } else { $issn = "NULL";}
                    if (isset($paper->book_title))   { $bookTitle = '"'.addslashes($paper->book_title).'"'; } else { $bookTitle = "NULL";}
                    if (isset($paper->eprintid))     { $eprintid = $paper->eprintid; } else { $eprintid = "NULL";}
                    if (isset($paper->official_url)) { $doi = '"'.addslashes($paper->official_url).'"'; } else { $doi = "NULL";}
                    if (isset($paper->uri))          { $uri = '"'.addslashes($paper->uri).'"'; } else { $uri = "NULL";}
                    if (isset($paper->abstract))     { $abstract = '"'.addslashes($paper->abstract).'"'; } else { $abstract = "NULL";}
                }


                echo $issn;
                if ($issn != "NULL") {
                    $eraRating = checkEra2010rank($issn);       // check ERA2010 rank based on ISSN
                    echo $eraRating . "<br>";
                }
            }
        }
              */



//
//                            // ONLY ADD TO DB IF IT HAS AN AUTHOR
//                            if(sizeof($allcreators)>0 && $allcreators!="NULL"){
//                                foreach($allcreators as $eachcreator){
//                                    $fName = $eachcreator->name->given;
//                                    $lName = $eachcreator->name->family;
//                                    $email = strtolower($eachcreator->id);
//
//                                    if ($formEmail == $email) {             // same author as the one in the uploaded spreadsheet
//                                        $mdxAuthorId = getMdxAuthorId($projectDetails, $fName, $lName, $email, $formEmail, $formCurrentEmployee);
//                                    } else {
//                                        $mdxAuthorId = getMdxAuthorId($projectDetails, $fName, $lName, $email, $formEmail, null);
//                                    }
//
//
//                                    // CHECK IF PUBLICATION + AUTHOR ALREADY IN DB
//                                    $publicationAlreadyInDB = checkPublicationAlreadyInDB ($projectDetails, $mdxAuthorId, $eprintid);
//        //                            echo "Publication + Author already in the DB? '$publicationAlreadyInDB'. Should show nothing if FALSE and 1 if true <br>";
//                                    if (!$publicationAlreadyInDB && !empty($mdxAuthorId)){      // Olga	van den Akker was an example of someone with NULL creator and empty mdxAuthorId
//                                        $sql = "INSERT INTO `publication` (`projectID`,`type`,`author`,`succeeds`,`title`,`isPublished`,`presType`,`keywords`,`publication`,`volume`,`number`,`publisher`,`eventTitle`,`eventType`,`isbn`,`issn`,`bookTitle`,`ePrintID`,`doi`,`uri`, `abstract`,`date`,`eraRating`) VALUES ($projectDetails[0], $type, $mdxAuthorId, $succeeds, $title, $ispublished, $presType, $keywords, $publication, $volume, $number, $publisher, $eventTitle, $eventType, $isbn, $issn, $bookTitle, $eprintid, $doi, $uri, $abstract, $date, $eraRating);";
//                                        if ($conn->query($sql) === TRUE) {
//        //                                    echo "New record created successfully. Publication added. Author ID: " . $mdxAuthorId." - Publication ID: ".$eprintid."<br>";
//                                        } else {
//                                            echo "<p>Error: " . $sql . "<br>" . $conn->error . "</p>";
//                                        }
//                                    } else {
//        //                                echo "Publication + Author already in the DB. Nothing changed. Author ID: " . $mdxAuthorId." -  Publication ID: ".$eprintid."<br>";
//                                    }
//                                    $totalPapersPerPerson++;
//        //                            echo "<hr>";
//                                }
//                            }
//                        }
////                    }
//                    else {
//        //                echo "Either Title is null or no publications after 2014. Date: $date . Title: $title <br>";
//                    }
//                } else {
//        //            echo "Date is null. Date: $date . Title: $title<br>";
//                }
//            }
//        }
//        echo "Total of <strong>" . $totalPapersPerPerson . "</strong> combinations of publication+author (where this person is an author) found.<br><br>";
//    }
//    echo "<hr><strong>Search completed! </strong><br>";
//    echo "<a href='/reftool/fullList.php'>Show full list →</a>";
//}       // check if variable session is set
//
//
//// check if publication + author already in the DB
//function checkPublicationAlreadyInDB ($projectDetails, $mdxAuthorId, $eprintid) {
//    include 'dbconnect.php';
//
//    if ($checkPublicationAlreadyInDB = $conn->query("SELECT * FROM reftool.publication WHERE  projectID = $projectDetails[0] AND author = $mdxAuthorId AND ePrintID = '$eprintid';")) {
//        $row_cnt = $checkPublicationAlreadyInDB->num_rows;
//        if($row_cnt>0) {
//            return true;
//        } else {
//            return false;
//        }
//        $checkPublicationAlreadyInDB->close();
//    }
//}
//
//
//
//// check if author is on the DB
//function getMdxAuthorId($projectDetails, $fname, $lname, $email, $formEmail, $formCurrentEmployee){
//    include 'dbconnect.php';
//
//    $fullName = $fname . ' ' . $lname;
//    $found = strpos($email, "@mdx.ac.uk");
//
//    if (isset($formCurrentEmployee)) {          // if spreadsheet says if current employee
//        $currentEmployee = $formCurrentEmployee;
//        $query = "SELECT * FROM mdxAuthor WHERE projectID = $projectDetails[0] AND CONCAT(firstName, ' ', lastName) LIKE '%$fullName%' OR email LIKE '%$email%';";
//    } else if ($found) {                        // if email ends with @mdx.ac.uk
//        $currentEmployee = 1;
//        $query = "SELECT * FROM mdxAuthor WHERE projectID = $projectDetails[0] AND CONCAT(firstName, ' ', lastName) LIKE '%$fullName%' OR email LIKE '%$email%';";
//    } else {                                    // considers that is not an employee
//        $currentEmployee = 0;
//        $query = "SELECT * FROM mdxAuthor WHERE projectID = $projectDetails[0] AND CONCAT(firstName, ' ', lastName) LIKE '%$fullName%';";      // does not search by email because many authors with '[ex-mdx]' email.
//    }
//
//
//    if ($checkMdxAuthorExistence = $conn->query($query)) {
//        $row_cnt = $checkMdxAuthorExistence->num_rows;
//
//        if($row_cnt>0) {                                                // author found in the DB
//            $resultsArray = $checkMdxAuthorExistence->fetch_assoc();
//            return $resultsArray['mdxAuthorID'];
//        } else {                                                        // author does NOT exist in the DB
//            $sql = "INSERT INTO `mdxAuthor` (`projectID`,`firstName`,`lastName`,`email`,`repositoryName`,`currentEmployee`) VALUES('$projectDetails[0]', '$fname','$lname','$email','$fullName','$currentEmployee');";
//            if ($conn->query($sql) === TRUE) {
//                $last_id = $conn->insert_id;
////                echo "New record created successfully. ID: ". $last_id. " - fullName: ".$fullName. "<br>";
//                return $last_id;
//            } else {
//                echo "<p>Error: " . $sql . "<br>" . $conn->error . "</p>";
//            }
//        }
//        $checkMdxAuthorExistence->close();
//    }
//}
?>
