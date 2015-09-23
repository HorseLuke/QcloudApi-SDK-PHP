<?php

namespace QcloudApi\Example2account;

use QcloudApi\Base\Request;
use QcloudApi\Integrate\ServiceLocator;

trait BaseSetupTestTrait{
    

    /**
     * @var \QcloudApi\Base\Request
     */
    protected $request;
    
    /**
     *
     * @var array
     */
    protected $config;
    
    protected function setUp(){
        parent::setUp();
        
        $this->request = ServiceLocator::getInstance()->getService('AccountRequest');
        
        $secretId = $this->request->getConfig('secretId');
        $secretKey = $this->request->getConfig('secretKey');
        
        if(empty($secretId) || empty($secretKey)){
            $this->markTestSkipped('secretId or secretKey is not set in config file, test will skipped' );
        }
        
    }
    
    
    
}