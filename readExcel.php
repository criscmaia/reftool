s<?php
require_once __DIR__ . '/simplexlsx.class.php';
include 'menu.php';

$filePath = $_SESSION['filePath'];

if ( $xlsx = SimpleXLSX::parse($filePath)) {
    echo '<pre>';
    $filteredFile = array_filter(array_map('array_filter', $xlsx->rows()));     // filter out all keys-values that are empty/null/0s
    $removedTitle = array_shift($filteredFile);                                 // array with removed headings from the spreadsheet. can be ignored.

    $totalNames = count($filteredFile);
    $_SESSION['importedNames'] = $filteredFile;                                 // save array names to SESSION so 'collectMdxPapers can access it
    echo '<br><strong>' . $totalNames . ' names found</strong>. <br><a href="/reftool/collectMdxPapers.php">Click here to continue ➔</a><br><br><br>';
    echo '<hr>Full imported content:<br><br>';
    print_r($filteredFile);
} else {
    echo SimpleXLSX::parse_error();
}
