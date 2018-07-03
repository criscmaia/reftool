<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'menu.php';
include 'dbconnect.php';

if(!isset($_SESSION["publications"]) && empty($_SESSION["publications"])) {
    header("Location: /reftool/v2/readExcel.php");
    die();
} else {
    $publications = $_SESSION["publications"];
            /*
            highlight_string("<?php\n\$data =\n" . var_export($publications, true) . ";\n?>");
//            */

//    $papersObj = json_decode($publications, true);                                   // Takes a JSON encoded string and converts it into a PHP variable

//    if (json_last_error() === JSON_ERROR_NONE) {                                           // if JSON is valid
        echo count($publications)."<br>";
        if (count($publications)>0) {                                                         // if at least one result is available
//            echo "Valid JSON and not empty <br>";
            $eraRating = "NULL";
            /*
            highlight_string("<?php\n\$data =\n" . var_export($publications, true) . ";\n?>");
//            */

            foreach($publications as $papersObjKeys => $papersObjValues) {                    // go through each paper
//              GET TITLE AND DATE 1st BECAUSE IF IT IS EMPTY OR <2014, JUST SKIP
                if (isset($publications[$papersObjKeys]['date']))         { $date = $publications[$papersObjKeys]['date']; } else { $date = "NULL";}
                if (isset($publications[$papersObjKeys]['title']))        { $title = '"'.addslashes($publications[$papersObjKeys]['title']).'"'; } else { $title = "NULL";}

                if ($date != "NULL") {
                    if (strlen($date)==4) {                 // only year
                        $date = $date . "-01-01";
                    } else if (strlen($date)==7) {          // only year and month
                        $date = $date . "-01";
                    }

                    $split_date = explode('-',$date);
                    $year = $split_date[0];
                    if ($title!="NULL" && $year>=2014){     // valid paper to process

                        $date = '"'.$date.'"';              // add quotes for DB INSERT

                        if (isset($publications[$papersObjKeys]['type']))         { $type        = '"'.addslashes($publications[$papersObjKeys]['type']).'"'; }
                        if (isset($publications[$papersObjKeys]['creators']))     { $allcreators = $publications[$papersObjKeys]['creators']; } else { $allcreators = "NULL";}        // minor scenarios where creator is null
                        if (isset($publications[$papersObjKeys]['succeeds']))     { $succeeds    = $publications[$papersObjKeys]['succeeds']; } else { $succeeds = "NULL";}
                        if (isset($publications[$papersObjKeys]['ispublished']))  { $ispublished = '"'.addslashes($publications[$papersObjKeys]['ispublished']).'"'; } else { $ispublished = "NULL";}
                        if (isset($publications[$papersObjKeys]['pres_type']))    { $presType    = '"'.addslashes($publications[$papersObjKeys]['pres_type']).'"'; } else { $presType = "NULL";}
                        if (isset($publications[$papersObjKeys]['keywords']))     { $keywords    = '"'.addslashes($publications[$papersObjKeys]['keywords']).'"'; } else { $keywords = "NULL";}
                        if (isset($publications[$papersObjKeys]['publication']))  { $publication = '"'.addslashes($publications[$papersObjKeys]['publication']).'"'; } else { $publication = "NULL";}
                        if (isset($publications[$papersObjKeys]['volume']))       { $volume      = '"'.addslashes($publications[$papersObjKeys]['volume']).'"'; } else { $volume = "NULL";}
                        if (isset($publications[$papersObjKeys]['number']))       { $number      = '"'.addslashes($publications[$papersObjKeys]['number']).'"'; } else { $number = "NULL";}
                        if (isset($publications[$papersObjKeys]['publisher']))    { $publisher   = '"'.addslashes($publications[$papersObjKeys]['publisher']).'"'; } else { $publisher = "NULL";}
                        if (isset($publications[$papersObjKeys]['event_title']))  { $eventTitle  = '"'.addslashes($publications[$papersObjKeys]['event_title']).'"'; } else { $eventTitle = "NULL";}
                        if (isset($publications[$papersObjKeys]['event_type']))   { $eventType   = '"'.addslashes($publications[$papersObjKeys]['event_type']).'"'; } else { $eventType = "NULL";}
                        if (isset($publications[$papersObjKeys]['isbn']))         { $isbn        = '"'.addslashes($publications[$papersObjKeys]['isbn']).'"'; } else { $isbn = "NULL";}
                        if (isset($publications[$papersObjKeys]['issn']))         { $issn        = '"'.addslashes($publications[$papersObjKeys]['issn']).'"'; } else { $issn = "NULL";}
                        if (isset($publications[$papersObjKeys]['book_title']))   { $bookTitle   = '"'.addslashes($publications[$papersObjKeys]['book_title']).'"'; } else { $bookTitle = "NULL";}
                        if (isset($publications[$papersObjKeys]['eprintid']))     { $eprintid    = $publications[$papersObjKeys]['eprintid']; } else { $eprintid = "NULL";}
                        if (isset($publications[$papersObjKeys]['official_url'])) { $doi         = '"'.addslashes($publications[$papersObjKeys]['official_url']).'"'; } else { $doi = "NULL";}
                        if (isset($publications[$papersObjKeys]['uri']))          { $uri         = '"'.addslashes($publications[$papersObjKeys]['uri']).'"'; } else { $uri = "NULL";}
                        if (isset($publications[$papersObjKeys]['abstract']))     { $abstract    = '"'.addslashes($publications[$papersObjKeys]['abstract']).'"'; } else { $abstract = "NULL";}

                        if ($issn != "NULL") {
                            $eraRating = checkEra2010rank($issn);       // check ERA2010 rank based on ISSN
//                            echo $eprintid.":".$issn.":".$title." - ".$eraRating . "<br>";
                        }

                        if(sizeof($allcreators)>0 && $allcreators!="NULL"){
                                        /*
            highlight_string("<?php\n\$data =\n" . var_export($allcreators, true) . ";\n?>");
//            */

//                            foreach($allcreators as $eachcreator) {
//                                ($author->getRepositoryName() == NULL)?($searchingName = $author->getFullNameReverse()):($searchingName = $author->getRepositoryName());    // define what is going to be the search variable
//                                $creatorFullName = ($eachcreator["name"]["given"]." ".$eachcreator["name"]["family"]);                                                      // get the creator full name
//                                if(startsWith($creatorFullName, $author->getFirstName()) && endsWith($creatorFullName, $author->getLastName())) {                           // double check if author is one of the creators
//                                    $author->
//                                }
//
//                                echo "$fName $lName <br>";
//                            }
                        }



                    } else {
                        echo "Either TITLE is null or YEAR < 2014 -- ".$publications[$papersObjKeys]['eprintid'].": ".$publications[$papersObjKeys]['title'].". <br>";
                    }
                } else {
//                    echo "Date is null -- ".$publications[$papersObjKeys]['eprintid'].": ".$publications[$papersObjKeys]['title'].".<br>";
                }
            }
        } else {
            echo "No valid publications collected.<br>";
            echo "<a href='/reftool/v2/'>go back</a><hr>";
        }
//    }
}

// check paper rank
function checkEra2010rank($issn) {
    include 'dbconnect.php';

    $issn = trim($issn, '"');                               //remove quotes from ISSN
    $sqlquery = "SELECT rank FROM reftool.era2010JournalTitleList WHERE CONCAT(ISSN1, ISSN2, ISSN3, ISSN4) LIKE '%$issn%' LIMIT 1;";

    if ($checkEraRank = $conn->query($sqlquery)) {
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
