<?php

require_once 'util/Database.php';
require_once 'entities/Author.php';

class AuthorModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function create($author)
    {
        $query = 'INSERT INTO author(name, last_name, country) VALUES (:name, :last_name, :country)';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':name', $author->getName());
        $stmt->bindValue(':last_name', $author->getLastName());
        $stmt->bindValue(':country', $author->getCountry());

        return $stmt->execute();
    }

    public function update($author)
    {
        // Inicializa o array para armazenar os campos a serem atualizados
        $fields = [];
        $query = 'UPDATE author SET ';

        // Verifica e adiciona cada campo apenas se o valor não for null ou vazio
        if (!empty($author->getName())) {
            $fields[] = 'name = :name';
        }
        if (!empty($author->getLastName())) {
            $fields[] = 'last_name = :last_name';
        }
        if (!empty($author->getCountry())) {
            $fields[] = 'country = :country';
        }
        //nada para actualizar
        if(!$fields) return true;
        $query .= implode(', ', $fields);
        $query .= ' WHERE author_id = :author_id';
        $stmt = $this->db->prepare($query);

        // Vincula os parâmetros
        $stmt->bindValue(':author_id', $author->getId());
        if (!empty($author->getName())) {
            $stmt->bindValue(':name', $author->getName());
        }
        if (!empty($author->getLastName())) {
            $stmt->bindValue(':last_name', $author->getLastName());
        }
        if (!empty($author->getCountry())) {
            $stmt->bindValue(':country', $author->getCountry());
        }

        return $stmt->execute();
    }

    public function delete($author_id)
    {
        $author = $this->findById($author_id);
        if ($author) {
            $query = 'DELETE FROM author WHERE author_id = :author_id';
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':author_id', $author_id, PDO::PARAM_INT);

            return true && $stmt->execute();
        }

        return false;
    }

    public function deleteAll()
    {
        $query = 'TRUNCATE TABLE author';

        return $this->db->query($query);
    }

    public function findAll()
    {
        $query = 'SELECT * FROM author';
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($result) {
            $authors = [];
            foreach ($result as $row) {
                $author = new Author($row['author_id'], $row['name'], $row['last_name'], $row['country']);
                array_push($authors, $author->toAssoc());
            }
            return $authors;
        } else {
            return null;
        }
    }

    public function findById($author_id)
    {
        $query = 'SELECT * FROM author WHERE author_id = :author_id';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':author_id', $author_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {            
                return new Author($result['author_id'], $result['name'], $result['last_name'], $result['country']);
        } else {
            return null;
        }
    }
}