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
        $stmt = $this->prepare($query);

        foreach ($parameters as $name => $value) {
            $stmt->bindValue(':' . $name, $value);
        }

        return $stmt->execute();
    }
}