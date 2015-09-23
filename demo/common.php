<?php

exit("comment out this code to run demo");

define('QCLOUD_API_SECRET_ID', "");    //你的secretId
define('QCLOUD_API_SECRET_KEY', "");    //你的secretKey

//如果用了PSR-4载入方式，以下require_once请删除忽略
require_once __DIR__. '/../src/QcloudApi/Base/Request.php';
require_once __DIR__. '/../src/QcloudApi/Base/Response.php';
