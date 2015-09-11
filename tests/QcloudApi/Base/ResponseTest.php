<?php

namespace QcloudApi\Base;

/**
 * Response基础测试
 * @author Horse Luke
 *
 */
class ResponseTest extends \PHPUnit_Framework_TestCase{
    
    protected function setUp(){
        parent::setUp();
    }
    
    public function testGetCode(){
        $response = new Response(array('_no_use' => 1));
        $this->assertEquals(-1, $response->getCode());
        $this->assertFalse($response->isOk());
    }
    
    public function testSetConfigNoUse(){
        $response = new Response(array('_no_use' => 1));
        $this->assertEquals(null, $response->getConfig('_no_use'));
    }
    
    public function testSetError(){
        $error = "ERROR_TEST";
        $errorDetail = 1;
        $response = new Response();
        $response->setError($error, $errorDetail);
        $this->assertFalse($response->isOk());
        $this->assertEquals($error, $response->getError());
        
        $errorRes = $response->getError(true);
        $this->assertEquals($errorDetail, $errorRes['errorDetail']);
    }
    
    public function testCreateWithText(){
        
        $code = 200;
        $rawResult = 'var test=1;';
        
        $response = new Response(array('datatype' => 'text'));
        $response->create($code, $rawResult);
        
        $this->assertEquals('text', $response->getConfig('datatype'));
        $this->assertTrue($response->isOk());
        $this->assertEquals($rawResult, $response->getRawResult());
        
    }
    
    public function testCreate(){
        $code = 200;
        $rawResult = '{"code":0,"message":"","data":[{"projectName":"test1","projectId":1002189,"createTime":"2015-04-28 14:42:53","creatorUin":670569769},{"projectName":"test2","projectId":1002190,"createTime":"2015-04-28 14:42:57","creatorUin":670569769}]}';
        
        $response = new Response();
        $response->create($code, $rawResult);
        
        $this->assertEquals('json', $response->getConfig('datatype'));
        $this->assertTrue($response->isOk());
        $this->assertEquals($rawResult, $response->getRawResult());
        
        $result = $response->getResult();
        $this->assertArrayHasKey('data', $result);
    }
    
    public function testCreateWithError(){
        $code = 403;
        $rawResult = '{"code":4000,"message":"error_test"}';
        
        $response = new Response();
        $response->create($code, $rawResult);
    
        $this->assertEquals('json', $response->getConfig('datatype'));
        $this->assertFalse($response->isOk());
        $this->assertEquals($rawResult, $response->getRawResult());
    
        $error = $response->getError();
        $this->assertEquals('API_RETURN_ERROR_CODE', $error);
    }
    
    public function testCreateWithErrorJSONERROR(){
        $code = 200;
        $rawResult = 'callback([1111, 2222])';
    
        $response = new Response();
        $response->create($code, $rawResult);

        
        $this->assertFalse($response->isOk());
        $this->assertEquals($rawResult, $response->getRawResult());
    
        $error = $response->getError();
        $this->assertEquals('JSON_PARSE_ERROR', $error);
    }
    
    public function testCreateWithErrorEmptyBody(){
        $code = 200;
        $rawResult = '';
    
        $response = new Response();
        $response->create($code, $rawResult);
        
        $this->assertFalse($response->isOk());
        $this->assertEquals($rawResult, $response->getRawResult());
    
        $error = $response->getError();
        $this->assertEquals('HTTP_BODY_EMPTY', $error);
    }
    
    public function testCreateWithErrorCode(){
        $code = 403;
        $rawResult = '{"code":0,"message":""}';
    
        $response = new Response();
        $response->create($code, $rawResult);
    
        $this->assertFalse($response->isOk());
        $this->assertEquals($rawResult, $response->getRawResult());
    
        $error = $response->getError();
        $this->assertEquals('HTTP_CODE_ERROR', $error);
    }
    
    
    public function testGetExtractInfo(){
        $response = new Response();
        $response->setExtractInfo(array('http_code' => 200));
        $this->assertArrayHasKey('http_code', $response->getExtractInfo());
    }
    
}