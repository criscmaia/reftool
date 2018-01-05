<?php
require_once __DIR__ . '/simplexlsx.class.php';
session_start();

//$filePath = $_SESSION['filePath'];
$filePath = "test.xlsx";

if ( $xlsx = SimpleXLSX::parse($filePath)) {
    $fullFile = $xlsx->rows();
    echo '<pre>';
    print_r( $fullFile );
    echo '</pre><hr>';
//        $filtered = array_filter($fullFile);
//        echo '<pre>';

    var_export(deepFilter($fullFile));

          echo '</pre><hr>';
    echo '<hr>';
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

function deepFilter(array $array)
{
    if (empty($array)) {
        return [];
    }

    $filteredArray = [];
    foreach ($array as $key => $value) {
        if (is_array($value) && !empty($value)) {
            $value = deepFilter($value);
        }
        if (!empty($value)) {
            $filteredArray[$key] = $value;
        }
    }

    return $filteredArray;
}


?>
