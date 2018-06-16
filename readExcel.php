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
    foreach($allAuthors as $author) {
//        echo $author->printAll() . "<br><br>";

        // get total of publications per author
        $link="http://eprints.mdx.ac.uk/cgi/search/archive/simple/export_mdx_JSON.js?output=JSON&exp=0|1|-|q3:creators_name/editors_name:ALL:EQ:".rawurlencode($author->getFullName());
//        echo "link: <pre>" . $link . "</pre><hr>";

        $result = mb_convert_encoding(file_get_contents($link), 'HTML-ENTITIES', "UTF-8");
//        echo "result: <pre>" . $result . "</pre><hr>";

        // removing extra comma after the last creator array value
        // tests working: Peter Moore, Almaas Ali, Cristiano Maia, Balbir Barn, Peter Moore + Almaas Ali + Cristiano Maia
        $search = "/" . "\"\s+\}\,\s+\}" . "/";  // ending quote for family, break line, bracket and comma, break line, another bracket
        $resultCleaned = preg_replace($search, "\"\n}\n}", $result);
//        echo "resultCleaned: <pre>" . $resultCleaned . "</pre><hr>";


        $json_str = $resultCleaned;
        $json = json_decode($json_str);
//        echo "json: <pre>" . $json . "</pre><hr>";
        $jsonData = json_encode($json, JSON_PRETTY_PRINT);
//        echo "jsonData: <pre>" . $jsonData . "</pre><hr>";


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
