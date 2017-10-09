<?php
namespace Plugin\KaiUConnection\Service;

use Eccube\Common\Constant;

/**
 * Class RecommendService.
 */
class ApiService
{
    /** @var \Eccube\Application */
    public $app;
    /**
     * コンストラクタ
     *
     * @param \Eccube\Application $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    public function callApi($apiUrl)
    {
        
    }
}
