<?php
require_once __DIR__ . '/simplexlsx.class.php';
session_start();

$filePath = $_SESSION['filePath'];

if ( $xlsx = SimpleXLSX::parse($filePath)) {
        $totalNames = count($xlsx->rows());
//        echo '<h1>$xlsx->rows()</h1>';
        echo '<pre>';
        print_r( $xlsx->rows() );
        echo '</pre>';
        $_SESSION['importedNames'] = $xlsx->rows();     // save array names to SESSION so 'collectMdxPapers can access it [cm-18.01.01]
        echo $totalNames . ' names found. <a href="/reftool/collectMdxPapers.php"> Click here to continue ➔</a>';
} else {
    echo SimpleXLSX::parse_error();
}

?>
