<?php
namespace Controllers\AdminController;

use Framework\BaseController;
use Models\ViewModels\AdminViewModel\IndexViewModel;
use Models\BindingModels\RightsBindingModel;

class IndexController extends BaseController
{
    /**
     * @Admin
     * @throws \Exception
     */
    public function index()
    {
        $this->db->prepare("
            SELECT username
            FROM users
            WHERE isAdmin = 1");

        $response = $this->db->execute()->fetchAllAssoc();
        $admins = [];

        foreach ($response as $admin) {
            $admins[] = $admin['username'];
        }

        $this->view->appendToLayout('meta', 'meta');
        $this->view->appendToLayout('header', 'header');
        $this->view->appendToLayout('body', new IndexViewModel($admins));
        $this->view->appendToLayout('footer', 'footer');
        $this->view->displayLayout('Layouts.Admin.home');
    }

    /**
     * @Admin
     * @throws \Exception
     */
    public function edit()
    {
        $this->view->appendToLayout('meta', 'meta');
        $this->view->appendToLayout('header', 'header');
        $this->view->appendToLayout('body', 'Admin.IndexController.edit');
        $this->view->appendToLayout('footer', 'footer');
        $this->view->displayLayout('Layouts.Admin.home');
    }

    /**
     * @Admin
     * @param RightsBindingModel $model
     */
    public function add(RightsBindingModel $model)
    {
        $username = $model->getUsername();
        $right = ucfirst(strtolower($model->getRightName()));

        $this->db->prepare("
            UPDATE users
            SET is{$right} = 1
            WHERE username = ?",
            [ $username ]);
        $this->db->execute();

        $this->redirect($this->path . 'admin');
    }

    /**
     * @Admin
     * @param RightsBindingModel $model
     */
    public function remove(RightsBindingModel $model)
    {
        $username = $model->getUsername();
        $right = ucfirst(strtolower($model->getRightName()));

        $this->db->prepare("
            UPDATE users
            SET is{$right} = 0
            WHERE username = ?",
            [ $username ]);
        $this->db->execute();

        $this->redirect($this->path . 'admin');
    }
}