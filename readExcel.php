<?php
require_once __DIR__ . '/simplexlsx.class.php';
session_start();

if ( $xlsx = SimpleXLSX::parse('staff.xlsx')) {
    echo '<h1>$xlsx->rows()</h1>';
    echo '<pre>';
    print_r( $xlsx->rows() );
    echo '</pre>';
    $_SESSION['importedNames'] = $xlsx->rows();
} else {
    echo SimpleXLSX::parse_error();
}

?>
