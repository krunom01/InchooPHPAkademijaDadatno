<?php

class AdminController
{
    public function index()
    {

        $posts = Post::all();
        $view = new View();
        $view->render('index', [
            "posts" => $posts,
            "message" => ""
        ]);
    }
    public function login()
    {
    

        $view = new View();    
        $view->render('login', [
            "message" => ""
        ]);
    }

    public function registration()
    {
        $view = new View();
        $view->render('registration', [
            "message"=>""
            ]);
       
    }


    public function register()
    {

        if(Validator::string(Request::post("firstname")) && Validator::string(Request::post("lastname"))) {
            if(Validator::email(Request::post("email"))) {
                if ($_FILES['image']['size'] == 0) {
                    $jpg = 'unset';
                } else {


                    if ($_FILES['image']['type'] != NULL) {
                        $dir = 'app/public/images/'; //save img to dir
                        if ($_FILES['image']['type'] != 'image/jpeg') { // if file type not jps
                            echo 'file is not jpg!';
                            exit();
                        }
                        if (move_uploaded_file($_FILES['image']['tmp_name'], $dir . $_FILES['image']['name'])) {
                            $jpg = $dir . $_FILES['image']['name'];
                        }
                    }
                }
                $db = Db::connect();
                $statement = $db->prepare("insert into user (firstname,lastname,email,pass,image) values (:firstname,:lastname,:email,:pass, :image)");
                $statement->bindValue('firstname', Request::post("firstname"));
                $statement->bindValue('lastname', Request::post("lastname"));
                $statement->bindValue('email', Request::post("email"));
                $statement->bindValue('pass', password_hash(Request::post("pass"), PASSWORD_DEFAULT));
                $statement->bindValue('image', $jpg);
                $statement->execute();
                Session::getInstance()->logout();
                $view = new View();
                $view->render('login', ["message" => ""]);
                }else{
                $view = new View();
                $view->render('registration', [
                    "message" => "krivo unesen email"
                ]);
            }
            }else{
            $view = new View();
            $view->render('registration', [
                "message" => "krivo uneseni podaci za ime i prezime"
            ]);
        }
    }

    public function delete($post)
    {

        $db = Db::connect();
        $db->beginTransaction();
        $statement = $db->prepare("delete from comment where post=:post");
        $statement->bindValue('post', $post);
        $statement->execute();

        $statement = $db->prepare("delete from likes where post=:post");
        $statement->bindValue('post', $post);
        $statement->execute();

        $statement = $db->prepare("delete from post where id=:post");
        $statement->bindValue('post', $post);
    
        $statement->execute();

        $db->commit();
        
        $this->index();
       
    }

    public function comment($post)
    {

        $db = Db::connect();
        $statement = $db->prepare("insert into comment (post,user, content) values (:post,:user,:content)");
        $statement->bindValue('post', $post);
        $statement->bindValue('user', Session::getInstance()->getUser()->id);
        $statement->bindValue('content', Request::post("content"));
        $statement->execute();
        
        $view = new View();

        $view->render('view', [
            "post" => Post::find($post)
        ]);
       
    }


    public function like($post)
    {
        try {
            $db = Db::connect();
            $statement = $db->prepare("insert into likes (post,user,uniquelike) values (:post,:user,:uniquelike)");
            $statement->bindValue('post', $post);
            $statement->bindValue('user', Session::getInstance()->getUser()->id);
            $statement->bindValue('uniquelike', Session::getInstance()->getUser()->id . $post);
            $statement->execute();
            header('Location: ' . App::config('url'));
        }catch (PDOException $exception){
            $posts = Post::all();
            $view = new View();
            $view->render('index', [
                "posts" => $posts,
                "message" => "Vec si lajkao post!"
            ]);
        }
    }

