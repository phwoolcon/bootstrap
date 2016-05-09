<?php

namespace Auth\Controllers;

use Phwoolcon\Auth\Adapter\Exception as AuthException;
use Phwoolcon\Auth\Auth;
use Phwoolcon\Controller;
use Phwoolcon\Log;
use Phwoolcon\Router;

class AccountController extends Controller
{

    protected function checkLoggedInUser()
    {
        return Auth::getInstance()->getUser();
    }

    public function getIndex()
    {
        if (!$user = $this->checkLoggedInUser()) {
            $this->redirect('/user/login');
            return;
        }
        $this->render('account', 'index');
    }

    public function getLogin()
    {
        $this->rememberRedirectUrl();
        $this->addPageTitle(__('Login'));
        $this->render('account', 'login');
    }

    public function postLogin()
    {
        $this->rememberRedirectUrl();
        try {
            Auth::getInstance()->login($this->request->getPost());
            return $this->redirect($this->session->get('redirect_url', url('account'), true));
        } catch (AuthException $e) {
            $this->flashSession->error($e->getMessage());
        } catch (\Exception $e) {
            Log::exception($e);
            $this->flashSession->error(__('Login failed'));
        }
        return $this->redirect('user/login');
    }

    protected function rememberRedirectUrl()
    {
        if (isHttpUrl($url = $this->request->get('redirect_url'))) {
            $this->session->set('redirect_url', $url);
        }
        return $this;
    }
}
