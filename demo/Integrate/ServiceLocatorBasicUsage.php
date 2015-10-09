<?php
/*
 * 使用依赖注入Service Locator调用本SDK的Request
 */

use QcloudApi\Integrate\ServiceLocator;

require __DIR__. '/../common.php';

//（以下代码必须且只需要调用一次，用于初始化依赖注入Service Locator的实例（\QcloudApi\Integrate\ServiceLocator））
$SLConfig = array(
    'configFile' => __DIR__. '/ConfigServiceLocatorDefaultDemo.php',    //配置文件写法见本文件所在文件夹下的ConfigServiceLocatorDefaultDemo.php
);
ServiceLocator::setInstanceDefaultConfig($SLConfig);
//（以上代码必须且只需要调用一次）初始化依赖注入Service Locator的实例


//请注意这里和demo文件/demo/demo.php的不同。
//通过Service Locator，你可以随时调用，而无需重新初始化
$request = ServiceLocator::getInstance()->getService('AccountRequest');


$response = $request->send('DescribeProject', array(), 'GET');

if(!$response->isOk()){
    exit("API Error!:". var_export($response->getError(true)));
}

$result = $response->getResult();
var_export($result);
