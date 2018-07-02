<?php
require_once __DIR__ . '/simplexlsx.class.php';
include_once 'menu.php';
require_once 'ClassAuthor.php';
require_once 'dbconnect.php';

// get Excel data
$filePath = $_SESSION['filePath'];
if ( $xlsx = SimpleXLSX::parse($filePath)) {
    $filteredFile = array_filter(array_map('array_filter', $xlsx->rows()));         // filter out all keys-values that are empty/null/0s
    $removedTitle = array_shift($filteredFile);                                     // array with removed headings from the spreadsheet. can be ignored.

    foreach($filteredFile as $author) {
        // create object instances and add to the array
        $allAuthors[]= new author(
            isset($author[0])?$author[0]:null,                              // First Name
            isset($author[1])?$author[1]:null,                              // Last Name
            isset($author[2])?strtolower($author[2]):null,                  // Email
            isset($author[3])?((strtolower($author[3])=="y")?1:0):null      // Current employee? - converts Y/y to 1, or anything else to 0
        );
    }

    $_SESSION['importedNames'] = $allAuthors;                                       // save array with all Authors object instance to SESSION so 'collectMdxPapers can access it
} else {
    echo SimpleXLSX::parse_error();
}

?>
    <table id="importedList">
        <thead>
            <tr style="text-align: left">
                <th>id</th>
                <th>First name</th>
                <th>Last name</th>
                <th>✔</th>
                <th>Employee Status</th>
                <th>Total of Publications</th>
                <th>Total of Publications - First Author</th>
                <th>Total of Publications - Co-Author</th>
            </tr>
        </thead>
        <tbody>
<?php
    function startsWith($haystack, $needle) {
        // search backwards starting from haystack length characters from the end
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
    }

    function endsWith($haystack, $needle) {
        // search forward starting from end minus needle length characters
        return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
    }


    $authorsId = 1;

    // loop all authors from the excel
    foreach($allAuthors as $author) {
        /*
            Do project details
        */
        $author->mdxAuthorID = checkIfMdxAuthorIsOnDB($projectDetails, $author);        // get DB id value and assign to object
//        echo $author->printAll()."<br>";

        /*
        if authors has had a repository name manually added
        gets the name that should be on ePrint from this system DB
        and use it to be the main search piece
        */
        $searchingName = "";
        $sql = "SELECT repositoryName FROM reftool.mdxAuthor WHERE firstName=\"".$author->getFirstName()."\" AND lastName=\"".$author->getLastName()."\"";
//        echo $sql."<br>";
        $result = $conn->query($sql);
        if (!$result) {
            trigger_error('Error in: '.$sql.'<br><br>Invalid query: ' . $conn->error);
        } else if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $author->repositoryName = $row["repositoryName"];
//                echo $row["repositoryName"]."<br>";
            }
        }

        // define what is going to be the search variable
        ($author->getRepositoryName() == NULL)?($searchingName = $author->getFullNameReverse()):($searchingName = $author->getRepositoryName());

        echo "Searching for <strong>".$searchingName."</strong>... <br />";
        $link="http://eprints.mdx.ac.uk/cgi/search/archive/simple/export_mdx_JSON.js?output=JSON&exp=0|1|-|q3:creators_name/editors_name:ALL:EQ:".rawurlencode($searchingName);

