<?php
namespace src\handlers;

use \src\models\User;
use \src\models\User_Relation;
use \src\handlers\PostHandler;

class UserHandler {

    public static function checkLogin(){
        if(!empty($_SESSION['token'])){
            $token = $_SESSION['token'];

            $data = User::select()->where('token', $token)->one();
            if(count($data) > 0){
                
                $loggedUser = new User();
                $loggedUser->id = $data['id'];
                $loggedUser->name = $data['name'];
                $loggedUser->avatar = $data['avatar'];
                
                return $loggedUser;
            }
        }
        return false;
    }

    public static function verifyLogin($email, $password) {
        $user = User::select()->where('email', $email)->one();

        if($user){
            if(password_verify($password, $user['password'])){
                $token = md5(time().rand(0, 9999).time());

                User::update()
                    ->set('token', $token)
                    ->where('email', $email)
                ->execute();

                return $token;
            }
        }
        return false;
    }

    public static function idExist($id) {
        $user = User::select()->where('id', $id)->one();
        return $user ? true : false;
    }

    public static function emailExist($email) {
        $user = User::select()->where('email', $email)->one;
        return $user ? true : false;
    }

    public static function getUser($id, $full = false){
        $data = User::select()->where('id', $id)->one();

        if($data){
            $user = new User();
            $user->id = $data['id'];
            $user->name = $data['name'];
            $user->birthdate = $data['birthdate'];
            $user->city = $data['city'];
            $user->work = $data['work'];
            $user->avatar = $data['avatar'];
            $user->cover = $data['cover'];

            if($full){
                $user->followers = [];
                $user->following = [];
                $user->photos = [];

                $followers = User_Relation::select()->where('userTo', $id)->get();
                foreach($followers as $follower) {
                    $userData = User::select()->where('id', $follower['user_from'])->one();

                    $newUser = new User();
                    $newUser->id = $userData['id'];
                    $newUser->name = $userData['name'];
                    $newUser->avatar = $userData['avatar'];

                    $user->followers[] = $newUser;
                }

                $following = User_Relation::select()->where('user_from', $id)->get();
                foreach($following as $follower) {
                    $userData = User::select()->where('id', $follower['userTo'])->one();

                    $newUser = new User();
                    $newUser->id = $userData['id'];
                    $newUser->name = $userData['name'];
                    $newUser->avatar = $userData['avatar'];

                    $user->following[] = $newUser;
                }

                $user->photos = PostHandler::getPhotosFrom($id);
            }

            return $user;
        }

        return false;
    }

    public static function addUser($name, $email, $password, $birthdate) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $token = md5(time().rand(0, 9999).time());

        User::insert([
            'name' => $name,
            'email' => $email,
            'password' => $hash,
            'birthdate' => $birthdate,
            'token' => $token
        ])->execute();

        return $token;
    }

    public static function isFollowing($from, $to){
        $data = User_Relation::select()
            ->where('user_from', $from)
            ->where('userTo', $to)
        ->one();

        if($data){
            return true;
        }

        return false;
    }

    public static function follow($from, $to){
        User_Relation::insert([
            'user_from' => $from,
            'userTo' => $to
        ])->execute();
    }

    public static function unfollow($from, $to){
        User_Relation::delete()
            ->where('user_from', $from)
            ->where('userTo', $to)
        ->execute();
    }
}