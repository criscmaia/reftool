<?php
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

    public function getFullName () {
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
?>
