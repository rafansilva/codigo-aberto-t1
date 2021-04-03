<?php


namespace Source\Models;


use CoffeeCode\DataLayer\DataLayer;

/**
 * Class Users
 * @package Source\Models
 */
class Users extends DataLayer
{
    /**
     * Users constructor.
     */
    public function __construct()
    {
        parent::__construct("users", ["first_name", "last_name", "email", "passwd"]);
    }
}