//        echo $link . "<br>";


        $result = mb_convert_encoding(file_get_contents($link), 'HTML-ENTITIES', "UTF-8");     // get the data from the ePrints result


        $papersObj = json_decode($result, true);                                               // Takes a JSON encoded string and converts it into a PHP variable


        if (json_last_error() === JSON_ERROR_NONE) {                                           // if JSON is valid
            if (count($papersObj)>0) {                                                         // if at least one result is available
                foreach($papersObj as $papersObjKeys => $papersObjValues) {                    // go through each paper
                    $papersObjValues = reset($papersObjValues);

                    // GET TITLE AND DATE 1st BECAUSE IF IT IS EMPTY OR <2014, JUST SKIP
                    if (isset($papersObj[$papersObjKeys]['date']))         { $date = $papersObj[$papersObjKeys]['date']; } else { $date = "NULL";}
                    if (isset($papersObj[$papersObjKeys]['title']))        { $title = $papersObj[$papersObjKeys]['title']; } else { $title = "NULL";}
                    if (isset($papersObj[$papersObjKeys]['creators']))     { $creators = $papersObj[$papersObjKeys]['creators']; } else { $creators = "NULL";}

                    if ($date != "NULL") {
                        if (strlen($date)==4) {                                                 // only year
                            $date = $date . "-01-01";
                        } else if (strlen($date)==7) {                                          // only year and month
                            $date = $date . "-01";
                        }
                        $split_date = explode('-',$date);
                        $year = $split_date[0];
                        if ($title=="NULL" || $creators=="NULL" || $year<2014){                 // if title/creators/date is null OR date is < 2014
                            unset($papersObj[$papersObjKeys]);                                  // remove from obj variable
                            continue;                                                           // go backs to loop without going through the authors below
                        }
                    } else {                                                                    // remove because no date is set
                        unset($papersObj[$papersObjKeys]);
                        continue;                                                               // go backs to loop without going through the authors below
                    }

                    foreach($papersObj[$papersObjKeys]['creators'] as $creatorsKeys => $creatorsValues) {       // for each author of each paper
                        if isset($papersObj[$papersObjKeys]['creators'][$creatorsKeys]['name']) {               // if name IS set to creator - "Yang, Xin-She" is not, as example
                            $creatorFullName = ($papersObj[$papersObjKeys]['creators'][$creatorsKeys]['name']['given']." ".$papersObj[$papersObjKeys]['creators'][$creatorsKeys]['name']['family']);        // get the creator full name
//                          echo $searchingName ." - ".$creatorFullName."? creatorsKeys: $creatorsKeys <br><hr>";
                            if(startsWith($creatorFullName, $author->getFirstName()) && endsWith($creatorFullName, $author->getLastName())) {        // double check if author is one of the creators
//                              echo "true <br>";
                                if ($creatorsKeys==0) {                                                             // if first authors
                                    $author->totalOfPublicationsFirstAuthor++;
                                } else {                                                                            // if co-author
                                    $author->totalOfPublicationsCoAuthor++;
                                }
                            }
                        }
                    }

                    foreach($papersObj[$papersObjKeys] as $key => $value) {                     // for the valid papers, go through each key
                        if(strpos($key, 'rioxx2_') === 0 || strpos($key, 'hoa_') === 0  || strpos($key, 'documents') === 0 || strpos($key, 'dates') === 0 || strpos($key, 'files') === 0) {     // remove unnecessary fields from valid papers
                            unset($papersObj[$papersObjKeys][$key]);
                        }
                    }
                }
                $eprintsDataJSON = json_encode($papersObj, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);      // Returns the JSON representation of a value
            } else {
                echo "No results found for <strong>".$searchingName."</strong><br>";
            }
        } else {
            echo "JSON for <strong>".$searchingName."</strong> is NOT valid <hr>";
        }

//        echo "<strong>".count($papersObj)."</strong> valid papers found.</p>";
        $_SESSION['publicationDetails'] = $eprintsDataJSON;
//        echo "<pre>" . $eprintsDataJSON . "</pre><hr>";


        /*
        highlight_string("<?php\n\$data =\n" . var_export($eprintsDataJSON, true) . ";\n?>");
        */

        echo '<tr>';
            echo '<td>' . $authorsId++ . '</td>';
            echo '<td>' . $author->getFirstName() . '</td>';
            echo '<td>' . $author->getLastName() . '</td>';
            echo '<td>' . '-' . '</td>';
            echo '<td>' . (($author->getEmployeeStatus()!=='')?(($author->getEmployeeStatus()==1)?'Y':'N'):'') . '</td>';           // if unknown, leaves blank. if 1=Y, else=N
            echo '<td>' . ($author->totalOfPublicationsFirstAuthor+$author->totalOfPublicationsCoAuthor) . '</td>';                 // if none = 0
            echo '<td>' . (($author->totalOfPublicationsFirstAuthor=='')?'0':$author->totalOfPublicationsFirstAuthor) . '</td>';    // if none = 0
            echo '<td>' . (($author->totalOfPublicationsCoAuthor=='')?'0':$author->totalOfPublicationsCoAuthor) . '</td>';          // if none = 0
        echo '</tr>';
    }

