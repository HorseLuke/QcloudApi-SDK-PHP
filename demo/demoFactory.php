<?php

exit("comment out this code to run demo");

//如果用了PSR-4载入方式，以下require_once请删除忽略
require_once __DIR__. '/../src/QcloudApi/Base/Request.php';
require_once __DIR__. '/../src/QcloudApi/Base/Response.php';

class QcloudApiFactory{
    private static $instance = array();
    
    private static $config = null;

    /**
     * 
     * @param string $host
     * @return \QcloudApi\Base\Request
     */
    public static function getInstance($host){
        if(isset(self::$instance[$host])){
            return self::$instance[$host];
        }
        
        self::$instance[$host] = new \QcloudApi\Base\Request(self::getConfig($host));
        
        return self::$instance[$host];
    }
    
    private static function getConfig($host){
        
        if(null === self::$config){
            $secretId = "";
            $secretKey = "";
            self::$config = array();
            
            self::$config['account.api.qcloud.com'] = array();
            self::$config['account.api.qcloud.com']['protocol'] = 'https';
            self::$config['account.api.qcloud.com']['host'] = 'account.api.qcloud.com';
            self::$config['account.api.qcloud.com']['secretId'] = $secretId;
            self::$config['account.api.qcloud.com']['secretKey'] = $secretKey;
            self::$config['account.api.qcloud.com']['defaultRegion'] = 'gz';
            
            
        }
        
        return isset(self::$config[$host]) ? self::$config[$host] : array();
    }


}


$request = QcloudApiFactory::getInstance('account.api.qcloud.com');
$response = $request->send('DescribeProject');

if(!$response->isOk()){
    exit("API Error!:". var_export($response->getError(true)));
}

$result = $response->getResult();
var_export($result);


