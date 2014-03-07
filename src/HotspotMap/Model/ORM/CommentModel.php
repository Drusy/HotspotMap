<?php

namespace HotspotMap\Model\ORM;

class CommentModel
{
    public $content;
    public $author;
    public $place;
    public $avatar;
    public $validated;
    public $creation_date;

    protected $id;
}