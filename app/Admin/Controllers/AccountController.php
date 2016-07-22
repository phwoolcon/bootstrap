<?php

namespace Admin\Controllers;

use Phwoolcon\Controller;
use Phwoolcon\Controller\Admin;
use Phwoolcon\Router;

class AccountController extends Controller
{
    use Admin;

    public function getIndex()
    {
        $this->render('account', 'index');
    }

    public function getLogin()
    {
        $this->addPageTitle(__('Login'));
        $this->render('account', 'login');
    }

    public function postLogin()
    {
    }

    public function missingMethod()
    {
        Router::throw404Exception();
    }
}
