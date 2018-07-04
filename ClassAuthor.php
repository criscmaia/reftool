<?php
class author {
    public $totalOfPublicationsFirstAuthor;
    public $totalOfPublicationsCoAuthor;
    public $repositoryName;                     // manually added by the user - when ePrint name is different from MDX name
    public $ignore;                             // manually chosen to be ignored by the user
    public $mdxAuthorID;
    public $publications = array();                     // id from each publicaiton for this author

    public function __construct($firstName, $lastName, $email, $employeeStatus) {
        echo "<h3>Creating obj: </h3><br>";
        echo $firstName ." - ";

        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->employeeStatus = $employeeStatus;
        $this->email = $email;
        if (!isset($employeeStatus)) {                          // not defined on the spreadsheet
            echo "no employee status - ";
            if (isset($email)) {                                // if email is set
                echo " with email - ";
                $domain = explode('@', $email);                 // get the domain
                $domain = array_pop($domain);
                if ($domain=="mdx.ac.uk") {                     // if MDX
                    $this->employeeStatus = "1";                // set as employee - IT MAY BE EX EMPLOYEE !
                    echo "mdx email ";
                }
            } else {                                            // no email and no status from spreadsheet = null
                echo "no email. empty employee status.";
                $this->employeeStatus = "";
            }
        } else {
            echo "employee: " .$employeeStatus;
        }
        echo "<br><br>";
    }


    public function getFirstName () {
        return $this->firstName;
    }

    public function getLastName () {
        return $this->lastName;
    }

    public function getFullName () {
        return $this->firstName . " " . $this->lastName;
    }

    public function getFullNameReverse () {
        return $this->lastName . ", " . $this->firstName;
    }

    public function getMdxAuthorID() {
        return $this->mdxAuthorID;
    }

    public function getRepositoryName() {
        return $this->repositoryName;
    }

    public function getEmail () {
        return $this->email;
    }

    public function getEmployeeStatus () {
        return $this->employeeStatus;
    }

    public function getTotalOfPublicationsFirstAuthor () {
        return $this->totalOfPublicationsFirstAuthor;
    }

    public function getTotalOfPublicationsCoAuthor () {
        return $this->totalOfPublicationsCoAuthor;
    }

    public function printAll () {
        return "Author ID:" . $this->mdxAuthorID . ": First name: " . $this->firstName . ". Last name: " . $this->lastName . ". Email: " . $this->email . ". Employee status: " . $this->employeeStatus;
    }
}
?>
