<?php

use QcloudApi\Integrate\FileRequestLogger;

require __DIR__. '/common.php';

/**
 * 调用v2/DescribeProject例子：http://www.qcloud.com/wiki/v2/DescribeProject
 */

//如果用了PSR-4载入方式，以下require_once请删除忽略
require_once __DIR__. '/../src/QcloudApi/Base/Request.php';
require_once __DIR__. '/../src/QcloudApi/Base/Response.php';

$request = new \QcloudApi\Base\Request(array(
    'host' => 'account.api.qcloud.com',
    'secretId' => QCLOUD_API_SECRET_ID,
    'secretKey' => QCLOUD_API_SECRET_KEY,
    'defaultRegion' => 'gz',
    'protocol' => 'https',    //部分接口不支持http，此时需要设置相关参数（也可以在构造时传入）
));

/*
 * 如果需要记录日志，可参照以下代码，
 * 在使用了\QcloudApi\Base\CurlRequestTrait的类中：
 *     - 注入实现了\QcloudApi\Base\CurlRequestLoggerInterface接口类的实例
 *         （\QcloudApi\Integrate\FileRequestLogger为一个示例）
 * 传递的参数请参见方法\QcloudApi\Base\CurlRequestLoggerInterface::receiveSignalRequestLogger()
 */
$fileLogger = new FileRequestLogger(array(
    'logDir' => QCLOUD_FILE_LOGDIR,
));
$request->setRequestLogger('fileLogger', $fileLogger);

$response = $request->send('DescribeProject', array(), 'GET');

if(!$response->isOk()){
    exit("API Error!:". var_export($response->getError(true)));
}

$result = $response->getResult();
var_export($result);
