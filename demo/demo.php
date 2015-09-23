<?php

exit("comment out this code to run demo");

/**
 * 调用v2/DescribeProject例子：http://www.qcloud.com/wiki/v2/DescribeProject
 */

//如果用了PSR-4载入方式，以下require_once请删除忽略
require_once __DIR__. '/../src/QcloudApi/Base/Request.php';
require_once __DIR__. '/../src/QcloudApi/Base/Response.php';

$request = new \QcloudApi\Base\Request(array(
    'host' => 'account.api.qcloud.com',
    'secretId' => '你的secretId',
    'secretKey' => '你的secretKey',
    'defaultRegion' => 'gz',
));

//部分接口不支持http，此时需要设置相关参数（也可以在构造时传入）
$request->setConfig(array(
    'protocol' => 'https',
));

$response = $request->send('DescribeProject', array(), 'GET');

if(!$response->isOk()){
    exit("API Error!:". var_export($response->getError(true)));
}

$result = $response->getResult();
var_export($result);