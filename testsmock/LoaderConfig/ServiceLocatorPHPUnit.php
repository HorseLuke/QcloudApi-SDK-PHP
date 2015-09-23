<?php

use QcloudApi\Base\Request;

if(!class_exists('QcloudApi\Integrate\ServiceLocator', false)){
    exit('ACCESS DENIED');
}

$config = array();

$config['Request_config_api_cvm_from_wiki_example'] = function($locator){
    //来源：http://www.qcloud.com/wiki/%E6%8E%A5%E5%8F%A3%E9%89%B4%E6%9D%83
    $request = new Request(array(
        'host' => 'cvm.api.qcloud.com',
        'secretId' => 'AKIDz8krbsJ5yKBZQpn74WFkmLPx3gnPhESA',
        'secretKey' => 'Gu5t9xGARNpq86cd98joQYCN3Cozk1qA',
        'defaultRegion' => '',
    ));
    
    return $request;
};

$config['Request_for_upload'] = function($locator){
    $request = new Request(array(
        'protocol' => 'http',
        'host' => '127.0.0.1',
        'secretId' => '',
        'secretKey' => '',
        'defaultRegion' => '',
    ));
    
    $request->setRequestLogger('fileLogger', $locator->getService('FileRequestLogger'));

    return $request;
};

return $config;
