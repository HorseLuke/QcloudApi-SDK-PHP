<?php

namespace QcloudApi\Base;

use QcloudApi\Integrate\ServiceLocator;
/**
 * Request基础测试
 * @author Horse Luke
 *
 */
class RequestTest extends \PHPUnit_Framework_TestCase{
    
    /**
     * @var \QcloudApi\Base\Request
     */
    protected $request;
    
    protected function setUp(){
        parent::setUp();
        $this->request = ServiceLocator::getInstance()->getService('Request_config_api_cvm_from_wiki_example');
    }
    
    public function testSetConfigNoUse(){
        $this->request->setConfig(array('_will_not_overwrite' => 1));
        $this->assertEquals(null, $this->request->getConfig('_will_not_overwrite'));
    }
    
    public function testGetConfig(){
        $this->assertEquals('cvm.api.qcloud.com', $this->request->getConfig('host'));
    }
    
    public function testBuildSignature(){
        $param = array(
            'SecretId' => $this->request->getConfig('secretId'),
            'Nonce' => 345122,
            'Region' => 'gz',
            'Timestamp' => 1408704141,
            'Action' => 'DescribeInstances',
        );
        
        $sig = $this->request->buildSignature($param, 'GET');
        
        $this->assertEquals('HgIYOPcx5lN6gz8JsCFBNAWp2oQ=', $sig);
    }
    
    public function testBuildSignaturePOSTWithFile(){
        $param = array(
            'SecretId' => $this->request->getConfig('secretId'),
            'Nonce' => 345122,
            'Region' => 'gz',
            'Timestamp' => 1408704141,
            'Action' => 'DescribeInstances',
            'Filepath' => $this->request->curl_file_create('/tmp/test1.txt'),    //POST时，buildSignature应该忽略该文件上传参数
        );
    
        $sig = $this->request->buildSignature($param, 'POST');
    
        $this->assertEquals('qiEVyAdhwHvQFCCpU5dDef3S8PA=', $sig);
    }
    
    public function testBuildParam(){
        $actionName = 'DescribeInstances';
        $param = array(
            'Instance.0' => '112',
        );
    
        $finalParam = $this->request->buildParam($actionName, $param, 'GET');
        
        $this->assertArrayHasKey('Action', $finalParam);
        $this->assertArrayHasKey('Region', $finalParam);
    }
    
    public function testBuildParamFromStr(){
        $actionName = 'DescribeInstances';
        $param = 'Rest=1';
    
        $finalParam = $this->request->buildParam($actionName, $param, 'GET');
    
        $this->assertArrayHasKey('Action', $finalParam);
        $this->assertArrayHasKey('Region', $finalParam);
    }
    
    public function testBuildParamFromEmpty(){
        $actionName = 'DescribeInstances';
        $param = null;
    
        $finalParam = $this->request->buildParam($actionName, $param, 'GET');
    
        $this->assertArrayHasKey('Action', $finalParam);
        $this->assertArrayHasKey('Region', $finalParam);
    }
    
    public function testCreateUrl(){
        $actionName = 'DescribeInstances';
        $param = array(
            'Region' => 'gz',
            'Action' => 'DescribeInstances',
        );
        
        $url = $this->request->createUrl($actionName, $param, 'GET');
        
        $this->assertStringStartsWith($this->request->createUrl($actionName, $param, 'POST'), $url);
        
    }
    
}