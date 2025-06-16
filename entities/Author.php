<?php

class Author
{
    private $author_id;
    private $name;
    private $last_name;
    private $country;


    public function __construct($author_id = null, $name = null, $last_name = null,$country=null)
    {
        $this->author_id = $author_id;
        $this->name = $name;
        $this->last_name = $last_name;
        $this->country = $country;
    }

    public function setId($author_id)
    {
        $this->author_id = $author_id;
    }

    public function getId()
    {
        return $this->author_id;
    }

    public function setname($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getFullName()
    {
        return $this->getName().' '.$this->getLastName();
    }

    public function setLastName($last_name)
    {
        $this->last_name = $last_name;
    }

    public function getLastName()
    {
        return $this->last_name;
    }

    public function setCountry($country)
    {
        $this->country = $country;
    }

    public function getCountry()
    {
        return $this->country;
    }
    
    public function toJson()
    {
        return json_encode($this->toAssoc());
    }

    public function toAssoc()
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getFullName(),
            'country' => $this->getCountry()
        ];
    }
}
