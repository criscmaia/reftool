<?php
require_once __DIR__ . '/simplexlsx.class.php';
session_start();

$filePath = $_SESSION['filePath'];
//$filePath = "test.xlsx";

if ( $xlsx = SimpleXLSX::parse($filePath)) {
    echo '<pre>';
    $filteredFile = array_filter(array_map('array_filter', $xlsx->rows()));     // filter out all keys-values that are empty/null
    print_r($filteredFile);
    $totalNames = count($filteredFile);
    $_SESSION['importedNames'] = $filteredFile;                                 // save array names to SESSION so 'collectMdxPapers can access it
    echo $totalNames . ' names found. <a href="/reftool/collectMdxPapers.php"> Click here to continue ➔</a>';
} else {
    echo SimpleXLSX::parse_error();
}
