<?php
namespace Plugin\KaiUConnection\Service;

use Eccube\Common\Constant;

/**
 * Class ApiService.
 */
class ApiService
{
    /** @var \Eccube\Application */
    public $app;

    /**
     * contruct
     *
     * @param \Eccube\Application $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    public function checkToken($token)
    {
        $url = $this->app['config']['KaiUConnection']['const']['api_url'];

        list($result, $info) = $this->getApi($url, $token);
        $data = json_decode($result, true);

        if (!isset($data['sites']) || empty($data['sites'])) {
            return false;
        }

        return true;
    }

    public function registerSite($tag)
    {
        $url = $this->app['config']['KaiUConnection']['const']['api_url'];

        // Create site id
        list($result, $info) = $this->postApi($url, $tag);

        $data = json_decode($result, true);
        if (!isset($data['script']) || empty($data['script'])) {
            return false;
        }

        return true;
    }

    public function getSiteId($tag)
    {
        $url = $this->app['config']['KaiUConnection']['const']['api_url'];

        // Create site id and get tag
        list($result, $info) = $this->getApi($url, $tag->getToken());
        
        $data = json_decode($result, true);
        if (!isset($data['sites'])) {
            $this->app->log('Get sites tag error:', $data);

            return false;
        }

        $sites = $data['sites'];
        if (!function_exists('array_column')) {
            $siteUrl = array_map(function($element) {
                return $element['url'];
            }, $sites);
        } else {
            $siteUrl = array_column($sites, 'url');
        }

        $index = array_search($tag->getSiteUrl(), $siteUrl);
        if ($index === false) {
            $this->app->log('Cannot found site id!');

            return false;
        }
        $tag->setSiteId($sites[$index]['id']);
        $this->app['kaiu.repository.tag']->save($tag);

        return true;
    }

    public function getSiteTag($tag)
    {
        if (!$tag->getSiteId()) {
            return false;
        }
        $url = $this->app['config']['KaiUConnection']['const']['api_url'].'/'.$tag->getSiteId();

        // get tag
        list($result, $info) = $this->getApi($url, $tag->getToken());
        $data = json_decode($result, true);

        if (!isset($data['script']) || empty($data['script'])) {
            $this->app->log('Get site tag error:', $data);

            return false;
        }
        $tagScript = serialize($data['script']);

        $tag->setTag($tagScript);
        $this->app['kaiu.repository.tag']->save($tag);

        return true;
    }

    public function getApi($url, $token)
    {
        $curl = curl_init($url);

        $options = array(
            //HEADER
            CURLOPT_HTTPHEADER => array(
                'cache-control: no-cache',
                'content-type: application/json',
                'kaiu-auth-token: '.$token,
            ),
            CURLOPT_HTTPGET => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
        );

        curl_setopt_array($curl, $options);

        $result = curl_exec($curl);
        $info = curl_getinfo($curl);

        $message = curl_error($curl);
        $info['message'] = $message;
        curl_close($curl);

        $this->app->log('KaiUConnection request get: ', $info);

        return array($result, $info);        
    }

    public function postApi($url, $tag)
    {
        $curl = curl_init($url);

        $options = array(
            //HEADER
            CURLOPT_HTTPHEADER => array(
                'cache-control: no-cache',
                'content-type: application/json',
                'kaiu-auth-token: '.$tag->getToken(),
            ),
            CURLOPT_POST => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
        );

        curl_setopt_array($curl, $options);

        $shopName = $this->app['config']['shop_name'];
        $body = array(
            'site' => array(
                'title' => $shopName,
                'url' => $tag->getSiteUrl(),
                'pic_sites_attributes' => array(
                    0 => array(
                        "name" => $tag->getSiteName(),
                        "email" => $tag->getEmail(),
                        "report_flag" => "0"
                        ),
                    ),
                ),
            );
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($body));

        $result = curl_exec($curl);
        $info = curl_getinfo($curl);

        $message = curl_error($curl);
        $info['message'] = $message;
        curl_close($curl);

        $this->app->log('KaiUConnection request post: ', $info);

        return array($result, $info);        
    }
}
