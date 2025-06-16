<?php

class User
{
    private $id;
    private $username;
    private $password;
    private $email;
    private $role_id;
    private $token;

    public function __construct($id = null, $username = null, $password = null, $email = null, $role_id = null)
    {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        $this->role_id = $role_id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getEmail()
    {
        return $this->email;
    }


    public function setRoleId($role_id)
    {
        $this->role_id = $role_id;
    }

    public function getRoleId()
    {
        return $this->role_id;
    }

    public function toJson()
    {
        return json_encode($this->toAssoc());
    }

    public function toAssoc()
    {
        return [
            'id' => $this->getId(),
            'username' => $this->getUsername(),
            'password' => $this->getPassword(),
            'email' => $this->getEmail(),
            'role_id' => $this->getRoleId()
        ];
    }
}
