<?php
namespace Auth\Controllers;

use Auth\Model\SsoSite;
use Exception;
use Phwoolcon\Auth\Auth;
use Phwoolcon\Crypt;
use Phwoolcon\Log;
use Phwoolcon\View;

class SsoController extends AccountController
{

    protected function checkInitToken($initTime, $initToken, $site)
    {
        return $initToken == md5(md5(fnGet($site, 'id') . $initTime) . fnGet($site, 'site_secret'));
    }

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
            $this->setBrowserCache($pageId, 'all', 3600);
        }
    }

    public function getRedirect()
    {
        $this->rememberRedirectUrl();
        $this->addPageTitle(__('Redirecting'));
        $url = $this->session->get('redirect_url', url('account'), true);
        if ($this->request->get('_immediately')) {
            return $this->redirect($url);
        }
        return $this->render('sso', 'redirect', [
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
        $this->jsonReturn($ssoData = $this->getSsoData($input), isset($ssoData['error']) ? 400 : 200);
    }

    protected function getSsoData($input)
    {
        try {
            $site = SsoSite::getSiteByReturnUrl(fnGet($input, 'notifyUrl'));
            $initToken = fnGet($input, 'initToken');
            $initTime = fnGet($input, 'initTime');
            if (!$this->checkInitToken($initTime, $initToken, $site)) {
                return ['error' => 'Invalid init token'];
            }
            if ($user = Auth::getUser()) {
                $ssoData = ['user_data' => $user->getData()];
                return $ssoData;
            }
            return ['user_data' => false];
        } catch (Exception $e) {
            Log::exception($e);
            return [];
        }
    }
}
