<?php
namespace src\handlers;

use \src\models\Post;
 
class PostHandler {

    public static function addPost($idUser, $type, $body){
        
        if($idUser){
            Post::insert([
                'id_user' => $idUser,
                'type' => $type,
                'created_at' => date('Y-m-d H:i:s'),
                'body' => $body
            ])->execute();
        }else{
            print_r('  não passou');
            exit;
        }
    }
}