<?php

require_once 'entities/User.php';
require_once 'util/Database.php';
require_once 'util/rbac/Role.php';
require_once 'util/rbac/RoleModelHandler.php';

class UserModel
{
    private $db;
    private $role_handler;

    public function __construct()
    {
        $this->db = Database::getConnection();
        $this->role_handler = new RoleModelHandler($this->db);
    }

    public function create($user)
    {
        $userExist = $this->getIdByEmail($user->getEmail());
        //check if user already exists
        if (!$userExist) {
            $userRoleName = strtolower($user->getEmail());
            $userRole = new Role($userRoleName);
            $roleId = $this->role_handler->saveRole($userRole);


            $hasUsers = $this->findAll();
            //if hasn´t user create an admin user
            if (!$hasUsers) {
                $adminRole = $this->role_handler->getRoleByName('admin');
                $userRole->inherite($adminRole);
            } else {
                //role which belongs to all users
                $groupRole = $this->role_handler->getRoleByName('user');
                //creating user
                $query = 'INSERT INTO user(username,password,email,role_id) VALUES (:username,:password,:email,:role_id)';
                $stmt = $this->db->prepare($query);
                $stmt->bindValue(':username', $user->getUsername());
                $stmt->bindValue(':password', $user->getPassword());
                $stmt->bindValue(':email', $user->getEmail());
                $stmt->bindValue(':role_id', $roleId);
                $stmt->execute();

                $newUserId = $this->db->lastInsertId();

                if ($newUserId) {
                    //Insert some specific resources
                    $userRole->specificRole($groupRole, 'user', $newUserId);
                }
            }
            //save the Role to DB
            return $this->role_handler->saveRole($userRole);
        }

        return false;
    }

    public function update($user)
    {
        // Inicializa o array para armazenar os campos a serem atualizados
        $fields = [];

        // Monta a query SQL base
        $query = 'UPDATE user SET ';

        // Verifica e adiciona cada campo apenas se o valor não for null ou vazio
        if (!empty($user->getUsername())) {
            $fields[] = 'username = :username';
        }
        if (!empty($user->getPassword())) {
            $fields[] = 'password = :password';
        }
        if (!empty($user->getEmail())) {
            $fields[] = 'email = :email';
        }
        //nada para actualizar
        if (!$fields) {
            return true;
        }
        $query .= implode(', ', $fields);
        $query .= ' WHERE user_id = :user_id';
        $stmt = $this->db->prepare($query);

        // Vincula os parâmetros
        $stmt->bindValue(':user_id', $user->getId(), PDO::PARAM_INT);
        if (!empty($user->getUsername())) {
            $stmt->bindValue(':username', $user->getUsername());
        }
        if (!empty($user->getPassword())) {
            $stmt->bindValue(':password', $user->getPassword());
        }
        if (!empty($user->getEmail())) {
            $stmt->bindValue(':email', $user->getEmail());
        }

        // Executa a query e retorna o resultado da execução
        return $stmt->execute();
    }

    public function delete($user)
    {
        $user = $this->findById($user->getId());
        //if user exist
        if ($user) {
            //delete user
            $query = 'DELETE FROM user WHERE user_id= :user_id';
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':user_id', $user->getId(), PDO::PARAM_INT);
            $stmt->execute();

            $this->role_handler->deleteRole(strtolower($user->getEmail()));

            return true;
        }

        return false;
    }

    public function deleteAll()
    {
        $user = $this->findAll();
        //if user exist
        if ($user) {
            //delete user
            $query = 'TRUNCATE TABLE user';
            $stmt = $this->db->query($query);
            foreach ($user as $delete) {
                $this->role_handler->deleteRole(strtolower($delete['email']));
            }

            return true;
        }

        return false;
    }

    public function findAll()
    {
        $query = 'SELECT * FROM user';
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($result) {
            $users = [];
            foreach ($result as ['user_id' => $user_id,'username' => $username,'password' => $password,'email' => $email,'role_id' => $roleId]) {
                array_push($users, (new User($user_id, $username, $password, $email, $roleId))->toAssoc());
            }

            return $users;
        } else {
            return null;
        }
    }

    public function findById($id)
    {
        $query = 'SELECT * FROM user WHERE user_id = :user_id';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':user_id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result[] = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result[0]) {
            foreach ($result as ['user_id' => $user_id,'username' => $username,'password' => $password,'email' => $email,'role_id' => $roleId]) {
                return new User($user_id, $username, $password, $email, $roleId);
            }
        } else {
            return null;
        }
    }

    public function getIdByEmail($email)
    {
        $query = 'SELECT user_id FROM user WHERE email=:email';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['user_id'] ?? null;
    }

    public function findByEmailAndPassword($email, $password)
    {
        $query = 'SELECT * FROM user WHERE email=:email AND password=:password';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':password', $password);
        $stmt->execute();
        $result[] = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result[0]) {
            foreach ($result as ['user_id' => $user_id,'username' => $username,'password' => $password,'email' => $email,'role_id' => $roleId]) {
                return new User($user_id, $username, $password, $email, $roleId);
            }
        } else {
            return null;
        }
    }
}
