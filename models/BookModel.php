<?php

require_once 'util/Database.php';
require_once 'entities/Book.php';

class BookModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function create($book)
    {
        $query = 'INSERT INTO book(title, isbn, year, publisher) VALUES (:title, :isbn, :year, :publisher)';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':title', $book->getTitle());
        $stmt->bindValue(':isbn', $book->getIsbn());
        $stmt->bindValue(':year', $book->getYear());
        $stmt->bindValue(':publisher', $book->getPublisher());

        return $stmt->execute();
    }

    public function update($book)
    {
        // Inicializa o array para armazenar os campos a serem atualizados
        $fields = [];
        $query = 'UPDATE book SET ';

        // Verifica e adiciona cada campo apenas se o valor não for null ou vazio
        if (!empty($book->getTitle())) {
            $fields[] = 'title = :title';
        }
        if (!empty($book->getIsbn())) {
            $fields[] = 'isbn = :isbn';
        }
        if (!empty($book->getYear())) {
            $fields[] = 'year = :year';
        }
        if (!empty($book->getPublisher())) {
            $fields[] = 'publisher = :publisher';
        }
        //nada para actualizar
        if(!$fields) return true;
        $query .= implode(', ', $fields);
        $query .= ' WHERE book_id = :book_id';
        $stmt = $this->db->prepare($query);

        // Vincula os parâmetros
        $stmt->bindValue(':book_id', $book->getId());
        if (!empty($book->getTitle())) {
            $stmt->bindValue(':title', $book->getTitle());
        }
        if (!empty($book->getIsbn())) {
            $stmt->bindValue(':isbn', $book->getIsbn());
        }
        if (!empty($book->getYear())) {
            $stmt->bindValue(':year', $book->getYear());
        }
        if (!empty($book->getPublisher())) {
            $stmt->bindValue(':publisher', $book->getPublisher());
        }

        return $stmt->execute();
    }

    public function delete($book_id)
    {
        $book = $this->findById($book_id);
        if ($book) {
            $query = 'DELETE FROM book WHERE book_id = :book_id';
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':book_id', $book_id, PDO::PARAM_INT);

            return true && $stmt->execute();
        }

        return false;
    }

    public function deleteAll()
    {
        $query = 'TRUNCATE TABLE book';

        return $this->db->query($query);
    }

    public function findAll()
    {
        $query = 'SELECT * FROM book';
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($result) {
            $books = [];
            foreach ($result as $row) {
                $book = new Book($row['book_id'], $row['title'], $row['isbn'], $row['year'], $row['publisher']);
                array_push($books, $book->toAssoc());
            }
            return $books;
        } else {
            return null;
        }
    }

    public function findById($book_id)
    {
        $query = 'SELECT * FROM book WHERE book_id = :book_id';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':book_id', $book_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {            
                return new Book($result['book_id'], $result['title'], $result['isbn'], $result['year'], $result['publisher']);
        } else {
            return null;
        }
    }

    public function findByISBN($book_isbn)
    {
        $query = 'SELECT * FROM book WHERE isbn = :book_isbn';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':book_isbn', $book_isbn);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return new Book($result['book_id'], $result['title'], $result['isbn'], $result['year'], $result['publisher']);
        } else {
            return null;
        }
    }
}