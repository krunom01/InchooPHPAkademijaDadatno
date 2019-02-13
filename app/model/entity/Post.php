<?php

class Post
{
    private $id;

    private $content;

    private $user;

    private $date;

    private $likes;

    private $comments;

    private $userid;

    private $tag;

    private $report;

    private $hidden;







    public function __construct($id, $content, $user,$date, $likes,$comments,$userid,$tag,$report,$hidden)
    {
        $this->setId($id);
        $this->setContent($content);
        $this->setUser($user);
        $this->setDate($date);
        $this->setLikes($likes);
        $this->setComments($comments);
        $this->setUserid($userid);
        $this->setTag($tag);
        $this->setReport($report);
        $this->setHidden($hidden);


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

    public static function all()
    {



        $list = [];
        $db = Db::connect();
        $statement = $db->prepare("select
  a.id, a.content, concat(b.firstname, ' ', b.lastname) as user, a.date,
  count(c.id) as likes, a.hidden
from
  post a inner join user b on a.user=b.id
         left join likes c on a.id=c.post
where a.date > ADDDATE(now(), INTERVAL -7 DAY)
group by a.id, a.content, concat(b.firstname, ' ', b.lastname), a.date,a.hidden
order by a.date desc limit 10");
        $statement->execute();
        foreach ($statement->fetchAll() as $post) {

            $statement = $db->prepare("select a.id, a.content, concat(b.firstname, ' ', b.lastname) as user, a.date from comment a inner join user b on a.user=b.id where a.post=:id ");
            $statement->bindValue('id', $post->id);
            $statement->execute();
            $comments = $statement->fetchAll();

            $statement = $db->prepare("
                    select a.content
                    from tag a
                    inner join tag_post b on a.id = b.tag
                    inner join post p on b.post = p.id
                    where b.post = :id");
            $statement->bindValue('id', $post->id);
            $statement->execute();
            $tag = $statement->fetchAll();

            $statement = $db->prepare("
                    select count(id_report) from report where post_report=:id;");
            $statement->bindValue('id', $post->id);
            $statement->execute();
            $report = $statement->fetchColumn();

            $statement = $db->prepare("
                  select count(id_clike) from commentlikes where post_clike=:id");
            $statement->bindValue('id', $post->id);
            $statement->execute();
            $commentlike = $statement->fetchColumn();

            $statement = $db->prepare("
                  select count(id_rc) from reportComment where post_rc=:id;");
            $statement->bindValue('id', $post->id);
            $statement->execute();
            $reportlike = $statement->fetchColumn();





            $list[] = new Post($post->id, $post->content, $post->user,$post->date,$post->likes,$comments,0, $tag,$report,$post->hidden);

        }


        return $list;
    }





    public static function find($id)
    {
        $id = intval($id);
        $db = Db::connect();
        $statement = $db->prepare("select 
        a.id, a.content, concat(b.firstname, ' ', b.lastname) as user, 
        a.date, a.user as userid, count(c.id) as likes, a.hidden
        from 
        post a inner join user b on a.user=b.id 
        left join likes c on a.id=c.post 
         where a.id=:id");
        $statement->bindValue('id', $id);
        $statement->execute();
        $post = $statement->fetch();

        $statement = $db->prepare("select a.id, a.content, concat(b.firstname, ' ', b.lastname) as user, a.date from comment a inner join user b on a.user=b.id where a.post=:id ");
        $statement->bindValue('id', $id);
        $statement->execute();
        $comments = $statement->fetchAll();

        $statement = $db->prepare("
                    select a.content
                    from tag a
                    inner join tag_post b on a.id = b.tag
                    inner join post p on b.post = p.id
                    where b.post = :id");
        $statement->bindValue('id', $id);
        $statement->execute();
        $tag = $statement->fetchAll();


        $statement = $db->prepare("
                    select count(id_report) from report where post_report=:id;");
        $statement->bindValue('id', $post->id);
        $statement->execute();
        $report = $statement->fetchColumn();

        $statement = $db->prepare("
                  select count(id_clike) from commentlikes where post_clike=:id");
        $statement->bindValue('id', $id);
        $statement->execute();
        $commentlike = $statement->fetchColumn();

        $statement = $db->prepare("
                  select count(id_rc) from reportComment where post_rc=:id;");
        $statement->bindValue('id', $id);
        $statement->execute();
        $reportlike = $statement->fetchColumn();




        return new Post($post->id, $post->content, $post->user,$post->date,$post->likes,$comments,$post->userid, $tag,$report,$post->hidden);
    }
}