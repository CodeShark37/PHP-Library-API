<?php

require_once 'models/UserModel.php';
require_once 'entities/User.php';
require_once 'util/Response.php';
require_once 'util/TokenHandler.php';
require_once 'config/Constants.php';


class AuthController
{
    private UserModel $user_model;

    public function __construct()
    {
        $this->user_model = new UserModel();
    }

    public function login($data)
    {
        $user = $this->user_model->findByEmailAndPassword($data->email, $data->password);

        if ($user) {
            $token = TokenHandler::getSignedJWTForLogin($user);

            return json_encode([
                    'id' => $user->getId(),
                    'username' => $user->getUsername(),
                    'email' => $user->getEmail(),
                    'token' => $token,
                    'expires' => TokenHandler::getJWTFormatedExpiration($token),
                ]);
        } else {
            return Response::sendWithCode(401, Constants::MSG['LOGIN_ERROR']);
        }
    }
}
