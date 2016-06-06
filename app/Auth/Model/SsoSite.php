<?php
namespace Auth\Model;

use Phwoolcon\Cache;
use Phwoolcon\Log;
use Phwoolcon\Model;

class SsoSite extends Model
{
    protected static $_sites;
    protected $_table = 'sso_sites';
    protected $_useDistributedId = false;

    public static function getSites()
    {
        if ((null === static::$_sites) && null === (static::$_sites = Cache::get($cacheKey = 'sso.sites'))) {
            /* @var static $site */
            $data = [];
            foreach (static::find() as $site) {
                if (!($url = $site->getData('site_url')) || !($host = parse_url($url, PHP_URL_HOST))) {
                    continue;
                }
                $site->setData('host', $host);
                $data[$host] = $site->getData();
            }
            static::$_sites = $data;
            Cache::set($cacheKey, $data);
        }
        return static::$_sites;
    }

    public static function getSiteByReturnUrl($returnUrl)
    {
        $sites = static::getSites();
        $host = parse_url($returnUrl, PHP_URL_HOST);
        return isset($sites[$host]) ? $sites[$host] : null;
    }
}
