<?php

namespace HotspotMap\Service;

use HotspotMap\Model\Comment;

class CommentMapper extends Mapper
{
    private $countByIdQuery = 'SELECT COUNT(*) FROM Comment WHERE id = :id';
    private $findByIdQuery = 'SELECT * FROM Comment WHERE id = :id';
    private $findAllQuery = 'SELECT * FROM Comment WHERE validated = :validated';
    private $insertQuery = 'INSERT INTO Comment Values (
        :id,
        :content,
        :author,
        :place,
        :avatar,
        :creation_date,
        :validated)';
    private $updateQuery = 'UPDATE Comment
        SET
        content = :content,
        author = :author,
        place = :place,
        :avatar,
        :creation_date,
        validated = :validated
        WHERE id = :id';
    private $findByPlaceQuery = 'SELECT * FROM Comment WHERE place = :place AND validated = :validated';


    private $delete = 'DELETE
     FROM Comment
     WHERE id = :id';

    public function save(Comment $comment)
    {
        $exist = $this->con->selectQuery($this->countByIdQuery, [
            'id' => $comment->getId()
        ]);

        $commentArray = array(
            'id' => $comment->getId(),
            'content' => $comment->content,
            'author' => $comment->author,
            'place' => $comment->place,
            'avatar'=>$comment->avatar,
            'creation_date'=>$comment->creation_date(),
            'validated' => $comment->validated
        );

        if ($exist[0][0] == 1) {
            $res = $this->con->executeQuery($this->updateQuery, $commentArray);
        } else {
            $res = $this->con->executeQuery($this->insertQuery, $commentArray);
        }

        return $res;
    }

    public function findById($id)
    {
        $commentTab = $this->con->selectQuery($this->findByIdQuery, [
            'id' => $id
        ]);

        if($commentTab == null)

            return null;

        return $this->fillComment($commentTab[0]);
    }

    public function findAllValidated()
    {
        return $this->findAll(true);
    }

    public function findAllNonValidated()
    {
        return $this->findAll(false);
    }

    protected function findAll($validated = true)
    {
        $commentTab = $this->con->selectQuery($this->findAllQuery, [
            'validated' => $validated
        ]);
        $commentList = [];

        if (!empty($commentTab)) {
            foreach ($commentTab as $comment) {
                $commentList[] = $this->fillComment($comment);
            }
        }

        return $commentList;
    }

    public function deleteById($id)
    {
        return $this->con->executeQuery($this->delete, [
            'id' => $id
        ]);
    }

    public function findAllValidatedByPlaceId($placeId)
    {
        $commentTab = $this->con->selectQuery($this->findByPlaceQuery, [
            'validated' => 1,
            'place'     => $placeId
        ]);
        $commentList = [];

        if (!empty($commentTab)) {
            foreach ($commentTab as $comment) {
                $commentList[] = $this->fillComment($comment);
            }
        }

        return $commentList;
    }

    public function fillComment($commentTab)
    {
        $comment = new Comment($commentTab['id']);
        $comment->place = $commentTab['place'];
        $comment->author = $commentTab['author'];
        $comment->creation_date = $commentTab['creation_date'];
        $comment->avatar = $commentTab['avatar'];
        $comment->content = $commentTab['content'];
        $comment->validated = $commentTab['validated'];

        return $comment;
    }
}
