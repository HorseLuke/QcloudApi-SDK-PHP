<?php

exit("comment out this code to run demo");

/**
 * 调用v2/DescribeProject例子，但返回原始数据供自行判断：http://www.qcloud.com/wiki/v2/DescribeProject
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

 //如果你想自行处理所有Response返回数据，则需要自行创建Response体，然后传入：
 $response = new \QcloudApi\Base\Response(array(
     'datatype' => 'text'
 ));
 $request->send('DescribeProject', array(), 'GET', $response);

 //注意：此时不会检查json体内的错误，仅检查是否存在网络错误！
if(!$response->isOk()){
    exit("API Error!:". var_export($response->getError(true)));
}

$rawResult = $response->getRawResult();
var_export($rawResult);
