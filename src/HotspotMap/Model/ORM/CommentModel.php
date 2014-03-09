<?php

namespace HotspotMap\Model\ORM;

class CommentModel
{
    public $content = "";
    public $author = "";
    public $place = "";
    public $avatar = "";
    public $validated = 0;
    public $creation_date = "";

    protected $id;
}
