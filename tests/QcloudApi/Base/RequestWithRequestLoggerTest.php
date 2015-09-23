<?php

namespace QcloudApi\Base;

use QcloudApi\Integrate\ServiceLocator;
/**
 * Request记录器基础测试
 * @author Horse Luke
 *
 */
class RequestWithRequestLoggerTest extends \PHPUnit_Framework_TestCase{
    
    /**
     *
     * @var QcloudApi\Base\Request
     */
    protected $mockCurlRequestTrait;
    
    protected function setUp(){
        parent::setUp();
        $this->mockCurlRequestTrait = ServiceLocator::getInstance()->getService('Request_for_upload');
    }
    

    public function testSetRequestLoggerByInterface(){
    
        $logger2 = new CurlRequestLoggerInterfaceExtendMock();
        $this->mockCurlRequestTrait->setRequestLogger('anotherLogger', $logger2);
    
        $url = 'http://127.0.0.1/ddd/ddd';
        $response = $this->mockCurlRequestTrait->rawSend($url);
        
        $this->assertEquals($response->getRawResult(), $logger2->responseRawResult);
    
    }
    
    public function testDelRequestLogger(){
        $logger2 = new CurlRequestLoggerInterfaceExtendMock();
    
        $this->mockCurlRequestTrait->setRequestLogger('test2', $logger2);
        $this->mockCurlRequestTrait->delRequestLogger('test2');
    
    }
    
    
}