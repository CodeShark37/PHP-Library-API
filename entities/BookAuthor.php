<?php

class BookAuthor
{
    private $author;
    private $book;
    private $author_order;

    public function __construct($book = null,$author = null, $author_order = null)
    {
        $this->author = $author;
        $this->book = $book;
        $this->author_order = $author_order;
    }

    public function setAuthor($author)
    {
        $this->author = $author;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function setBook($book)
    {
        $this->book = $book;
    }

    public function getBook()
    {
        return $this->book;
    }

    public function setAuthorOrder($author_order)
    {
        $this->author_order = $author_order;
    }

    public function getAuthorOrder()
    {
        return $this->author_order;
    }
    
    public function toJson()
    {
        return json_encode($this->toAssoc());
    }

    public function toAssoc()
    {
        return [
            'author' => ($this->getAuthor())->toAssoc(),
            'book' => ($this->getBook())->toAssoc(),
            'author_order' => $this->getAuthorOrder()
        ];
    }
}
