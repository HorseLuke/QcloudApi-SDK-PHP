<?php

exit("comment out this code to run demo");

define('QCLOUD_API_SECRET_ID', "");    //你的secretId
define('QCLOUD_API_SECRET_KEY', "");    //你的secretKey
define('QCLOUD_FILE_LOGDIR', "/media/ramdisk/");

//如果用了PSR-4载入方式，以下代码请勿使用；否则，必须有且只有使用一次。
require_once __DIR__. '/../src/QcloudApi/Integrate/Loader.php';
\QcloudApi\Integrate\Loader::getInstance()->reg2SPL();
//如果用了PSR-4载入方式，以上代码请勿使用；否则，必须有且只有使用一次。