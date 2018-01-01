<?php
require_once __DIR__ . '/simplexlsx.class.php';

if ( $xlsx = SimpleXLSX::parse('staff.xlsx')) {
    echo '<h1>$xlsx->rows()</h1>';
    echo '<pre>';
    print_r( $xlsx->rows() );
    echo '</pre>';
    session_start();
    $_SESSION['firstName'] = $xlsx->rows()[1][0];
    $_SESSION['lastName'] = $xlsx->rows()[1][1];
} else {
    echo SimpleXLSX::parse_error();
}

?>
