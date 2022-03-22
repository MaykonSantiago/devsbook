<?php

namespace src\handlers;

use \src\models\Post;
use \src\models\User;
use \src\models\UserRelation;

class PostHandler
{

    public static function addPost($idUser, $type, $body)
    {

        if ($idUser) {
            Post::insert([
                'id_user' => $idUser,
                'type' => $type,
                'created_at' => date('Y-m-d H:i:s'),
                'body' => $body
            ])->execute();
        }
    }

    public function _postListObject($postList, $loggedUserId){
        $post = [];

        foreach ($postList as $postItem) {
            $newPost = new Post();
            $newPost->id = $postItem['id'];
            $newPost->type = $postItem['type'];
            $newPost->created_at = $postItem['created_at'];
            $newPost->body = $postItem['body'];
            $newPost->mine = false;

            if ($postItem['id_user'] == $loggedUserId) {
                $newPost->mine = true;
            }

            //Preencher as informações adicionais no post
            $newUser = User::select()->where('id', $postItem['id_user'])->one();
            $newPost->user = new User();
            $newPost->user->id = $newUser['id'];
            $newPost->user->name = $newUser['name'];
            $newPost->user->avatar = $newUser['avatar'];

            //Preenche informações de likes.
            $newPost->likeCount = 0;
            $newPost->liked = false;

            //Preencher informações dos comentários.
            $newPost->comments = [];

            $post[] = $newPost;
        }

        return $post;
    }

    public static function getUserFeed($idUser, $page, $loggedUserId)
    {
        $perPage = 2;

        //Pegar os posts do usuário logado.
        $postList = Post::select()
            ->where('id_user',$idUser)
            ->orderBy('created_at', 'desc')
            ->page($page, $perPage)
            ->get();

        $total = Post::select()
            ->where('id_user',$idUser)
            ->count();

        $pageCount = ceil($total / $perPage);

        //Transfomar os resultados em objetos do model.
        $post = self::_postListObject($postList, $loggedUserId);

        //Retornar o resultado.
        return [
            'posts' => $post,
            'pageCount' => $pageCount,
            'currentPage' => $page
        ];
    }

    public static function getHomeFeed($idUser, $page)
    {
        $perPage = 2;

        //Pegar lista de usuários que eu sigo e me adicionando a lista.
        $userList = UserRelation::select()->where('user_from', $idUser)->get();
        $users = [];

        foreach ($userList as $userItem) {
            $users[] = $userItem['userTo'];
        }
        $users[] = $idUser;

        //Pegar os posts dos usuários que eu sigo ordenado pela data.
        $postList = Post::select()
            ->where('id_user', 'in', $users)
            ->orderBy('created_at', 'desc')
            ->page($page, $perPage)
            ->get();

        $total = Post::select()
            ->where('id_user', 'in', $users)
            ->count();

        $pageCount = ceil($total / $perPage);

        //Transfomar os resultados em objetos do model.
        $post = self::_postListObject($postList, $idUser);

        //Retornar o resultado.
        return [
            'posts' => $post,
            'pageCount' => $pageCount,
            'currentPage' => $page
        ];
    }

    public static function getPhotosFrom($idUser)
    {
        $photosData = Post::select()
            ->where('id_user', $idUser)
            ->where('type', 'photo')
            ->get();

        $photos = [];

        foreach ($photosData as $photo) {
            $newPost = new Post();
            $newPost->id = $photo['id'];
            $newPost->type = $photo['type'];
            $newPost->created_at = $photo['created_at'];
            $newPost->body = $photo['body'];

            $photos[] = $newPost;
        }

        return $photos;
    }
}
