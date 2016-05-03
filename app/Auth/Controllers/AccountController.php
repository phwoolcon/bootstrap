<?php

namespace Auth\Controllers;

use Phwoolcon\Auth\Auth;
use Phwoolcon\Controller;
use Phwoolcon\Router;

class AccountController extends Controller
{

    protected function checkStatus()
    {
        return Auth::getInstance()->getUser();
    }

    public function getIndex()
    {
        if (!$user = $this->checkStatus()) {
            $this->redirect('/user/login');
            return;
        }
        $this->render('account', 'index');
    }

    public function getLogin()
    {
        $this->addPageTitle(__('Login'));
        $this->render('account', 'login');
    }

    public function postLogin()
    {}

    public function missingMethod()
    {
        Router::throw404Exception();
    }
}
