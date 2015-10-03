<?php

namespace Controllers;

use Framework\BaseController;
use Framework\Common;
use Models\BindingModels\AuthorizeBindingModel\ChangePasswordBindingModel;
use Models\BindingModels\AuthorizeBindingModel\LoginBindingModel;
use Models\BindingModels\AuthorizeBindingModel\RegisterBindingModel;
use Models\ViewModels\UserViewModel\ProfileViewModel;
use Models\ViewModels\UserViewModel\AllUsersViewModel;
use Models\ViewModels\UserViewModel\User;

class UserController extends BaseController
{
    /**
     * @NotLogged
     * @param LoginBindingModel $model
     * @throws \Exception
     */
    public function login(LoginBindingModel $model)
    {
        $this->db->prepare("
            SELECT id, username, password
            FROM users
            WHERE username = ?",
            [ $model->getUsername() ]);

        $response = $this->db->execute()->fetchRowAssoc();

        if (!$response) {
            throw new \Exception('User not found!', 400);
        }

        if(!password_verify($model->getPassword(), $response['password']))
        {
            throw new \Exception("Incorrect password", 400);
        }

        $id = $response['id'];
        $username = $response['username'];

        $this->session->_login = $id;
        $this->session->_username = $model->getUsername();
        $this->session->escapedUsername = $username;

        $this->redirect($this->path);
    }

    /**
     * @Authorize error:("You are not logged in!")
     * @throws \Exception
     */
    public function logout()
    {
        $this->session->destroySession();
        $this->redirect($this->path);
    }

    /**
     * @NotLogged
     * @param RegisterBindingModel $model
     * @throws \Exception
     */
    public function register(RegisterBindingModel $model)
    {
        if (!preg_match('/^[\w]{3,15}$/', $model->getUsername()))
        {
            throw new \Exception("Invalid username format!", 400);
        }

        if (strlen($model->getPassword()) < 3)
        {
            throw new \Exception("Password should be at least 3 characters long!", 400);
        }

        if($model->getPassword() != $model->getConfirm())
        {
            throw new \Exception("Confirm password does not match!", 400);
        }

        // Check for already registered with the same name
        $this->db->prepare("
            SELECT id
            FROM users
            WHERE username = ?",
            [ $model->getUsername() ]);

        $response = $this->db->execute()->fetchRowAssoc();

        if ($response['id'] !== null) {
            throw new \Exception("Username '{$model->getUsername()}' already taken!", 400);
        }

        $this->db->prepare("
            INSERT INTO users (username, password, cash)
            VALUES (?, ?, ?)",
            [
                $model->getUsername(),
                password_hash($model->getPassword(), PASSWORD_DEFAULT),
                $this->config->shopping_cart['initialCash']
            ])->execute();

        $loginBindingModel = new LoginBindingModel(
            [
                'username' => $model->getUsername(),
                'password' => $model->getPassword()
            ]);
        $this->login($loginBindingModel);
    }

    /**
     * @Get
     * @Route("user/{name:string}/profile")
     */
    public function profile()
    {
        $username = $this->input->getForDb(1);
        $this->db->prepare("
            SELECT id, isAdmin, Cash, isEditor, isModerator
            FROM users
            WHERE username = ?",
            [ $username ]);

        $response = $this->db->execute()->fetchRowAssoc();

        if ($response === false) {
            throw new \Exception("No user found with name '$username'", 404);
        }

        $isAdmin = $response['isAdmin'];
        $isEditor = $response['isEditor'];
        $isModerator = $response['isModerator'];
        $balance = $response['Cash'];

        $this->view->appendToLayout('header', 'header');
        $this->view->appendToLayout('meta', 'meta');
        $this->view->appendToLayout('body', new ProfileViewModel($username, $isAdmin, $balance, $isEditor, $isModerator));
        $this->view->appendToLayout('footer', 'footer');
        $this->view->displayLayout('Layouts.userProfile');
    }

    /**
     * @Authorize
     * @Put
     * @Route("user/changePass")
     * @param ChangePasswordBindingModel $model
     * @throws \Exception
     */
    public function changePassword(ChangePasswordBindingModel $model)
    {
        if (strlen($model->getNewPassword()) < 3)
        {
            throw new \Exception("Password should be at least 3 characters long!", 400);
        }

        if($model->getNewPassword() != $model->getConfirm())
        {
            throw new \Exception("Confirm password does not match!", 400);
        }

        $username = $this->session->_username;
        $id = $this->session->_login;

        $this->db->prepare("
            SELECT id, password
            FROM users
            WHERE id = ? AND username = ?",
            [ $id, $username ]);

        $response = $this->db->execute()->fetchRowAssoc();

        if(!password_verify($model->getOldPassword(), $response['password']))
        {
            throw new \Exception("Incorrect old password", 400);
        }

        $this->db->prepare("
            UPDATE users
            SET password = ?
            WHERE id = ? AND username = ?",
            [ password_hash($model->getNewPassword(), PASSWORD_DEFAULT), $id, $username ]);

        $this->db->execute();
        $this->redirect($this->path);
     }

    /**
     * @Route("users/all/{start:int}/{end:int}")
     * @Get
     */
    public function allUsers()
    {
        $skip = $this->input->get(2);
        $take = $this->input->get(3) - $skip;

        $this->db->prepare("
            SELECT username,isAdmin, isEditor, isModerator
            FROM users
            ORDER BY username LIMIT {$take} OFFSET {$skip}");

        $response = $this->db->execute()->fetchAllAssoc();
        $users = [];

        foreach ($response as $user) {
            $users[] = new User(
                $user['username'],
                Common::normalize($user['isAdmin'], 'noescape|bool'),
                Common::normalize($user['isEditor'], 'noescape|bool'),
                Common::normalize($user['isModerator'], 'noescape|bool')
            );
        }

        $this->view->appendToLayout('meta', 'meta');
        $this->view->appendToLayout('header', 'header');
        $this->view->appendToLayout('body', new AllUsersViewModel($users, $skip, $take + $skip));
        $this->view->appendToLayout('footer', 'footer');
        $this->view->displayLayout('Layouts.home');
    }
}