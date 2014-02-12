<?php
/**
 * Created by PhpStorm.
 * User: florian
 * Date: 11/02/14
 * Time: 16:03
 */
namespace HotspotMap\Model;

use HotspotMap\Model\ORM\UserModel;

class User extends UserModel{

    public function __construct($id=null)
    {
        if($id == null){
           $this->id = uniqid();
           $this->pseudo = uniqid("user_");
        }
        else{
            $this->id = $id;
        }
    }

    public function setEmail($email)
    {
        //TODO : VERIF
        $this->email = $email;
    }


    public function getEmail()
    {
        return $this->email;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setWebsite($website)
    {
        //TODO : VERIF
        $this->website = $website;
    }

    public function getWebsite()
    {
        return $this->website;
    }
} 