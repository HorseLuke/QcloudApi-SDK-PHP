<?php

namespace QcloudApi\Base;


class Response{

    
    protected $code = -1;
    
    protected $rawResult;
    
    protected $result;
    
    protected $error;
    
    protected $isOk = false;
    
    protected $errorDetail;
    
    protected $extraInfo;
    
    protected $cfg_datatype = 'json';
    
    /**
     * 配置项：是否允许body体为空
     * @var bool
     */
    protected $cfg_allow_body_empty = false;
    
    /**
     * 初始化对象
     * @param array $config
     */
    public function __construct(array $config = null){
        if(!empty($config)){
            $this->setConfig($config);
        }
    }
    
    /**
     * 批量或单个设置配置
     * @param mixed $key 如果是数组，则批量设置，此时不用传value
     * @param mixed $value
     */
    public function setConfig($key, $value = null)
    {
        if(is_array($key)){
            foreach ($key as $k => $v) {
                $k = 'cfg_' . $k;
                if (!property_exists($this, $k)) {
                    continue;
                }
                $this->{$k} = $v;
            }
            
        }else{
            $k = 'cfg_' . $key;
            if (property_exists($this, $k)) {
                $this->{$k} = $value;
            }
        }

    }
    
    /**
     * 获取配置
     *
     * @param string $k
     * @return string
     */
    public function getConfig($k)
    {
        $k = 'cfg_' . $k;
        return property_exists($this, $k) ? $this->{$k} : null;
    }
    
    
    public function create($code, $rawResult){
        $this->code = $code;
        $this->rawResult = $rawResult;
        
        if (!$this->cfg_allow_body_empty && ($this->rawResult == "" || $this->rawResult == null)) {
            return $this->setError('HTTP_BODY_EMPTY');
        }
        
        switch($this->cfg_datatype){
            case 'json':
                $result = json_decode($rawResult, true);
                if(is_array($result)){
                    $this->result = $result;
                }
                break;
            case 'raw':
                $this->result = $rawResult;
                break;
            default:
                break;
        }
        
       switch($this->cfg_datatype){
            case 'json':
                if(!is_array($this->result)){
                    return $this->setError('JSON_PARSE_ERROR');
                }elseif($this->result['code'] != 0){
                    return $this->setError('API_RETURN_ERROR_CODE', $this->result['code']. ': '. $this->result['message']);
                }
                break;
            default:
                break;
        }
        
        if($this->code != 200){
            return $this->setError('HTTP_CODE_ERROR');
        }
        
        $this->isOk = true;
        return true;
        
    }
    

    public function getCode(){
        return $this->code;
    }

    public function getRawResult(){
        return $this->rawResult;
    }
    
    public function getResult(){
        return $this->result;
    }

    public function getError($withDetail = false){
        if(!$withDetail){
            return $this->error;
        }
        return array('error' => $this->error, 'errorDetail' => $this->errorDetail);
    }
    
    public function isOk(){
        return $this->isOk;
    }
    
    public function setError($error, $errorDetail = null){
        $this->error = $error;
        if(!empty($errorDetail)){
            $this->errorDetail = $errorDetail;
        }
        return false;
    }
    
    public function setExtractInfo($info){
        $this->extraInfo = $info;
    }
    
    public function getExtractInfo(){
        return $this->extraInfo;
    }
    
}