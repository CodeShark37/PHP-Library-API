<?php

class Book
{
    private $book_id;
    private $title;
    private $isbn;
    private $year;
    private $publisher;
    private $authors;

    public function __construct($book_id = null, $title = null, $isbn = null, $year = null, $publisher = null,$authors=null)
    {
        $this->book_id = $book_id;
        $this->title = $title;
        $this->isbn = $isbn;
        $this->year = $year;
        $this->publisher = $publisher;
        $this->authors = $authors;
    }

    public function setId($book_id)
    {
        $this->book_id = $book_id;
    }

    public function getId()
    {
        return $this->book_id;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setIsbn($isbn)
    {
        $this->isbn = $isbn;
    }

    public function getIsbn()
    {
        return $this->isbn;
    }

    public function setYear($year)
    {
        $this->year = $year;
    }

    public function getYear()
    {
        return $this->year;
    }

    public function setPublisher($publisher)
    {
        $this->publisher = $publisher;
    }

    public function getPublisher()
    {
        return $this->publisher;
    }

    public function setAuthors($publisher)
    {
        $this->publisher = $publisher;
    }

    public function getAuthors()
    {
        return $this->publisher;
    }


    public function toJson()
    {
        return json_encode($this->toAssoc());
    }

    public function toAssoc()
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'isbn' => $this->getIsbn(),
            'year' => $this->getYear(),
            'publisher' => $this->getPublisher()
        ];
    }
}
