<?php
namespace Auth\Controllers;

use Phwoolcon\Controller;
use Phwoolcon\View;

class SsoController extends Controller
{

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
}
