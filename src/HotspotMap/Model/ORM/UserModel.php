<?php

namespace HotspotMap\Model\ORM;

class UserModel
{
    public $firstname;
    public $lastname;
    public $username;

    protected $password;
    protected $salt;
    protected $roles;
    protected $id;
    protected $email;
    protected $website;
}
