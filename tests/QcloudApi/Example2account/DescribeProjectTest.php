<?php

namespace QcloudApi\Example2account;

/**
 * Request测试：DescribeProject
 * @author Horse Luke
 *
 */
class DescribeProjectTest extends \PHPUnit_Framework_TestCase{

    use BaseSetupTestTrait;
    
    protected $actionName = 'DescribeProject';
    
    public function testBasicUsage(){
        $response = $this->request->send($this->actionName);
        if(!$response->isOk()){
            $this->fail(
                "RESPONSE_HAS_ERROR. "
                . "ERROR INFO:". $response->getError(). PHP_EOL
                . "RAW HTTP RETURN BODY:". PHP_EOL.  $response->getRawResult(). PHP_EOL
            );
        }
        
        $result = $response->getResult();
        
        $this->assertArrayHasKey('data', $result);
    }
    

}