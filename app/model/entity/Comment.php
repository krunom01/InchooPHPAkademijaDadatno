<?php

class Comment
{
    private $id;

    private $content;

    private $user;

    private $date;

    private $likes;

    private $reports;




    public function __construct($id, $content, $user, $date, $likes,$reports)
    {
        $this->setId($id);
        $this->setContent($content);
        $this->setUser($user);
        $this->setDate($date);
        $this->setLikes($likes);
        $this->setReports($reports);


    }


    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    public function __get($name)
    {
        return isset($this->$name) ? $this->$name : null;
    }

    public function __call($name, $arguments)
    {
        $function = substr($name, 0, 3);
        if ($function === 'set') {
            $this->__set(strtolower(substr($name, 3)), $arguments[0]);
            return $this;
        } else if ($function === 'get') {
            return $this->__get(strtolower(substr($name, 3)));
        }

        return $this;
    }


    public static function find($id)
    {
        $list = [];
        $id = intval($id);
        $db = Db::connect();
        $statement = $db->prepare("
            SELECT a.*, concat(b.firstname, ' ', b.lastname) as user,
            count(c.id_clike) as  likes
            from comment a
            inner join user b on a.user = b.id
            left join commentlikes c on a.id = c.post_clike
            
            where a.id = :id
            group by a.id, a.content, concat(b.firstname, ' ', b.lastname), a.date
            order by a.date desc;");
        $statement->bindValue('id', $id);
        $statement->execute();


        foreach ($statement->fetchAll() as $comment) {

            $statement = $db->prepare("
                    select count(id_rc) from reportComment where post_rc=:id;");
            $statement->bindValue('id', $comment->id);
            $statement->execute();
            $report = $statement->fetchColumn();

            $list[] = new Comment($comment->id, $comment->content,$comment->user,$comment->date,$comment->likes,$report);
        }
        return $list;
    }
}
