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
        $result = mb_convert_encoding(file_get_contents($link), 'HTML-ENTITIES', "UTF-8");          // get the data from the ePrints result
        $eprintsDataObj = json_decode($result, true);                                               // Takes a JSON encoded string and converts it into a PHP variable


        if (json_last_error() === JSON_ERROR_NONE) {                                                // if JSON is valid
            if (count($eprintsDataObj)>0) {                                                         // if at least one result is available
//                for ($i = 0; $i < count($eprintsDataObj); $i++) {                                   // go through each paper
                foreach($eprintsDataObj as $keyObj => $valueObj) {
                    // GET TITLE AND DATE 1st BECAUSE IF IT IS EMPTY OR <2014, JUST SKIP
                    if (isset($eprintsDataObj[$i]['date']))         { $date = $eprintsDataObj[$i]['date']; } else { $date = "NULL";}
                    if (isset($eprintsDataObj[$i]['title']))        { $title = $eprintsDataObj[$i]['title']; } else { $title = "NULL";}

                    echo "<p>Date: $date. Title: $title</p>";

                    if ($date != "NULL") {
                        if (strlen($date)==4) {              // only year
                            $date = $date . "-01-01";
                        } else if (strlen($date)==7) {       // only year and month
                            $date = $date . "-01";
                        }
                        $split_date = explode('-',$date);
                        $year = $split_date[0];
                        if ($title=="NULL" || $year<2014){  // remove because title is null OR date is < 2014
                            echo "<p>Removing paper because title is null OR date is < 2014</p>";
//                            print_r($eprintsDataObj[0]);
                            unset($eprintsDataObj[$i]);
                            echo "<h1>".count($eprintsDataObj)."</h1>";
                            continue;
                        }
                    } else {                                // remove because no date is set
                        echo "<h4>Removing paper because no date is set</h4>";
//                        unset($eprintsDataObj[$i]);
                    }


                    foreach($eprintsDataObj[$keyObj] as $key => $value) {                                // go through each author


//                        if () {                                                                     // remove paper if date is null or not >= 2014
//                            unset($eprintsDataObj[$i]);
//                        } else
                        if(strpos($key, 'rioxx2_') === 0 || strpos($key, 'hoa_') === 0  || strpos($key, 'documents') === 0 || strpos($key, 'dates') === 0 || strpos($key, 'files') === 0) {     // remove unnecessary fields from valid papers
                            unset($eprintsDataObj[$i][$key]);
                        }
                    }
                }
                $eprintsDataJSON = json_encode($eprintsDataObj, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);      // Returns the JSON representation of a value
                echo "<pre>" . $eprintsDataJSON . "</pre><hr>";
            } else {
                echo "No results found for <strong>".$author->getFullName()."</strong><hr>";
            }
        } else {
            echo "JSON for <strong>".$author->getFullName()."</strong> is NOT valid <hr>";
        }


//        $eprintsDataJSON

//        $eprintsDataJSON = json_encode($result, true);
//
//
//
//        $eprintsDataObj = json_decode($result, true);
//
//        // check if JSON is valid
//        if (json_last_error() === JSON_ERROR_NONE) {
//            // ignore any paper before 2014
//
//
//
//            // go through each author
//            for ($i = 0; $i < count($eprintsDataObj); $i++) {
//                // go through each paper
//                foreach($eprintsDataObj[$i] as $key => $value) {
//
//
//                    // if null or not >= 2014, remove from json
//                    if () {
//
//                    } else {
//                        // remove unnecessary fields from valid papers
//                        if(strpos($key, 'rioxx2_') === 0 || strpos($key, 'hoa_') === 0  || strpos($key, 'documents') === 0 || strpos($key, 'dates') === 0 || strpos($key, 'files') === 0) {
//                            unset($eprintsDataObj[$i][$key]);
//                        }
//                    }
//                }
//            }
//
//        } else {
//            echo "JSON is NOT valid <br>";
//        }

        /*
        highlight_string("<?php\n\$data =\n" . var_export($eprintsDataObj, true) . ";\n?>");
        */

        echo '<tr>';
            echo '<td>' . $authorsId++ . '</td>';
            echo '<td>' . $author->getFirstName() . '</td>';
            echo '<td>' . $author->getLastName() . '</td>';
            echo '<td>' . '-' . '</td>';
            echo '<td>' . $author->getEmployeeStatus() . '</td>';
            echo '<td>' . $author->totalOfPublicationsFirstAuthor . '</td>';
            echo '<td>' . $author->totalOfPublicationsCoAuthor . '</td>';
        echo '</tr>';
    }
?>
                <tbody>
    </table>
