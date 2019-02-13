<?php
/**
 * Created by PhpStorm.
 * User: kruno
 * Date: 13/02/19
 * Time: 16.19
 */

class User {
    private $id;
    private $firstname;
    private $lastname;
    private $email;
    private $admin;
    private $image;

    public function __construct($id, $firstname, $lastname, $email, $admin, $image)
    {
        $this->setId($id);
        $this->setFirstName($firstname);
        $this->setLastName($lastname);
        $this->setEmail($email);
        $this->setAdmin($admin);
        $this->setImage($image);

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
    public static function user($userid)
    {
        $list = [];
        $userid = intval($userid);
        $db = Db::connect();
        $statement = $db->prepare("select * from user where id = :id");
        $statement->bindValue('id', $userid);
        $statement->execute();
        foreach ($statement->fetchAll() as $user) {
            $list = new User($user->id, $user->firstname, $user->lastname, $user->email, $user->admin,$user->image);
        }
        return $list;
    }
}