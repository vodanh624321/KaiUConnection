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

    public function registerSite($config)
    {
        $url = $this->app['config']['KaiUConnection']['const']['api_url'];

        // Create site id
        list($result, $info) = $this->postApi($url, $config);

        $data = json_decode($result, true);
        if (!isset($data['script']) || empty($data['script'])) {
            return false;
        }

        return true;
    }

    public function getSiteList($token)
    {
        $url = $this->app['config']['KaiUConnection']['const']['api_url'];

        // Create site id and get tag
        list($result, $info) = $this->getApi($url, $token);
        
        $data = json_decode($result, true);
        if (!isset($data['sites'])) {
            $this->app->log('KaiUConnection get sites tag error:', $data);

            return false;
        }

        $sites = $data['sites'];

        // usort($sites, function($a, $b) {
        //     if ($a['id'] == $b['id']) {
        //         return 0;
        //     }
        //     return ($a['id'] > $b['id']) ? -1 : 1;
        // });
        
        return array_reverse($sites);
    }

    public function getSiteTag($config, $siteId)
    {
        $url = $this->app['config']['KaiUConnection']['const']['api_url'].'/'.$siteId;

        // get config
        list($result, $info) = $this->getApi($url, $config->getToken());
        $data = json_decode($result, true);

        if (!isset($data['script']) || empty($data['script'])) {
            $this->app->log('KaiUConnection get site config error:', $data);

            return false;
        }
        $configScript = serialize($data['script']);
        $config->setTag($configScript);
        $config->setSiteId($siteId);
        $this->app['orm.em']->persist($config);
        $this->app['orm.em']->flush($config);

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

        // $this->app->log('KaiUConnection request get: ', $info);

        return array($result, $info);        
    }

    public function postApi($url, $config)
    {
        $curl = curl_init($url);

        $options = array(
            //HEADER
            CURLOPT_HTTPHEADER => array(
                'cache-control: no-cache',
                'content-type: application/json',
                'kaiu-auth-token: '.$config->getToken(),
            ),
            CURLOPT_POST => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
        );

        curl_setopt_array($curl, $options);

        // $shopName = $this->app['config']['shop_name'];
        $body = array(
            'site' => array(
                'title' => $config->getName(),
                'url' => $config->getUrl(),
                'pic_sites_attributes' => array(
                    0 => array(
                        "name" => $config->getName(),
                        "email" => $config->getEmail(),
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

        // $this->app->log('KaiUConnection request post: ', $info);

        return array($result, $info);        
    }
}
