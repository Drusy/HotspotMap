<?php

namespace HotspotMap\Model;

use HotspotMap\Model\ORM\CommentModel;

class Comment extends CommentModel implements \JsonSerializable
{
    public function __construct($id = null)
    {
        if ($id == null) {
            $this->id = uniqid();
        } else {
            $this->id = $id;
        }

        $this->creation_date = date_default_timezone_get();
    }

    public function jsonSerialize()
    {
        $json = array();
        foreach ($this as $key => $value) {
            $json[$key] = $value;
        }

        return $json;
    }
}