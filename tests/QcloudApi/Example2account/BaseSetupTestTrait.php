<?php

namespace QcloudApi\Example2account;

use QcloudApi\Base\Request;

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
        
        $this->config = \Testsmoke_Loader::configRead('Request_config_api_account');
        
        if(empty($this->config['secretId']) || empty($this->config['secretKey'])){
            $this->markTestSkipped('secretId or secretKey is not set in config file, test will skipped' );
        }
        
        $this->request = new Request($this->config);
    }
    
    
    
}