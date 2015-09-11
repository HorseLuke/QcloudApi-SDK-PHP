<?php

if(!class_exists('PHPUnit_TextUI_Command', false)){
    exit('THIS IS FOR PHPUNIT RUN ONLY');
}

require __DIR__ . '/testsmock/Class/Testsmoke_Loader.php';

Testsmoke_Loader::regLoadClassPath("QcloudApi_src", __DIR__. '/src');
Testsmoke_Loader::regLoadClassPath("testcase", __DIR__. '/tests');

Testsmoke_Loader::define(array(
    'D_APP_DIR' => __DIR__ . '/testsmock',
    'D_ENTRY_FILE' => __FILE__,
    'D_ENV' => 'Dev',
));

$printPHPUnit = function($buffer = ""){
    echo PHP_EOL;
    if(!empty($buffer)){
        echo "\x1b[30;42m". $buffer. "\x1b[0m";
    }
};

$printPHPUnit();
$printPHPUnit("PHPUnit Test Prepare OK");
$printPHPUnit();
$printPHPUnit();
