<?php

require_once '../libraries/Database.php';

class User
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    // Procura por um usuario usando email ou nome de usuario
    public function findUserByEmailOrUsername($email, $username)
    {
        $this->db->query('SELECT * FROM users WHERE usersUid = :username OR usersEmail = :email');
        $this->db->bind(':username', $username);
        $this->db->bind(':email', $email);

        $row = $this->db->single();

        // Procura pela linha
        if ($this->db->rowCount() > 0) {
            return $row;
        }

        return false;
    }

    // Registra usuario
    public function register($data)
    {
        $this->db->query('INSERT INTO users (usersName, usersEmail, usersUid, usersPwd)
        VALUES (:name, :email, :Uid, :password)');
        // Binda os valores
        $this->db->bind(':name', $data['usersName']);
        $this->db->bind(':email', $data['usersEmail']);
        $this->db->bind(':Uid', $data['usersUid']);
        $this->db->bind(':password', $data['usersPwd']);

        //Executa
        if ($this->db->execute()) {
            return true;
        }

        return false;
    }

    // Loga o usuario
    public function login($nameOrEmail, $password)
    {
        $row = $this->findUserByEmailOrUsername($nameOrEmail, $nameOrEmail);

        if (false == $row) {
            return false;
        }

        $hashedPassword = $row->usersPwd;
        if (password_verify($password, $hashedPassword)) {
            return $row;
        }

        return false;
    }

    // Reseta senha
    public function resetPassword($newPwdHash, $tokenEmail)
    {
        $this->db->query('UPDATE users SET usersPwd=:pwd WHERE usersEmail=:email');
        $this->db->bind(':pwd', $newPwdHash);
        $this->db->bind(':email', $tokenEmail);

        if ($this->db->execute()) {
            return true;
        }

        return false;
    }
}
