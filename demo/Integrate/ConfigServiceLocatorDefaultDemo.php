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
        'secretId' => QCLOUD_API_SECRET_ID,
        'secretKey' => QCLOUD_API_SECRET_KEY,
        'defaultRegion' => 'gz',
    ));
    
    /*
     * 如果需要记录日志，可参照以下代码，
     * 在使用了\QcloudApi\Base\CurlRequestTrait的类中：
     *     - 注入实现了\QcloudApi\Base\CurlRequestLoggerInterface接口类的实例
     *         （\QcloudApi\Integrate\FileRequestLogger为一个示例）
     * 传递的参数请参见方法\QcloudApi\Base\CurlRequestLoggerInterface::receiveSignalRequestLogger()
     */
    $request->setRequestLogger('fileLogger', $locator->getService('FileRequestLogger'));
    
    return $request;
};

//创建一个基于云安全（csec.api.qcloud.com）、默认区域在all的Request实例
$config['CsecRequest'] = function($locator){
    $request = new Request(array(
        'protocol' => 'https',
        'host' => 'csec.api.qcloud.com',
        'secretId' => QCLOUD_API_SECRET_ID,
        'secretKey' => QCLOUD_API_SECRET_KEY,
        'defaultRegion' => 'all',
    ));
    
    $request->setRequestLogger('fileLogger', $locator->getService('FileRequestLogger'));
    
    return $request;
};


$config['FileRequestLogger'] = function($locator){
    $fileLogger = new FileRequestLogger(array(
        'logDir' => QCLOUD_FILE_LOGDIR,
    ));
    
    return $fileLogger;
};

return $config;
