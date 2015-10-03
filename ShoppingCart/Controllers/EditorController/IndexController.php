<?php

namespace Controllers\EditorController;

use Framework\BaseController;

class IndexController extends BaseController {
    /**
     * @Role("Editor")
     */
    public function index(){
        $this->view->appendToLayout('meta', 'meta');
        $this->view->appendToLayout('header', 'header');
        $this->view->appendToLayout('body', 'Editor.IndexController.index');
        $this->view->appendToLayout('footer', 'footer');
        $this->view->displayLayout('Layouts.Editor.home');
    }
}