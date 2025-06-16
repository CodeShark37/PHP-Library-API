<?php

require_once 'models/AuthorModel.php';
require_once 'entities/Author.php';
require_once 'util/Response.php';


class authorController
{
    private $author_model;

    public function __construct()
    {
        $this->author_model = new authorModel();
    }

    public function findAll()
    {
        $authors = $this->author_model->findAll();

        if ($authors) {
            return json_encode($authors);
        } else {
            return Response::sendWithCode(400, 'No authors found');
        }
    }

    public function findById($author_id)
    {
        $author = $this->author_model->findById($author_id);

        if ($author) {
            return $author->toJson();
        } else {
            return Response::sendWithCode(400, 'Author not found');
        }
    }

    public function create($data)
    {
        $author = new author();
        $author->setName($data->name);
        $author->setLastName($data->last_name);
        $author->setCountry($data->country);

        if ($this->author_model->create($author)) {
            return Response::sendWithCode(201, 'New author created');
        } else {
            return Response::sendWithCode(500, 'Error creating author');
        }
    }

    public function update($data)
    {
        $author = new author();
        $author->setId($data->id);
        $author->setName($data->name??null);
        $author->setLastName($data->last_name??null);
        $author->setCountry($data->country??null);

        if ($this->author_model->update($author)) {
            return Response::sendWithCode(200, 'Author updated');
        } else {
            return Response::sendWithCode(500, 'Error updating author');
        }
    }

    public function delete($author_id)
    {   
        if ($this->author_model->delete($author_id)) {
            return Response::sendEmpty(204);
        } else {
            return Response::sendWithCode(500, 'Error deleting author');
        }
    }
}
