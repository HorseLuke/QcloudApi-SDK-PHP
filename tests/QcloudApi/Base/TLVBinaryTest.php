<?php

namespace QcloudApi\Base;

/**
 * TLVBinary基础测试
 * @author Horse Luke
 *
 */
class TLVBinaryTest extends \PHPUnit_Framework_TestCase{
    
    /**
     * 
     * @var \QcloudApi\Base\TLVBinary
     */
    protected $tlv;
    
    protected function setUp(){
        parent::setUp();
        $this->tlv = new TLVBinary();
    }
    
    
    public function testCreate(){
        $data = array('type' => 0, 'value' => 'dddddddddddd你好');
        
        $binary = $this->tlv->create($data['type'], $data['value']);
        $tlvPlain = $this->tlv->read($binary);
        
        $this->assertEquals($data['type'], $tlvPlain[0][0]);
        $this->assertEquals($data['value'], $tlvPlain[0][2]);
    }
    
    public function testCreateAndReadToAssoc(){
        $data = array('type' => 0, 'value' => 'dddddddddddd你好');
    
        $binary = $this->tlv->create($data['type'], $data['value']);
        $tlvPlain = $this->tlv->read($binary, true);
    
        $this->assertEquals($data['type'], $tlvPlain[0]['type']);
        $this->assertEquals($data['value'], $tlvPlain[0]['value']);
    }
    
    
    public function testBatchCreateAndReadByAssoc(){
        $data = array(
            array('type' => 0, 'value' => 'tlvdata'),
            array('type' => 1, 'value' => 'tlvdata你好'),
            array('type' => 1, 'value' => null),
        );
        
        $binary = $this->tlv->batchCreate($data);
        $tlvPlain = $this->tlv->read($binary, true);
        
        $this->assertEquals($data[0]['type'], $tlvPlain[0]['type']);
        $this->assertEquals($data[0]['value'], $tlvPlain[0]['value']);
        
        $this->assertEquals($data[1]['type'], $tlvPlain[1]['type']);
        $this->assertEquals($data[1]['value'], $tlvPlain[1]['value']);
        
        $this->assertTrue("" === $tlvPlain[2]['value']);
        $this->assertFalse($data[2]['value'] === $tlvPlain[2]['value']);
    }
    
    public function testBatchCreateAndReadByIndex(){
        $data = array(
            array(0, '你好'),
            array(1, '你好1'),
            array(2, ''),
        );
        
        $binary = $this->tlv->batchCreate($data);
        $tlvPlain = $this->tlv->read($binary);
        
        $this->assertEquals($data[0][0], $tlvPlain[0][0]);
        $this->assertEquals($data[0][1], $tlvPlain[0][2]);
        
        $this->assertEquals($data[1][0], $tlvPlain[1][0]);
        $this->assertEquals($data[1][1], $tlvPlain[1][2]);

        $this->assertTrue($data[2][1] === $tlvPlain[2][2]);
    }
    
    public function testReadBadLength(){
        
        set_error_handler(array($this, 'forErrorHandler'));
        
        $binary = "1111";
        $tlvPlain = $this->tlv->read($binary);
        
        restore_error_handler();
        
        $this->assertEmpty($tlvPlain);
        
    }
    
    public function testReadBadType(){
    
        set_error_handler(array($this, 'forErrorHandler'));
    
        $binary = "1";
        $tlvPlain = $this->tlv->read($binary);
    
        restore_error_handler();
    
        $this->assertEmpty($tlvPlain);
    
    }
    
    
    public function forErrorHandler($errno, $errstr, $errfile, $errline){
        /*
        echo PHP_EOL. PHP_EOL;
        var_export(array($errno, $errstr, $errfile, $errline));
        echo PHP_EOL. PHP_EOL;
        */
    }
    
    
}