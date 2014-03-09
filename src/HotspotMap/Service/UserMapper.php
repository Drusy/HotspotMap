<?php

namespace HotspotMap\Service;

use HotspotMap\Model\User;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class UserMapper extends Mapper implements UserProviderInterface
{
    private $countByIdQuery      = 'SELECT COUNT(*) FROM User WHERE id = :id';
    private $findByIdQuery       = 'SELECT * FROM User WHERE id = :id';
    private $findByUsernameQuery = 'SELECT * FROM User WHERE username = :username';
    private $findAllQuery        = 'SELECT * FROM User';
    private $insertQuery         = 'INSERT INTO User Values (
        :id,
        :firstname,
        :lastname,
        :email,
        :username,
        :website,
        :password,
        :salt,
        :roles)';
    private $updateQuery         = 'UPDATE User
        SET
        firstname = :firstname,
        lastname = :lastname,
        email = :email,
        username = :username,
        website = :website,
        password = :password,
        salt = :salt,
        roles = :roles
        WHERE id = :id';

    private function fillUser($userTab)
    {
        $user = new User($userTab['id']);
        $user->username = $userTab['username'];
        $user->firstname = strtolower($userTab['firstname']);
        $user->lastname = strtolower($userTab['lastname']);
        $user->setEmail(strtolower($userTab['email']));
        $user->setWebsite(strtolower($userTab['website']));
        $user->setPassword($userTab['password']);
        $user->setSalt($userTab['salt']);
        $user->setRoles($userTab['roles']);

        return $user;
    }

    public function save(User $user)
    {
        $exist = $this->con->selectQuery($this->countByIdQuery, [
            'id' => $user->getId()
        ]);

        if ($exist[0][0] == 1) {
            $res = $this->con->executeQuery($this->updateQuery, [
                'id'            => $user->getId(),
                'firstname'     => strtolower($user->firstname),
                'lastname'      => strtolower($user->lastname),
                'email'         => strtolower($user->getEmail()),
                'username'      => $user->username,
                'website'       => strtolower($user->getWebsite()),
                'password'      => $user->getPassword(),
                'salt'          => $user->getSalt(),
                'roles'         => $user->getRoles()
            ]);
        } else {
            $res = $this->con->executeQuery($this->insertQuery, [
                'id' => $user->getId(),
                'firstname' => strtolower($user->firstname),
                'lastname' => strtolower($user->lastname),
                'email' => strtolower($user->getEmail()),
                'username' => $user->username,
                'website' => strtolower($user->getWebsite()),
                'password'      => $user->getPassword(),
                'salt'          => $user->getSalt(),
                'roles'  => $user->getRoles()
            ]);
        }

        return $res;
    }

    public function findById($id)
    {
        $userTab = $this->con->selectQuery($this->findByIdQuery, [
            'id' => $id
        ]);

        if ($userTab == null) {
            return null;
        }

        return $this->fillUser($userTab[0]);
    }

    public function findByUsername($username)
    {
        $userTab = $this->con->selectQuery($this->findByUsernameQuery, [
            'username' => $username
        ]);

        if ($userTab == null) {
            return null;
        }

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

    /// UserProviderInterface
    public function loadUserByUsername($username)
    {
        $user = $this->findByUsername($username);

        if($user == null)
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));

        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === 'HotspotMap\Model\User';
    }
}
