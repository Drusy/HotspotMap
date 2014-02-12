<?php
/**
 * Created by PhpStorm.
 * User: florian
 * Date: 11/02/14
 * Time: 16:36
 */

namespace HotspotMap\Service;


class Connection extends \PDO
{
    public function executeQuery($query, array $parameters = [])
    {
        $stmt = $this->prepareQuery($query, $parameters);

        return $stmt->execute();
    }

    public function selectQuery($query, array $parameters = [])
    {
        $stmt = $this->prepareQuery($query, $parameters);

        if($stmt->execute())
            return $stmt->fetch(\PDO::FETCH_BOTH);

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