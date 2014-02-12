<?php
/**
 * Created by PhpStorm.
 * User: florian
 * Date: 11/02/14
 * Time: 16:07
 */

namespace HotspotMap\Service;


use HotspotMap\Model\User;

class UserMapper extends Mapper{

    private $countQueryById = 'SELECT COUNT(*) FROM User WHERE id = :id';

    private $insertQuery = 'INSERT INTO User Values (
        :id,
        :firstname,
        :lastname,
        :email,
        :pseudo,
        :website)';

    private $updateQuery = 'UPDATE USER
        SET
        id = :id,
        firstname = :firstname,
        lastname = :lastname,
        email = :email,
        pseudo = :pseudo,
        website = :website';

    public function save(User $u){

        $exist = $this->con->selectQuery($this->countQueryById, [
            'id'    =>  $u->getId()
        ]);

        if($exist[0] == 1){
            $res = $this->con->executeQuery($this->updateQuery, [
                'id'    => $u->getId(),
                'firstname' => $u->getFirstname(),
                'lastname' => $u->getLastname(),
                'email' => $u->getEmail(),
                'pseudo' => $u->getPseudo(),
                'website' => $u->getWebsite()
            ]);
        }
        else{
            $res = $this->con->executeQuery($this->insertQuery, [
                'id'    => $u->getId(),
                'firstname' => $u->getFirstname(),
                'lastname' => $u->getLastname(),
                'email' => $u->getEmail(),
                'pseudo' => $u->getPseudo(),
                'website' => $u->getWebsite()
            ]);
        }

        return $res;
    }
} 