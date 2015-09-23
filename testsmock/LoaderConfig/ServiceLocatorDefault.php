<?php

use QcloudApi\Base\Request;
use QcloudApi\Integrate\FileRequestLogger;

if(!class_exists('QcloudApi\Integrate\ServiceLocator', false)){
    exit('ACCESS DENIED');
}

$config = array();

//创建一个基于account.api.qcloud.com、默认区域在gz的Request实例
$config['AccountRequest'] = function($locator){
    $request = new Request(array(
        'protocol' => 'https',
        'host' => 'account.api.qcloud.com',
        'secretId' => '',
        'secretKey' => '',
        'defaultRegion' => 'gz',
    ));
    
    $request->setRequestLogger('fileLogger', $locator->getService('FileRequestLogger'));
    
    return $request;
};

//创建一个基于云安全（csec.api.qcloud.com）、默认区域在all的Request实例
$config['CsecRequest'] = function($locator){
    $request = new Request(array(
        'protocol' => 'https',
        'host' => 'csec.api.qcloud.com',
        'secretId' => '',
        'secretKey' => '',
        'defaultRegion' => 'all',
    ));
    
    $request->setRequestLogger('fileLogger', $locator->getService('FileRequestLogger'));
    
    return $request;
};


$config['FileRequestLogger'] = function($locator){
    $fileLogger = new FileRequestLogger(array(
        'logDir' => "",
    ));

    return $fileLogger;
};


return $config;
