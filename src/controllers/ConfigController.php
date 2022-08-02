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

    public function config($atts = [])
    {
        $flash = '';
        if(!empty($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            $_SESSION['flash'] = '';
        }

        //Detectando o usuário acessado.
        $id = $this->loggedUser->id;
        if (!empty($atts['id'])) {
            $id = $atts['id'];
        }

        //Pegando informação do usuário.
        $user = UserHandler::getUser($id, true);
        if (!$user) {
            $this->redirect('/');
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
        
        $_SESSION['flash'] = UserHandler::updateUser($user, $newPassword, $confirmPassword);

        $this->redirect('/configuracoes');
    }
}
