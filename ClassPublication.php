<?php
class publication {
    public $projectID;
    public $publicationID;
    public $type;
    public $author;
    public $succeeds;
    public $title;
    public $isPublished;
    public $presType;
    public $keywords;
    public $publication;
    public $volume;
    public $number;
    public $publisher;
    public $eventTitle;
    public $eventType;
    public $isbn;
    public $issn;
    public $bookTitle;
    public $ePrintID;
    public $doi;
    public $uri;
    public $additionalInfo;
    public $abstract;
    public $date;
    public $eraRating;

    public function getProjectID() {
        return $this->projectID;
    }

    public function getPublicationID() {
        return $this->publicationID;
    }

    public function getType() {
        return $this->type;
    }

    public function getAuthor() {
        return $this->author;
    }

    public function getSucceeds() {
        return $this->succeeds;
    }
    public function getTitle() {
        return $this->title;
    }

    public function getIsPublished() {
        return $this->isPublished;
    }

    public function getPresType() {
        return $this->presType;
    }

    public function getKeywords() {
        return $this->keywords;
    }

    public function getPublication() {
        return $this->publication;
    }

    public function getVolume() {
        return $this->volume;
    }

    public function getNumber() {
        return $this->number;
    }

    public function getPublisher() {
        return $this->publisher;
    }

    public function getEventTitle() {
        return $this->eventTitle;
    }

    public function getEventType() {
        return $this->eventType;
    }

    public function getIsbn() {
        return $this->isbn;
    }

    public function getIssn() {
        return $this->issn;
    }

    public function getBookTitle() {
        return $this->bookTitle;
    }

    public function getEPrintID() {
        return $this->ePrintID;
    }

    public function getDoi() {
        return $this->doi;
    }

    public function getUri() {
        return $this->uri;
    }

    public function getAdditionalInfo() {
        return $this->additionalInfo;
    }

    public function getAbstract() {
        return $this->abstract;
    }

    public function getDate() {
        return $this->date;
    }

    public function getEraRating() {
        return $this->eraRating;
    }
}
?>
