<?php

namespace Admin\Controllers;

use Phwoolcon\Controller\Admin;
use Phwoolcon\Router;

class AccountController extends Admin
{

    public function getIndex()
    {
        $this->render('admin', 'index');
    }

    public function getLogin()
    {
        $this->addPageTitle(__('Login'));
        $this->render('admin', 'login');
    }

    public function postLogin()
    {}

    public function missingMethod()
    {
        Router::throw404Exception();
    }
}
