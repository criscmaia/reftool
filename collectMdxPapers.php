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

    $searchedAuthor = json_decode($publications, true);                 // Takes a JSON encoded string and converts it into a PHP variable
    if (json_last_error() === JSON_ERROR_NONE) {                        // if JSON is valid
        if (count($searchedAuthor)>0) {
            $eraRating = "NULL";

            foreach($searchedAuthor as $searchedAuthorKeys => $searchedAuthorPublications) {                     // see all the authors
                foreach($searchedAuthor[$searchedAuthorKeys] as $publicationKey => $publicationDetails) {        // go through the author's publications

//                        `projectID`,
//                        `publicationID`,
//                    `type`,
//                        `author`,
//                    `succeeds`,
//                    `title`,
//                    `isPublished`,
//                    `presType`,
//                    `keywords`,
//                    `publication`,
//                    `volume`,
//                    `number`,
//                    `publisher`,
//                    `eventTitle`,
//                    `eventType`,
//                    `isbn`,
//                    `issn`,
//                    `bookTitle`,
//                    `ePrintID`,
//                    `doi`,
//                    `uri`,
//                        `additionalInfo`,
//                    `abstract`,
//                    `date`,
//                    `eraRating`

                    if (isset($searchedAuthor[$searchedAuthorKeys][$publicationKey]['type']))         { $type        = '"'.addslashes($searchedAuthor[$searchedAuthorKeys][$publicationKey]['type']).'"'; }
                    if (isset($searchedAuthor[$searchedAuthorKeys][$publicationKey]['succeeds']))     { $succeeds    = $searchedAuthor[$searchedAuthorKeys][$publicationKey]['succeeds']; } else { $succeeds = "NULL";}
                    if (isset($searchedAuthor[$searchedAuthorKeys][$publicationKey]['title']))        { $title = '"'.addslashes($searchedAuthor[$searchedAuthorKeys][$publicationKey]['title']).'"'; } else { $title = "NULL";}
                    if (isset($searchedAuthor[$searchedAuthorKeys][$publicationKey]['ispublished']))  { $ispublished = '"'.addslashes($searchedAuthor[$searchedAuthorKeys][$publicationKey]['ispublished']).'"'; } else { $ispublished = "NULL";}
                    if (isset($searchedAuthor[$searchedAuthorKeys][$publicationKey]['pres_type']))    { $presType    = '"'.addslashes($searchedAuthor[$searchedAuthorKeys][$publicationKey]['pres_type']).'"'; } else { $presType = "NULL";}
                    if (isset($searchedAuthor[$searchedAuthorKeys][$publicationKey]['keywords']))     { $keywords    = '"'.addslashes($searchedAuthor[$searchedAuthorKeys][$publicationKey]['keywords']).'"'; } else { $keywords = "NULL";}
                    if (isset($searchedAuthor[$searchedAuthorKeys][$publicationKey]['publication']))  { $publication = '"'.addslashes($searchedAuthor[$searchedAuthorKeys][$publicationKey]['publication']).'"'; } else { $publication = "NULL";}
                    if (isset($searchedAuthor[$searchedAuthorKeys][$publicationKey]['volume']))       { $volume      = '"'.addslashes($searchedAuthor[$searchedAuthorKeys][$publicationKey]['volume']).'"'; } else { $volume = "NULL";}
                    if (isset($searchedAuthor[$searchedAuthorKeys][$publicationKey]['number']))       { $number      = '"'.addslashes($searchedAuthor[$searchedAuthorKeys][$publicationKey]['number']).'"'; } else { $number = "NULL";}
                    if (isset($searchedAuthor[$searchedAuthorKeys][$publicationKey]['publisher']))    { $publisher   = '"'.addslashes($searchedAuthor[$searchedAuthorKeys][$publicationKey]['publisher']).'"'; } else { $publisher = "NULL";}
                    if (isset($searchedAuthor[$searchedAuthorKeys][$publicationKey]['event_title']))  { $eventTitle  = '"'.addslashes($searchedAuthor[$searchedAuthorKeys][$publicationKey]['event_title']).'"'; } else { $eventTitle = "NULL";}
                    if (isset($searchedAuthor[$searchedAuthorKeys][$publicationKey]['event_type']))   { $eventType   = '"'.addslashes($searchedAuthor[$searchedAuthorKeys][$publicationKey]['event_type']).'"'; } else { $eventType = "NULL";}
                    if (isset($searchedAuthor[$searchedAuthorKeys][$publicationKey]['isbn']))         { $isbn        = '"'.addslashes($searchedAuthor[$searchedAuthorKeys][$publicationKey]['isbn']).'"'; } else { $isbn = "NULL";}
                    if (isset($searchedAuthor[$searchedAuthorKeys][$publicationKey]['issn']))         { $issn        = '"'.addslashes($searchedAuthor[$searchedAuthorKeys][$publicationKey]['issn']).'"'; } else { $issn = "NULL";}
                    if (isset($searchedAuthor[$searchedAuthorKeys][$publicationKey]['book_title']))   { $bookTitle   = '"'.addslashes($searchedAuthor[$searchedAuthorKeys][$publicationKey]['book_title']).'"'; } else { $bookTitle = "NULL";}
                    if (isset($searchedAuthor[$searchedAuthorKeys][$publicationKey]['eprintid']))     { $eprintid    = $searchedAuthor[$searchedAuthorKeys][$publicationKey]['eprintid']; } else { $eprintid = "NULL";}
                    if (isset($searchedAuthor[$searchedAuthorKeys][$publicationKey]['official_url'])) { $doi         = '"'.addslashes($searchedAuthor[$searchedAuthorKeys][$publicationKey]['official_url']).'"'; } else { $doi = "NULL";}
                    if (isset($searchedAuthor[$searchedAuthorKeys][$publicationKey]['uri']))          { $uri         = '"'.addslashes($searchedAuthor[$searchedAuthorKeys][$publicationKey]['uri']).'"'; } else { $uri = "NULL";}
                    if (isset($searchedAuthor[$searchedAuthorKeys][$publicationKey]['abstract']))     { $abstract    = '"'.addslashes($searchedAuthor[$searchedAuthorKeys][$publicationKey]['abstract']).'"'; } else { $abstract = "NULL";}
                    if (isset($searchedAuthor[$searchedAuthorKeys][$publicationKey]['date']))         { $date = $searchedAuthor[$searchedAuthorKeys][$publicationKey]['date']; } else { $date = "NULL";}
                    if ($issn != "NULL")  /* check ERA from issn */                                   { $eraRating = checkEra2010rank($issn); }

                    if (isset($searchedAuthor[$searchedAuthorKeys][$publicationKey]['creators']))     { $allcreators = $searchedAuthor[$searchedAuthorKeys][$publicationKey]['creators']; } else { $allcreators = "NULL";}                          // minor scenarios where creator is null

                    echo "$type - $succeeds - $title - $ispublished - $presType - $keywords - $publication - $volume - $number - $publisher - $eventTitle - $eventType - $isbn - $issn - $bookTitle - $doi - $uri - $abstract - $date - $eraRating - ".print_r($allcreators)."<br><hr>";

                }
            }
        }
    } else {
        echo "Invalid JSON. <br>";
    }
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
