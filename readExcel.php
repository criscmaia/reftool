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
                $eprintsDataJSON = json_encode($eprintsDataObj, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);  // Returns the JSON representation of a value
                echo "<pre>" . $eprintsDataJSON . "</pre><hr>";
            } else {
                echo "No results found for <strong>".$author->getFullName()."</strong><hr>";
            }
        } else {
            echo "JSON for <strong>".$author->getFullName()."</strong> is NOT valid <hr>";
        }



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
