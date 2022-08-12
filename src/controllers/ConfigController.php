<?php

namespace src\controllers;

use \core\Controller;
use \src\handlers\UserHandler;
use \src\models\User;

class ConfigController extends Controller
{

    private $loggedUser;

    public function __construct()
    {
        $this->loggedUser = UserHandler::checkLogin();
        if ($this->loggedUser === false) {
            $this->redirect('/login');
        }
    }

    public function config()
    {
        $user = UserHandler::getUser($this->loggedUser->id);

        $flash = '';
        if(!empty($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            $_SESSION['flash'] = '';
        }

        $this->render('configurations', [
            'loggedUser' => $this->loggedUser,
            'user' => $user,
            'flash' => $flash
        ]);
    }

    public function configSave(){
        $name = filter_input(INPUT_POST, 'name');
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $newPassword = filter_input(INPUT_POST, 'newPassword', );
        $confirmPassword = filter_input(INPUT_POST, 'confirmPassword', );
        $birthdate = filter_input(INPUT_POST, 'birthdate');
        $city = filter_input(INPUT_POST, 'city');
        $work = filter_input(INPUT_POST, 'work');
        
         // Detectando usuário acessado
         $id = $this->loggedUser->id;
         if(!empty($atts['id'])) {
             $id = $atts['id'];
         }
         
         // Pegando informações do usuário
        $user = UserHandler::getUser($id, true);
        if(!$user) {
            $this->redirect('/');
        }

        $user = new User();
        $user->id = $id;
        if($name){$user->name = $name;}
        if($email){$user->email = $email;}
        if($birthdate){$user->birthdate = $birthdate;}
        if($city){$user->city = $city;}
        if($work){$user->work = $work;}

        //Avatar
        if(isset($_FILES['avatar']) && !empty($_FILES['avatar']['tmp_name'])){
            $newAvatar = $_FILES['avatar'];
            $avatarName = $this->cutImagem($newAvatar, 200, 200, 'media/avatars');
            $user->avatar = $avatarName;
        }

        //Cover
        if(isset($_FILES['cover']) && !empty($_FILES['cover']['tmp_name'])){
            $newCover = $_FILES['cover'];
            
            if(in_array($newCover['type'], ['image/jpeg', 'image/jpg', 'image/png'])) {
                $coverName = $this->cutImagem($newCover, 850, 310, 'media/covers');
                $user->cover = $coverName;
            }
        }
        
        $_SESSION['flash'] = UserHandler::updateUser($user, $newPassword, $confirmPassword);

        $this->redirect('/configuracoes');
    }

    //Função para manipular imagem
    private function cutImagem($file, $w, $h, $folder){
        //Pegando o tamnho das imagens originais
        list($widthOrig, $heightOrig) = getimagesize($file['tmp_name']);
        $ratio = $widthOrig / $heightOrig;

        $newWidth = $w;
        $newHeight = $h;

        if($newHeight < $h){
            $newHeight = $h;
            $newWidth = $newHeight * $ratio;
        }

        //Calculo pra descobrir quanto da imagem vai ser cortada.
        $x = $w - $newWidth;
        $y = $h - $newHeight;
        //Calculo pra descobrir quanto da imagem vai ser cortada de cada lado pra ficar centralizada.
        //Nunca vai cair no else é colocado  só por segurança.
        $x = $x < 0 ? $x/2 : $x;
        $y = $y < 0 ? $y/2 : $y;

        //Processo para criar a imagem
        $finalImage = imagecreatetruecolor($w, $h);
        switch($file['type']) {
            case 'image/jpeg':
            case 'image/jpg':
                $image = imagecreatefromjpeg($file['tmp_name']);
            break;
            case 'image/png':
                $image = imagecreatefrompng($file['type']);
            break;
        }
        
        imagecopyresampled(
            $finalImage, $image,
            $x, $y, 0, 0,
            $newWidth, $newHeight, $widthOrig, $heightOrig,
        );

        $fileName = md5(time().rand(0,9999)).'.jpg';

        //Salvando a imagem no servidor.
        imagejpeg($finalImage, $folder.'/'.$fileName);

        return $fileName;
    }
}
