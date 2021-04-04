<?php


namespace Source\Models;


use CoffeeCode\DataLayer\DataLayer;
use Exception;

/**
 * Class User
 * @package Source\Models
 */
class User extends DataLayer
{
    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct("users", ["first_name", "last_name", "email", "passwd"]);
    }

    /**
     * @return bool
     */
    public function save(): bool
    {
        if (!$this->validateEmail() || !$this->validatePasswd() || !parent::save()) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    protected function validateEmail(): bool
    {
        if (empty($this->email) || !filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $this->fail = new Exception("Por favor informe um e-mail válido para continuar!");
            return false;
        }

        //UPDATE
        if (!empty($this->id)) {
            if ($this->find("email = :email AND id != :id", "email={$this->email}&id={$this->id}")->fetch()) {
                $this->fail = new Exception("O e-mail informado já está cadastrado");
                return false;
            }
        }

        // CREATE
        if (empty($this->id)) {
            if ($this->find("email = :email", "email={$this->email}")->fetch()) {
                $this->fail = new Exception("O e-mail informado já está cadastrado");
                return false;
            }
        }
        
        return true;
    }

    /**
     * @return bool
     */
    protected function validatePasswd(): bool
    {
        if (empty($this->passwd) || !is_passwd($this->passwd)) {
            $min = PASSWD["min"];
            $max = PASSWD["max"];
            $this->fail = new Exception("A senha deve ter entre {$min} e {$max} caracteres");
            return false;
        } else {
            $this->passwd = passwd($this->passwd);
            return true;
        }
    }
}