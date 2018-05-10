<?php
require_once __DIR__ . '/simplexlsx.class.php';
include 'menu.php';

class author {
    public $totalOfPublicationsFirstAuthor;
    public $totalOfPublicationsCoAuthor;
    public $ignore;

    public function __construct($firstName, $lastName, $email, $employeeStatus) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->employeeStatus = $employeeStatus;
    }

    public function getFirstName () {
        return $this->firstName;
    }

    public function getLastName () {
        return $this->lastName;
    }

    public function getFullname () {
        return $this->firstName . " " . $this->lastName;
    }

    public function getEmail () {
        return $this->email;
    }

    public function getEmployeeStatus () {
        return $this->employeeStatus;
    }

    public function printAll () {
        return "First name: " . $this->firstName . ". Last name: " . $this->lastName . ". Email: " . $this->email . ". Employee status: " . $this->employeeStatus;
    }
}



$filePath = $_SESSION['filePath'];

if ( $xlsx = SimpleXLSX::parse($filePath)) {
    $filteredFile = array_filter(array_map('array_filter', $xlsx->rows()));     // filter out all keys-values that are empty/null/0s
    $removedTitle = array_shift($filteredFile);                                 // array with removed headings from the spreadsheet. can be ignored.

    foreach($filteredFile as $author) {
        $allAuthors[]= new author($author[0], $author[1], $author[2], $author[3]);
    }

    foreach($allAuthors as $author) {
        echo $author->printAll() . "<br>";
    }

    $_SESSION['importedNames'] = $allAuthors;                                     // save array with all Authors object instance to SESSION so 'collectMdxPapers can access it

//    $totalNames = count($filteredFile);
//    $_SESSION['importedNames'] = $filteredFile;                                 // save array names to SESSION so 'collectMdxPapers can access it
//    echo '<br><strong>' . $totalNames . ' names found</strong>. <br><a href="/reftool/collectMdxPapers.php">Click here to continue ➔</a><br><br><br>';
//    echo '<hr>Full imported content:<br><br>';
//    print_r($filteredFile);
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
?>
<tbody>
</table>
