<?php

require_once 'util/Database.php';
require_once 'entities/BookAuthor.php';
require_once 'models/BookModel.php';
require_once 'models/AuthorModel.php';


class BookAuthorModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function create($book_author)
    {
        $query = 'INSERT INTO book_author(book_id, author_id, author_order) VALUES (:book_id, :author_id, :author_order)';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':book_id', ($book_author->getBook())->getId(),PDO::PARAM_INT);
        $stmt->bindValue(':author_id', ($book_author->getAuthor())->getId(),PDO::PARAM_INT);
        $stmt->bindValue(':author_order', $book_author->getAuthorOrder(),PDO::PARAM_INT);

        return $stmt->execute();
    }   

   /* analysing 
   public function update($book_author)
    {
        $fields = [];
        $query = 'UPDATE book_author SET ';

        $fields[] = 'book_id = :book_id';
        $fields[] = 'author_id = :author_id';
        // Verifica e adiciona cada campo apenas se o valor não for null
        if (!empty($book_author->getAuthorOrder())) {
            $fields[] = 'author_order = :author_order';
        }

        $query .= implode(', ', $fields);
        $query .= ' WHERE book_id = :book_id AND author_id = :author_id';
        $stmt = $this->db->prepare($query);

        // Vincula os parâmetros
            $stmt->bindValue(':book_id', ($book_author->getBook())->getId(),PDO::PARAM_INT);
            $stmt->bindValue(':author_id', ($book_author->getAuthor())->getId(),PDO::PARAM_INT);
        
        if (!empty($book_author->getAuthorOrder())) {
            $stmt->bindValue(':author_order', $book_author->getAuthorOrder(),PDO::PARAM_INT);
        }

        return $stmt->execute();
    }*/

    public function deleteByBookId($book_id)
    {
        $query = 'DELETE FROM book_author WHERE book_id = :book_id';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':book_id', $book_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteByAuthorId($author_id)
    {
        $query = 'DELETE FROM book_author WHERE author_id = :author_id';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':author_id', $book_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteAll()
    {
        $query = 'TRUNCATE TABLE book_author';

        return $this->db->query($query);
    }

    public function findAll()
    {
        $query = 'SELECT * FROM book_author';
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($result) {
            $book_authors = [];
            foreach ($result as $row) {
                $book = (new BookModel())->findById($row['book_id']); 
                $author = (new AuthorModel())->findById($row['author_id']); 
                $book_author = new BookAuthor($book, $author, $row['author_order']);
                array_push($book_authors, $book_author->toAssoc());
            }
            return $book_authors;
        } else {
            return null;
        }
    }

    public function findAuthorsByBookId($book_id)
    {
        $query = 'SELECT author_id FROM book_author WHERE book_id = :book_id ORDER BY author_order';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':book_id', $book_id);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($result) {   
            $book_authors = [];
            foreach ($result as $key => $row) {  
                $author = (((new AuthorModel())->findById($row['author_id']))->toAssoc())['name'];             
                array_push($book_authors, $author);
            }
            return $book_authors;
        } else {
            return null;
        }
    }

    public function findBooksByAuthorId($author_id)
    {
        $query = 'SELECT book_id,author_order FROM book_author WHERE author_id = :author_id';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':author_id', $author_id);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($result) { 
            $author_books = [];
            foreach ($result as $row) {         
                $book = (((new BookModel())->findById($row['book_id']))->toAssoc())['title'];
                array_push($author_books, $book);
            }
            return $author_books;
        } else {
            return null;
        }
    }
}