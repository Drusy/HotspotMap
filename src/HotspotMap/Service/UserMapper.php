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

    private $countByIdQuery = 'SELECT COUNT(*) FROM User WHERE id = :id';

    private $findByIdQuery = 'SELECT * FROM User WHERE id = :id';

    private $findAllQuery = 'SELECT * FROM User';

    private $insertQuery = 'INSERT INTO User Values (
        :id,
        :firstname,
        :lastname,
        :email,
        :pseudo,
        :website)';

    private $updateQuery = 'UPDATE User
        SET
        firstname = :firstname,
        lastname = :lastname,
        email = :email,
        pseudo = :pseudo,
        website = :website
        WHERE id = :id';

    public function save(User $user){

        $exist = $this->con->selectQuery($this->countByIdQuery, [
            'id'    =>  $user->getId()
        ]);

        if($exist[0][0] == 1){
            $res = $this->con->executeQuery($this->updateQuery, [
                'id'    => $user->getId(),
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'email' => $user->getEmail(),
                'pseudo' => $user->pseudo,
                'website' => $user->getWebsite()
            ]);
        }
        else{
            $res = $this->con->executeQuery($this->insertQuery, [
                'id'    => $user->getId(),
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'email' => $user->getEmail(),
                'pseudo' => $user->pseudo,
                'website' => $user->getWebsite()
            ]);
        }

        return $res;
    }

    public function findById($id){
        $userTab = $this->con->selectQuery($this->findByIdQuery, [
            'id'    =>  $id
        ]);
        return $this->fillUser($userTab);
    }

    public function findAll(){
        $userTab = $this->con->selectQuery($this->findAllQuery);
        $userList = [];

        foreach ($userTab as $user) {
            $userList[] = $this->fillUser($user);
        }

        return $userList;
    }

    private function fillUser($userTab){
        $user = new User($userTab['id']);
        $user->pseudo = $userTab['pseudo'];
        $user->firstname = $userTab['firstname'];
        $user->lastname = $userTab['lastname'];
        $user->setEmail($userTab['email']);
        $user->setWebsite($userTab['website']);

        return $user;
    }
} 