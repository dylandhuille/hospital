<?php
require_once(dirname(__FILE__) . '/../utils/database.php');

class User
{

    private $_id;
    private $_lastname;
    private $_email;
    private $_password;
    private $_validated_token;

    private $_pdo;

    public function __construct(
        $lastname = null,
        $email = null,
        $password = null
    ) {

        $this->_lastname = $lastname;
        $this->_email = $email;
        $this->_password = $password;
        $this->_validated_token = bin2hex(openssl_random_pseudo_bytes(60));

        $this->_pdo = Database::getInstance();
    }

    public function set()
    {

        $sql = 'INSERT INTO `user` (`lastname`, `email`, `password`, `validated_token`)
        VALUES (:lastname, :email, :password, :validated_token);';

        try {
            $sth = $this->_pdo->prepare($sql);
            $sth->bindValue(':lastname', $this->_lastname);
            $sth->bindValue(':email', $this->_email);
            $sth->bindValue(':password', $this->_password);
            $sth->bindValue(':validated_token', $this->_validated_token);
            if (!$sth->execute()) {
                throw new PDOException('Problème lors de l\'inscription');
            }
            return true;
        } catch (\PDOException $ex) {
            return $ex;
        }
    }

    public function getValidatedToken()
    {
        return $this->_validated_token;
    }

    public static function get($id)
    {
        $sql = 'SELECT * FROM `user`
                WHERE `id` = :id;';
        try {
            $pdo = Database::getInstance();
            $sth = $pdo->prepare($sql);
            $sth->bindValue(':id', $id, PDO::PARAM_INT);
            if (!$sth->execute()) {
                throw new PDOException('Erreur d\'exécution');
            }
            return $sth->fetch();
        } catch (\PDOException $ex) {
            //throw $ex;
        }
    }

    public static function deleteToken($id)
    {
        $sql = 'UPDATE `user` SET `validated_token`= null
                WHERE `id` = :id';
        try {
            $pdo = Database::getInstance();
            $sth = $pdo->prepare($sql);
            $sth->bindValue(':id', $id, PDO::PARAM_INT);
            if (!$sth->execute()) {
                throw new PDOException('Erreur d\'exécution');
            } else {
                return $sth->rowCount();
            }
        } catch (\PDOException $ex) {
            //throw $ex;
        }
    }

    public static function setValidateAccount($id)
    {
        $sql = 'UPDATE `user` SET `validated_at`= CURRENT_TIMESTAMP()
                WHERE `id` = :id';

        try {
            $pdo = Database::getInstance();
            $sth = $pdo->prepare($sql);
            $sth->bindValue(':id', $id, PDO::PARAM_INT);

            if (!$sth->execute()) {
                throw new PDOException('Problème de validation du compte');
            } else {
                return true;
            }
        } catch (\PDOException $ex) {
            return $ex;
        }
    }

    public static function getByEmail($email)
    {
        $sql = 'SELECT * FROM `user` WHERE `email` = :email;';

        try {
            $pdo = Database::getInstance();
            $sth = $pdo->prepare($sql);
            $sth->bindValue(':email', $email);

            if (!$sth->execute()) {
                throw new PDOException('Problème d\'execution');
            } else {
                return $sth->fetch();
            }
        } catch (\PDOException $ex) {
            return $ex;
        }
    }

    public static function isValidated($email)
    {
        $sql = 'SELECT `validated_at` FROM `user` WHERE `email` = :email;';

        try {
            $pdo = Database::getInstance();
            $sth = $pdo->prepare($sql);
            $sth->bindValue(':email', $email);

            if (!$sth->execute()) {
                throw new PDOException('Problème d\'execution');
            } else {
                return $sth->fetchColumn();
            }
        } catch (\PDOException $ex) {
            return $ex;
        }
    }
}