<?php
class author {
    public $totalOfPublicationsFirstAuthor;
    public $totalOfPublicationsCoAuthor;
    public $ignore;

    public function __construct($firstName, $lastName, $email, $employeeStatus) {
        // if already in DB:
        //      if any new papers
        //          get details to obj
        // if empty(email || employeeStatus)
        //      update it

        $this->firstName = $firstName;
        $this->lastName = $lastName;

        $this->employeeStatus = $employeeStatus;
        $this->email = $email;

        if (!isset($employeeStatus)) {                          // not defined on the spreadsheet
            if (isset($email)) {                                // if email is set
                $domain = explode('@', $email);                 // get the domain
                $domain = array_pop($domain);
                if ($domain=="mdx.ac.uk") {                     // if MDX
                    $this->employeeStatus = "1";                // set as employee - IT MAY BE EX EMPLOYEE !
                }
            } else {                                            // no email and no status from spreadsheet = null
                $this->employeeStatus = "";
            }
        }
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
        return "First name: " . $this->firstName . ". Last name: " . $this->lastName . ". Email: " . $this->email . ". Employee status: " . $this->employeeStatus;
    }
}
?>
