<?php

namespace HotspotMap\Service;

use HotspotMap\Model\User;

class UserMapper extends Mapper
{
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

    private function fillUser($userTab)
    {
        $user = new User($userTab['id']);
        $user->pseudo = $userTab['pseudo'];
        $user->firstname = $userTab['firstname'];
        $user->lastname = $userTab['lastname'];
        $user->setEmail($userTab['email']);
        $user->setWebsite($userTab['website']);

        return $user;
    }

    public function save(User $user)
    {
        $exist = $this->con->selectQuery($this->countByIdQuery, [
            'id' => $user->getId()
        ]);

        if ($exist[0][0] == 1) {
            $res = $this->con->executeQuery($this->updateQuery, [
                'id' => $user->getId(),
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'email' => $user->getEmail(),
                'pseudo' => $user->pseudo,
                'website' => $user->getWebsite()
            ]);
        } else {
            $res = $this->con->executeQuery($this->insertQuery, [
                'id' => $user->getId(),
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'email' => $user->getEmail(),
                'pseudo' => $user->pseudo,
                'website' => $user->getWebsite()
            ]);
        }

        return $res;
    }

    public function findById($id)
    {
        $userTab = $this->con->selectQuery($this->findByIdQuery, [
            'id' => $id
        ]);

        return $this->fillUser($userTab[0]);
    }

    public function findAll()
    {
        $userTab = $this->con->selectQuery($this->findAllQuery);
        $userList = [];

        if (!empty($userTab)) {
            foreach ($userTab as $user) {
                $userList[] = $this->fillUser($user);
            }
        }

        return $userList;
    }
}