    public function report($post)
    {
        try {
        $db = Db::connect();
        $statement = $db->prepare("insert into report(user_report,post_report,uniquelike_report) 
                                            values (:user_report,:post_report,:uniquelike_report)");
        $statement->bindValue('user_report', Session::getInstance()->getUser()->id);
        $statement->bindValue('post_report',$post);
        $statement->bindValue('uniquelike_report', Session::getInstance()->getUser()->id . $post);
        $statement->execute();
        header('Location: ' . App::config('url'));
        }catch (PDOException $exception){
            $posts = Post::all();
            $view = new View();
            $view->render('index', [
                "posts" => $posts,
                "message" => "Vec si reportao post!"
            ]);
        }

    }


    public function commentlike($post)
    {
        try {
            $db = Db::connect();
            $statement = $db->prepare("insert into commentlikes (user_clike,post_clike,uniqueclike) 
                                            values (:user_clike,:post_clike,:uniqueclike)");
            $statement->bindValue('user_clike', Session::getInstance()->getUser()->id);
            $statement->bindValue('post_clike',$post);
            $statement->bindValue('uniqueclike', Session::getInstance()->getUser()->id . $post);
            $statement->execute();
            header('Location: ' . App::config('url'));
        }catch (PDOException $exception){
            $posts = Post::all();
            $view = new View();
            $view->render('index', [
                "posts" => $posts,
                "message" => "vec si lajkao ovaj komentar"
            ]);
        }

    }

    public function reportComment($post)
    {
        try {
            $db = Db::connect();
            $statement = $db->prepare("insert into reportComment (user_rc,post_rc,uniquerc) 
                                            values (:user_rc,:post_rc,:uniquerc)");
            $statement->bindValue('user_rc', Session::getInstance()->getUser()->id);
            $statement->bindValue('post_rc',$post);
            $statement->bindValue('uniquerc', Session::getInstance()->getUser()->id . $post);
            $statement->execute();
            header('Location: ' . App::config('url'));
        }catch (PDOException $exception){
            $posts = Post::all();
            $view = new View();
            $view->render('index', [
                "posts" => $posts,
                "message" => "vec si reportao ovaj komentar"
            ]);
        }

    }


    public function authorize()
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
    public function update($id)
    {
            if(Validator::string(Request::post("firstname")) && Validator::string(Request::post("lastname"))) {
                if(Validator::email(Request::post("email"))) {
                    if(Validator::password(Request::post("newpassword"), Request::post("password"))) {
                        if ($_FILES['image']['size'] == 0) {
                            $jpg = 'unset';
                        } else {

                            if ($_FILES['image']['type'] != NULL) {
                                $dir = 'app/public/images/'; //save img to dir
                                if ($_FILES['image']['type'] != 'image/jpeg') { // if file type not jps
                                    echo 'file is not jpg!';
                                    exit();
                                }
                                if (move_uploaded_file($_FILES['image']['tmp_name'], $dir . $_FILES['image']['name'])) {
                                    $jpg = $dir . $_FILES['image']['name'];
                                }
                            }
                        }
                        $db = Db::connect();
                        $statement = $db->prepare("update user set firstname = :firstname , lastname= :lastname,
                         email = :email, pass=:pass, image=:image  where id = :id");
                        $statement->bindValue('firstname', Request::post("firstname"));
                        $statement->bindValue('lastname', Request::post("lastname"));
                        $statement->bindValue('email', Request::post("email"));
                        $statement->bindValue('pass', password_hash(Request::post("newpassword"), PASSWORD_DEFAULT));
                        $statement->bindValue('id', $id);
                        $statement->bindValue('image', $jpg);
                        $statement->execute();
                        //update session
                        Session::getInstance()->getUser()->firstname = Request::post("firstname");
                        Session::getInstance()->getUser()->lastname = Request::post("lastname");
                        Session::getInstance()->getUser()->email = Request::post("email");
                        Session::getInstance()->getUser()->image = $jpg;
                        header('Location: ' . App::config('url'));

                    } else {
                        $view = new View();
                        $view->render('updateUser',["message"=>"Wrong password!"]);
                    }
                }
                else {
                    $view = new View();
                    $view->render('updateUser',["message"=>"Wrong email!"]);

                }
            }
            else {
                $view = new View();
                $view->render('updateUser',["message"=>"Wrong insert for first or last name!"]);
            }


    }

    public function logout()
    {
    
        Session::getInstance()->logout();
        $this->index();
    }

    public function json()
    {

        $posts = Post::all();
       //print_r($posts);
        echo json_encode($posts);
    }

    public function hidePost($postid){

        $hidden = 'hidden';

        $db = Db::connect();
        $statement = $db->prepare("update post set hidden =:hidden where id =:id ");
        $statement->bindValue('hidden', $hidden);
        $statement->bindValue('id', $postid);
        $statement->execute();
        header('Location: ' . App::config('url'));

    }

    public function search($content){
        $db = Db::connect();
        $statement = $db->prepare("select a.content as tag,p.content as  post, concat(u.firstname, ' ',u.lastname) as user
                    from tag a
                    inner join tag_post tp on a.id = tp.tag
                    inner join post p on tp.post = p.id
                    inner join user u on p.user = u.id
                    where a.content=:content");
        $statement->bindValue('content', $content);

        $statement->execute();
        $statement->fetch();


    }



}