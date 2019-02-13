<?php

class IndexController
{
    public function index()
    {
        $view = new View();
        $posts = Post::all();


        $view->render('index', [
            "posts" => $posts
        ]);
    }
    public function updateUser($id = 0)
    {
        $view = new View();

        $view->render('updateUser', [
            "post" => Post::find($id),
            "message" => ""
        ]);

    }
    public function registration()
    {
        $view = new View();

        $view->render('registration', [
            "message" => ""
        ]);

    }

    public function view($id = 0)
    {
        $view = new View();

        $view->render('view', [
            "post" => Post::find($id)
        ]);
    }

    public function tag($id = 0)
    {
        $view = new View();
        $view->render('view', [
            "tag" => Tag::findContent($id)
        ]);
    }

    public function newPost()
    {
        $data = $this->_validate($_POST);

        if ($data === false) {
            header('Location: ' . App::config('url'));
        } else {
            $connection = Db::connect();
            $sql = 'INSERT INTO post (content,user) VALUES (:content,:user)';
            $stmt = $connection->prepare($sql);
            $stmt->bindValue('content', $data['content']);
            $stmt->bindValue('user', Session::getInstance()->getUser()->id);
            $stmt->execute();
            header('Location: ' . App::config('url'));

        }
    }

    public function newTag($id)
    {
        $data = $this->_validate($_POST);

        if ($data === false) {
            header('Location: ' . App::config('url'));
        } else {
            $db = Db::connect();
            $db->beginTransaction();
            $stmt = $db->prepare("INSERT INTO tag (content) VALUES (:content)");
            $stmt->bindValue('content', $data['content']);
            $stmt->execute();
            $tag_id=$db->lastInsertId();

            $stmt = $db->prepare("INSERT INTO tag_post (post,tag) VALUES (:post, :tag)");
            $stmt->bindValue('post', $id);
            $stmt->bindValue('tag', $tag_id);
            $stmt->execute();
            $db->commit();

            header('Location: ' . App::config('url'));
        }
    }

    /**
     * @param $data
     * @return array|bool
     */
    private function _validate($data)
    {
        $required = ['content'];

        //validate required keys
        foreach ($required as $key) {
            if (!isset($data[$key])) {
                return false;
            }

            $data[$key] = trim((string)$data[$key]);
            if (empty($data[$key])) {
                return false;
            }
        }
        return $data;
    }


    public function postSearch()
    {
//ne dostaju kontrole
        $db = Db::connect();
        $statement = $db->prepare("select id ,firstname,lastname,email,image, pass from user where email=:email");
        $statement->bindValue('email', Request::post("email"));
        $statement->execute();


        if($statement->rowCount()>0){
            $user = $statement->fetch();
            if(password_verify(Request::post("password"), $user->pass)){

                unset($user->pass);

                Session::getInstance()->login($user);


                $this->index();
            }else{
                $view = new View();
                $view->render('login',["message"=>"Neispravna kombinacija korisničko ime i lozinka"]);
            }
        }else{
            $view = new View();
            $view->render('login',["message"=>"Neispravan email"]);
        }




    }
}