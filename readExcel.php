<?php
require_once __DIR__ . '/simplexlsx.class.php';
include_once 'menu.php';
include_once 'ClassAuthor.php';

// get Excel data
$filePath = $_SESSION['filePath'];
if ( $xlsx = SimpleXLSX::parse($filePath)) {
    $filteredFile = array_filter(array_map('array_filter', $xlsx->rows()));         // filter out all keys-values that are empty/null/0s
    $removedTitle = array_shift($filteredFile);                                     // array with removed headings from the spreadsheet. can be ignored.

    foreach($filteredFile as $author) {
        $allAuthors[]= new author($author[0], $author[1], $author[2], $author[3]);  // create object instances and add to the array
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
