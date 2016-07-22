<?php

namespace Auth\Controllers;

use User;
use Phwoolcon\Auth\Adapter\Exception as AuthException;
use Phwoolcon\Auth\Auth;
use Phwoolcon\Controller;
use Phwoolcon\Log;
use Phwoolcon\Router;

class AccountController extends Controller
{

    protected function checkLoggedInUser()
    {
        return Auth::getUser();
    }

    public function getActivate()
    {
        if (!$user = Auth::getInstance()->activatePendingConfirmationUser($this->input('auth.confirm'))) {
            $this->render('page', 'single-message', ['message' => __('Account Confirmation Failed')]);
            return;
        }
        $this->flashSession->success(__('Account Activated Successfully'));
        $this->redirect('sso/redirect');
    }

    public function getConfirm()
    {
        if (!($userData = Auth::getInstance()->getPendingConfirmationData()) ||
            !($uid = fnGet($userData, 'id')) ||
            User::findFirstSimple(['id' => $uid])
        ) {
            $this->flashSession->error(__('Account Confirmation Failed'));
            $this->redirect('sso/redirect');
            return;
        }
        $this->addPageTitle(__('Account Confirmation'));
        $this->render('account', 'confirm', [
            'user_data' => $userData,
        ]);
    }

    public function getForgotPassword()
    {
        $this->addPageTitle(__('Forgot password'));
        $this->render('account', 'forgot-password');
    }

    public function getIndex()
    {
        if (!$user = $this->checkLoggedInUser()) {
            $this->flashSession->error(__('Please login first'));
            $this->redirect('account/login');
            return;
        }
        $this->addPageTitle(__('My Account'));
        $this->render('account', 'index');
    }

    public function getLogin()
    {
        $this->rememberRedirectUrl();
        $this->addPageTitle(__('Login'));
        $this->render('account', 'login');
    }

    public function getLogout()
    {
        $this->rememberRedirectUrl();
        Auth::getInstance()->logout();
        $this->flashSession->success(__('Logout success'));
        return $this->redirect(url('sso/redirect'));
    }

    public function getRegister()
    {
        $this->rememberRedirectUrl();
        $this->addPageTitle(__('Register'));
        $this->render('account', 'register');
    }

    public function postForgotPassword()
    {
    }

    public function postLogin()
    {
        $this->rememberRedirectUrl();
        $credential = $this->request->getPost('auth');
        try {
            Auth::getInstance()->login($credential);
            $this->session->clearFormData('auth_retry');
            $this->flashSession->success(__('Login success'));
            return $this->redirect(url('sso/redirect'));
        } catch (AuthException $e) {
            $this->flashSession->error($e->getMessage());
        } catch (\Exception $e) {
            Log::exception($e);
            $this->flashSession->error(__('Login failed'));
        }
        unset($credential['password']);
        $this->session->rememberFormData('auth_retry', $credential);
        return $this->redirect('account/login');
    }

    public function postRegister()
    {
        $this->rememberRedirectUrl();
        $credential = $this->request->getPost('register');
        try {
            if (!$this->request->getPost('terms')) {
                throw new AuthException(__('Please agree to Terms of Service'));
            }
            $user = Auth::getInstance()->register($credential);
            $this->session->clearFormData('register_retry');
            if ($user->getData('confirmed')) {
                $this->flashSession->success(__('Register success'));
                return $this->redirect(url('sso/redirect'));
            }
            $this->flashSession->success(__('Register success, but confirmation required'));
            return $this->redirect(url('account/confirm'));
        } catch (AuthException $e) {
            $this->flashSession->error($e->getMessage());
        } catch (\Exception $e) {
            Log::exception($e);
            $this->flashSession->error(__('Register failed'));
        }
        unset($credential['password'], $credential['confirm_password']);
        $this->session->rememberFormData('register_retry', $credential);
        return $this->redirect('account/register');
    }

    protected function rememberRedirectUrl()
    {
        if (($url = $this->request->get('redirect_url')) && isHttpUrl($url)) {
            $this->session->set('redirect_url', $url);
        }
        return $this;
    }
}
