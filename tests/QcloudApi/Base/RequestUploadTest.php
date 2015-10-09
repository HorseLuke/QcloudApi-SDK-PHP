<?php

namespace QcloudApi\Base;

use QcloudApi\Integrate\ServiceLocator;
/**
 * @author Horse Luke
 *
 */
class RequestUploadTest extends \PHPUnit_Framework_TestCase{
    
    /**
     * 
     * @var QcloudApi\Base\Request
     */
    protected $mockCurlRequestTrait;
    
    protected function setUp(){
        parent::setUp();
        $this->mockCurlRequestTrait = ServiceLocator::getInstance()->getService('Request_for_upload');
    }
    

    public function testSendFile(){
 
        $param = array(
            'name' => 'test',
            'file' => $this->mockCurlRequestTrait->curl_file_create(D_APP_DIR. '/Assets/1.txt'),
        );
    
        $url = 'http://127.0.0.1/other/file_upload/file_upload1.php';
        
        //特殊测试才需要手动构造$response
        $response = new Response(array(
            'datatype' => 'raw',
        ));
    
        //rawSend之后，其实会返回$response
        $response = $this->mockCurlRequestTrait->rawSend($url, $param, 'POST', $response);
        
        if(!$response->isOk()){
            $this->assertTrue(in_array($response->getError(), array('HTTP_CODE_ERROR', 'CURL_ERROR')));
        }else{
            $result = $response->getResult();
            $this->assertTrue(stripos($result, 'FILE_UPLOAD_OK') !== false);
        }
        
    }
    
    public function testSendFileToUnsupportedProtocol(){
        $param = array(
            'file' => $this->mockCurlRequestTrait->curl_file_create(D_APP_DIR. '/Assets/1.txt'),
            'name' => 'test',
        );
        
        $url = 'xftp://127.0.0.1/56stgseaafs/asfasdf34tr/asdfasdfgadg/dfghgdfgdaf/asfdadsfasdf/fdhgdh/wqrwerafdsgdsfg/sdgsdfgfdsg/sdgsdfga';
        
        $response = $this->mockCurlRequestTrait->rawSend($url, $param, 'POST');
        
        $this->assertEquals('CURL_ERROR', $response->getError());
        
    }
    
}