function checkIfMdxAuthorIsOnDB($projectDetails, $localAuthor){
    include 'dbconnect.php';
    $fullName = $localAuthor->getFirstName() . ' ' . $localAuthor->getLastName();
    $query = "SELECT * FROM mdxAuthor WHERE projectID = $projectDetails[0] AND CONCAT(firstName, ' ', lastName) LIKE \"%$fullName%\";";

//    echo $query."<br>";
    $result = $conn->query($query);
    if (!$result) {
        trigger_error('Error in: '.$query.'<br><br>Invalid query: ' . $conn->error);
    } else if ($result->num_rows > 0) {                                                                                             // author IS on the DB
        while($row = $result->fetch_assoc()) {
//            print_r($row);
            if ($row['email'] != $localAuthor->getEmail() ||  $row['currentEmployee'] != $localAuthor->getEmployeeStatus()) {       // check if email or current employee is different
                echo "email or current employee on the spreadsheet is different from the DB. Overwritting it... <br>";
                if ($localAuthor->getEmployeeStatus()=='') {
                    $sqlUpdate = "UPDATE `mdxAuthor` SET `email` = \"".$localAuthor->getEmail()."\" WHERE `mdxAuthorID` = ".$row['mdxAuthorID'].";";
                } else {
                    $sqlUpdate = "UPDATE `mdxAuthor` SET `email` = \"".$localAuthor->getEmail()."\", `currentEmployee` = ".$localAuthor->getEmployeeStatus()." WHERE `mdxAuthorID` = ".$row['mdxAuthorID'].";";
                }

                $result = $conn->query($sqlUpdate);
                if ($result) {
                    echo "Overwritten! $sqlUpdate <br>";
                } else {
                    echo "Error: " . $sqlUpdate . "<br>" . $conn->error;
                }
                $conn->close();
            } else {
//                echo "email AND current employee on the spreadsheet are the same from the DB. Nothing changes. <br>";
            }
            return $row['mdxAuthorID'];                                                                                             // return author DB id
//            echo $row['mdxAuthorID']."<br>";
        }
    } else if ($result->num_rows == 0) {                                                                                            // author is NOT on the DB
//        echo "Should show first name if it can access the objs: ".$localAuthor->getFirstName()."<br>";
        if ($localAuthor->getEmployeeStatus()=='') {
            $sql = "INSERT INTO `mdxAuthor` (`projectID`,`firstName`,`lastName`,`email`) VALUES ('$projectDetails[0]', \"".$localAuthor->getFirstName()."\",\"".$localAuthor->getLastName()."\",\"".$localAuthor->getEmail()."\");";
        } else {
            $sql = "INSERT INTO `mdxAuthor` (`projectID`,`firstName`,`lastName`,`email`,`currentEmployee`) VALUES ('$projectDetails[0]', \"".$localAuthor->getFirstName()."\",\"".$localAuthor->getLastName()."\",\"".$localAuthor->getEmail()."\",".$localAuthor->getEmployeeStatus().");";
        }

        if ($conn->query($sql) === TRUE) {
            $last_id = $conn->insert_id;
            echo "New record created successfully. ID: ". $last_id. " - fullName: ".$fullName. "<br>";
            return $last_id;                                                                                                        // return newly created author DB id
        } else {
            echo "<p>Error: " . $sql . "<br>" . $conn->error . "</p>";
        }
    }
    $conn->close();
}

?>
                <tbody>
    </table>
<a href="/reftool/v2/collectMdxPapers.php">Collect papers from selected authors --> </a>
