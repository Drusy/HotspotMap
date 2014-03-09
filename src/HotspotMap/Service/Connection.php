<?php

namespace HotspotMap\Service;

class Connection extends \PDO
{

    private static $con = null;

    public static function getConnection()
    {
        if (Connection::$con == null) {
            $dsn = $GLOBALS['DB_DSN'];
            $user = $GLOBALS['DB_USER'];
            $password = $GLOBALS['DB_PASSWD'];

            Connection::$con = new Connection($dsn, $user, $password);
        }

        return Connection::$con;
    }

    public function executeQuery($query, array $parameters = [])
    {
        $stmt = $this->prepareQuery($query, $parameters);

        return $stmt->execute();
    }

    public function selectQuery($query, array $parameters = [])
    {
        $stmt = $this->prepareQuery($query, $parameters);

        if ($stmt->execute()) {
            while ($row = $stmt->fetch(\PDO::FETCH_BOTH)) {
                $tab[] = $row;
            }

            return $tab;
        }

        return null;
    }

    private function prepareQuery($query, array $parameters = [])
    {
        $stmt = $this->prepare($query);

        foreach ($parameters as $name => $value) {
            $stmt->bindValue(':' . $name, $value);
        }

        return $stmt;
    }
}
