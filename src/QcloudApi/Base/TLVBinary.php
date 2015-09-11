<?php

namespace QcloudApi\Base;

/**
 * 
 * @author Horse Luke
 * @link http://blog.csdn.net/chexlong/article/details/6974201
 * @link http://blog.csdn.net/xm1331305/article/details/38514313
 *
 */
class TLVBinary{
    
    protected $typeBinaryLen = 4;
    
    protected $lengthBinaryLen= 4;
    
    /**
     * 创建单个二进制TLV包，不需要传递值长度
     * @param int $type 类型
     * @param int|string $value 值
     * @return string
     */
    public function create($type, $value){
        return pack("N", $type). pack("N", strlen($value)). $value;
    }
    
    /**
     * 创建多个二进制TLV包
     * @param array $data 待组装的TLV多个数组，其中每个单元为1个array。
     * 使用数组索引时，依次为类型和值，比如：
     *     array(
     *         array(0, '数据1'),
     *         array(0, '数据2'),
     *     )
     * 使用关联索引时，type为类型，value为值，比如：
     *     array(
     *         array('type' => 0, 'value' => '数据1'),
     *         array('type' => 1, 'value' => '数据2'),
     *     )
     * 以上均不需要传递值长度
     * @return string
     */
    public function batchCreate($data){
        $msg = "";
        foreach($data as $row){
            if(isset($row['type'])){
                $msg .= $this->create($row['type'], $row['value']);
            }else{
                $msg .= $this->create($row[0], $row[1]);
            }
        }
        return $msg;
    }
    
    /**
     * 读取一个二进制包
     * @param string $binary
     * @param bool $assoc 返回的结果，每行是返回关联索引么？默认false，返回数字索引
     * @return array
     */
    public function read($binary, $assoc = false){
        $binaryLength = strlen($binary);
        $result = array();
        
        $i=0;
        while($i < $binaryLength){
            //获取type
            $tlvType = $this->getValueOfTL($binary, $i, $this->typeBinaryLen);
            if($tlvType === false){
                break;
            }
            $i += $this->typeBinaryLen;
            
            //获取Length
            $tlvLength = $this->getValueOfTL($binary, $i,$this->lengthBinaryLen);
            if($tlvLength === false){
                break;
            }
            $i += $this->lengthBinaryLen;
            
            //获取Value
            if($tlvLength > 0){
                $tlvValue = substr($binary, $i, $tlvLength);
                $i += $tlvLength;
            }else{
                $tlvValue = "";
            }
            
            if($assoc){
                $result[] = array('type' => $tlvType, 'length' => $tlvLength, 'value' =>$tlvValue);
            }else{
                $result[] = array($tlvType, $tlvLength, $tlvValue);
            }
            
        }
        
        return $result;
    }
    
    /**
     * 获取type或者value的值
     * @param string $binary
     * @param int $start
     * @param int $len
     * @return bool|int
     */
    protected function getValueOfTL($binary, $start, $len){
        $subStr = substr($binary, $start, $len);
        $unpacked = unpack("N", $subStr);
        return isset($unpacked[1]) ? $unpacked[1] : false;
    }
    
}
