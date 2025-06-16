<?php

require_once 'entities/User.php';
require_once 'models/UserModel.php';
require_once 'util/Response.php';

class userController
{
    private userModel $user_model;

    public function __construct()
    {
        $this->user_model = new UserModel();
    }

    public function findAll()
    {
        $users = $this->user_model->findAll();

        if ($users) {
            return json_encode($users);
        } else {
            return Response::sendWithCode(400, 'No users found');
        }
    }

    public function findById($id)
    {
        $user = $this->user_model->findById($id);

        if ($user) {
            return $user->toJson();
        } else {
            return Response::sendWithCode(400, 'User not found');
        }
    }

    public function create($data)
    {
        $user = new User();
        $user->setUsername($data->username);
        $user->setEmail($data->email);
        $user->setPassword($data->password);

        if ($this->user_model->create($user)) {
            return Response::sendWithCode(201, 'New user created');
        } else {
            return Response::sendWithCode(500, 'Error creating user');
        }
    }

    public function update($data)
    {
        $user = new User();
        $user->setId($data->id);
        $user->setUserName($data->username??null);
        $user->setEmail($data->email??null);
        $user->setPassword($data->password??null);

        if ($this->user_model->update($user)) {
            return Response::sendWithCode(200, 'User updated');
        } else {
            return Response::sendWithCode(500, 'Error updating user');
        }
    }

    public function delete($id)
    {
        $user = new User($id);
        if ($this->user_model->delete($user)) {
            return Response::sendEmpty(204);
        }
        return Response::sendWithCode(500, 'Error deleting user');
    }

}
