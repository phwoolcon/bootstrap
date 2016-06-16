<?php
namespace Auth\Controllers;

use Phwoolcon\Auth\Auth;
use Phwoolcon\Auth\Controller\SsoTrait;
use Phwoolcon\Model\User;
use Phwoolcon\View;

class SsoController extends AccountController
{
    use SsoTrait;

    public function getCheckIframe()
    {
        $pageId = $this->request->getURI();
        $eTag = $this->request->getHeader('If-None-Match');
        if ($eTag && $eTag == $this->getBrowserCache($pageId, static::BROWSER_CACHE_ETAG)) {
            $this->response->setStatusCode(304);
            $this->setBrowserCacheHeaders($eTag, 3600);
        } else if ($content = $this->getBrowserCache($pageId, static::BROWSER_CACHE_CONTENT)) {
            $this->response->setContent($content);
            $this->setBrowserCacheHeaders($this->getContentEtag($content), 3600);
        } else {
            View::noHeader(true);
            View::noFooter(true);
            $this->render('sso', 'iframe');
            $this->setBrowserCache($pageId, static::BROWSER_CACHE_ETAG | static::BROWSER_CACHE_CONTENT, 3600);
        }
    }

    public function getRedirect()
    {
        $this->rememberRedirectUrl();
        $this->addPageTitle(__('Redirecting'));
        $url = $this->session->get('redirect_url', url('account'), true);
        if ($this->request->get('_immediately')) {
            $this->redirect($url);
            return;
        }
        $this->render('sso', 'redirect', [
            'config' => [
                'url' => $url,
                'timeout' => Auth::getOption('redirect_timeout') * 1000,
                'uid' => Auth::getUser() ? Auth::getUser()->getId() : null,
                // TODO Use session TTL instead of 0
                'uidTtl' => Auth::getUser() ? 0 : 0,
            ],
        ]);
    }

    public function postServerCheck()
    {
        $input = $this->request->get();
        $ssoData = $this->getSsoUserData($input);
        if (fnGet($ssoData, 'error')) {
            $this->jsonReturn($ssoData, 400);
            return;
        }
        if (($user = fnGet($ssoData, 'user')) instanceof User) {
            unset($ssoData['user']);
            // TODO Set extra sso data
        }
        $this->jsonReturn($ssoData, 200);
    }
}
