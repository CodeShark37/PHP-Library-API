<?php

require_once 'models/BookModel.php';
require_once 'models/AuthorModel.php';
require_once 'models/BookAuthorModel.php';
require_once 'entities/Book.php';
require_once 'entities/BookAuthor.php';
require_once 'util/Response.php';


class BookController
{
    private $book_model;
    private $author_model;
    private $book_author_model;


    public function __construct()
    {
        $this->book_model = new BookModel();
        $this->author_model = new AuthorModel();
        $this->book_author_model = new BookAuthorModel();
    }

    public function findAll()
    {
        $books = $this->book_model->findAll();
        
        if ($books) {
            foreach($books as $key=>$book){
                $book_authors = $this->book_author_model->findAuthorsByBookId($book['id']); 
                $books[$key]['authors']= implode(', ',$book_authors);
            }
            return json_encode($books);
        } else {
            return Response::sendWithCode(400, 'No books found');
        }
    }

    public function findById($book_id)
    {
        $book = $this->book_model->findById($book_id);

        if ($book) {
            return $book->toJson();
        } else {
            return Response::sendWithCode(400, 'Book not found');
        }
    }

    public function create($data)
    {
        $book = new Book();
        $book->setTitle($data->title);
        $book->setIsbn($data->isbn);
        $book->setYear($data->year);
        $book->setPublisher($data->publisher);
        $sucess = false;
        
        if (!empty($data->authors_id) && $this->book_model->create($book)) {
            $new_book = $this->book_model->findByISBN($data->isbn);

            foreach ($data->authors_id as $author_order => $author_id) {
                $author = $this->author_model->findById($author_id);
                $book_author = new BookAuthor($new_book,$author,$author_order+1);
                $sucess = $this->book_author_model->create($book_author);
            }
        }
        
        return($sucess)?
            Response::sendWithCode(201, 'New book created'):
            Response::sendWithCode(500, 'Error creating book')
        ;
        
    }

    public function update($data)
    {
        $book = new Book();
        $book->setId($data->id);
        $book->setTitle($data->title??null);
        $book->setIsbn($data->isbn??null);
        $book->setYear($data->year??null);
        $book->setPublisher($data->publisher??null);

        if ($this->book_model->update($book)) {
            if(!empty($data->authors_id)){
                $this->book_author_model->deleteByBookId($data->id);
                $book = $this->book_model->findById($data->id);
                foreach ($data->authors_id as $author_order => $author_id) {
                    $author = $this->author_model->findById($author_id);
                    $book_author = new BookAuthor($book,$author,$author_order+1);
                    $this->book_author_model->create($book_author);
                }
            }
            return Response::sendWithCode(200, 'Book updated');
        } else {
            return Response::sendWithCode(500, 'Error updating book');
        }
    }

    public function delete($book_id)
    {   
        if ($this->book_author_model->deleteByBookId($book_id) &&
            $this->book_model->delete($book_id)){
            return Response::sendEmpty(204);
        } else {
            return Response::sendWithCode(500, 'Error deleting book');
        }
    }
}
