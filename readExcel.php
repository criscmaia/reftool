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
                <th>Total of Publications - First Author</th>
                <th>Total of Publications - Co-Author</th>
            </tr>
        </thead>
        <tbody>
<?php
    $authorsId = 1;

    // loop all authors from the excel
    foreach($allAuthors as $author) {
        echo "Searching for <strong>".$author->getFullName()."</strong>:<br>";
        $link="http://eprints.mdx.ac.uk/cgi/search/archive/simple/export_mdx_JSON.js?output=JSON&exp=0|1|-|q3:creators_name/editors_name:ALL:EQ:".rawurlencode($author->getFullName());
        $result = mb_convert_encoding(file_get_contents($link), 'HTML-ENTITIES', "UTF-8");     // get the data from the ePrints result
        $papersObj = json_decode($result, true);                                               // Takes a JSON encoded string and converts it into a PHP variable


        if (json_last_error() === JSON_ERROR_NONE) {                                           // if JSON is valid
            if (count($papersObj)>0) {                                                         // if at least one result is available
                foreach($papersObj as $papersObjKeys => $papersObjValues) {                    // go through each paper
                    $papersObjValues = reset($papersObjValues);

                    // GET TITLE AND DATE 1st BECAUSE IF IT IS EMPTY OR <2014, JUST SKIP
                    if (isset($papersObj[$papersObjKeys]['date']))         { $date = $papersObj[$papersObjKeys]['date']; } else { $date = "NULL";}
                    if (isset($papersObj[$papersObjKeys]['title']))        { $title = $papersObj[$papersObjKeys]['title']; } else { $title = "NULL";}

                    if ($date != "NULL") {
                        if (strlen($date)==4) {                                                 // only year
                            $date = $date . "-01-01";
                        } else if (strlen($date)==7) {                                          // only year and month
                            $date = $date . "-01";
                        }
                        $split_date = explode('-',$date);
                        $year = $split_date[0];
                        if ($title=="NULL" || $year<2014){                                      // if title is null OR date is < 2014
                            unset($papersObj[$papersObjKeys]);                                  // remove from obj variable
                            continue;                                                           // go backs to loop without going through the authors below
                        }
                    } else {                                                                    // remove because no date is set
                        unset($papersObj[$papersObjKeys]);
                        continue;                                                               // go backs to loop without going through the authors below
                    }

                    foreach($papersObj[$papersObjKeys]['creators'] as $creatorsKeys => $creatorsValues) {
                        $creatorFullName = ($papersObj[$papersObjKeys]['creators'][$creatorsKeys]['name']['given']." ".$papersObj[$papersObjKeys]['creators'][$creatorsKeys]['name']['family']);
                        echo $author->getFullName()." = ".$creatorFullName."?<br>";
                        if($author->getFullName() == $creatorFullName) {
                            if ($creatorsKeys==0) {
                                $author->totalOfPublicationsFirstAuthor++;
                            } else {
                                $author->totalOfPublicationsCoAuthor++;
                            }
                        }
                    }
                    echo "getTotalOfPublicationsFirstAuthor: ".$author->getTotalOfPublicationsFirstAuthor() . ". getTotalOfPublicationsCoAuthor: " . $author->getTotalOfPublicationsCoAuthor();
                    echo "<hr>";

                    foreach($papersObj[$papersObjKeys] as $key => $value) {                     // for the valid papers, go through each key
                        if(strpos($key, 'rioxx2_') === 0 || strpos($key, 'hoa_') === 0  || strpos($key, 'documents') === 0 || strpos($key, 'dates') === 0 || strpos($key, 'files') === 0) {     // remove unnecessary fields from valid papers
                            unset($papersObj[$papersObjKeys][$key]);
                        }
                    }
                }
                $eprintsDataJSON = json_encode($papersObj, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);      // Returns the JSON representation of a value
            } else {
                echo "No results found for <strong>".$author->getFullName()."</strong><hr>";
            }
        } else {
            echo "JSON for <strong>".$author->getFullName()."</strong> is NOT valid <hr>";
        }

        echo "<p><strong>".count($papersObj)."</strong> valid papers found</p>";
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
            echo '<td>' . (($author->totalOfPublicationsFirstAuthor=='')?'0':$author->totalOfPublicationsFirstAuthor) . '</td>';    // if none = 0
            echo '<td>' . (($author->totalOfPublicationsCoAuthor=='')?'0':$author->totalOfPublicationsCoAuthor) . '</td>';          // if none = 0
        echo '</tr>';
    }
?>
                <tbody>
    </table>
