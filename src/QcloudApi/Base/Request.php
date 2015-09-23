<?php

namespace QcloudApi\Base;

/**
 * 腾讯云API请求实体（一体化紧凑型）
 * @author Horse Luke
 *
 */
class Request{
    
    /**
     * 接口协议
     * @var string
     */
    protected $protocol = 'http';
    
    /**
     * (curl)关闭ssl证书verify
     * $protocol为https有效
     * @var string
     */
    protected $cfg_curlDisableSslVerify = true;
    
    /**
     * 等同于CURLOPT_TIMEOUT：The maximum number of seconds to allow cURL functions to execute.
     * @var int
     */
    protected $cfg_curlTimeout = 10;
    
    /**
     * 等同于CURLOPT_CONNECTTIMEOUT： The number of seconds to wait while trying to connect. Use 0 to wait indefinitely.
     * @var int
     */
    protected $cfg_curlConnectTimeout = 7;
    
    /**
     * 接口域名
     * @var string
     */
    protected $host = "";
    
    /**
     * 接口url
     * @var string
     */
    protected $uri = "/v2/index.php";
    
    /**
     * @var string
     */
    protected $secretId = "";
    
    /**
     * @var string
     */
    protected $secretKey = "";
    
    /**
     * 默认区域参数
     * @var string
     */
    protected $defaultRegion = "";
    
    /**
     * 请求client
     * @var string
     */
    protected $requestClient = "TDG_SRV_0.1";
    
	protected $_curlInit;
    
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
     * 设置配置
     * @param array $config
     */
    public function setConfig(array $config){
        foreach($config as $k => $v){
            if($k{0} === '_'){
                continue;
            }
            $this->{$k} = $v;
        }
    }
    
    /**
     * 获取配置
     * @param string $k
     * @return string
     */
    public function getConfig($k){
        if($k{0} === '_'){
            return null;
        }
        return isset($this->{$k}) ? $this->{$k} : null;
    }
    
    /**
     * 发送请求
     * @param string $actionName
     * @param string $param
     * @param string $requestMethod 请求方法，必须全大写
     * @param Response $response
     * @return Response $response
     */
    public function send($actionName, $param = null, $requestMethod = 'GET', Response $response = null){
        
        $requestMethod = strtoupper($requestMethod);
        
        $url = $this->protocol. '://'. $this->host. $this->uri;
        $bodyParam = $this->buildParam($actionName, $param, $requestMethod);
        if('GET' == $requestMethod){
           $url .= '?'. http_build_query($bodyParam);
           $bodyParam = array();
        }
        
        return $this->rawSend($url, $bodyParam, $requestMethod, $response);
    }
    
    /**
     * 原始发送请求
     * @param string $url 完整URL
     * @param string|array $bodyParam body请求体。$requestMethod为POST时有效
     * @param string $requestMethod 请求方法，必须全大写
     * @param Response $response
     * @return Response $response
     */
    protected function rawSend($url, $bodyParam = null, $requestMethod = 'GET', Response $response = null){
        
        if(null === $response){
            $response = new Response();
        }
        
        if(null === $this->_curlInit){
            $this->_curlInit = curl_init();
        }
        
        $curlOpt = $this->getDefaultCurlOpt();
        
        $curlOpt[CURLOPT_URL] = $url;
        
        if($requestMethod == 'POST'){
            $curlOpt[CURLOPT_POST] = true;
        }
        $curlOpt[CURLOPT_CUSTOMREQUEST] = $requestMethod;
        
        if($requestMethod == 'POST' || $requestMethod == 'PUT'){
            if(is_array($bodyParam)){
                if(!$this->rawSendCheckHasFile($bodyParam)){
                    $bodyParam = http_build_query($bodyParam);
                }else{
                    $bodyParam = $this->rawSendBuildCleanUploadBody($bodyParam);
                }
            }
        
            if($bodyParam !== null && $bodyParam !== ""){
                $curlOpt[CURLOPT_POSTFIELDS] = $bodyParam;
            }else{
                $curlOpt[CURLOPT_POSTFIELDS] = "";
            }
        }
        
        curl_setopt_array($this->_curlInit, $curlOpt);
        
        $rawResult = curl_exec($this->_curlInit);
        $curlInfo = curl_getinfo($this->_curlInit);
        
        $curl_errno = curl_errno($this->_curlInit);
        if($curl_errno){
            $response->setError("CURL_ERROR", curl_error($this->_curlInit). '[ErrCode '. $curl_errno. ']');
        }else{
            $response->create($curlInfo['http_code'], $rawResult);
        }
        
        $response->setExtractInfo($curlInfo);
        return $response;
    }
    
    public function getDefaultCurlOpt(){
        $curlOpt = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_TIMEOUT => $this->cfg_curlTimeout,
            CURLOPT_CONNECTTIMEOUT => $this->cfg_curlConnectTimeout,
            CURLOPT_USERAGENT => (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'TDG_SRV no useragent'),
        );
    
        if ($this->cfg_curlDisableSslVerify) {
            $curlOpt[CURLOPT_SSL_VERIFYPEER] = false;
            $curlOpt[CURLOPT_SSL_VERIFYHOST] = 0;
        }
    
        if(defined('CURLOPT_SAFE_UPLOAD')){
            $curlOpt[CURLOPT_SAFE_UPLOAD] = true;
        }
    
        return $curlOpt;
    }
    
    /**
     * 判断请求body体是否有文件上传指令？
     * @param unknown $params
     * @return boolean
     */
    protected function rawSendCheckHasFile($params){
         
        foreach($params as $v){
            
            //CURLFileCompat为低于php 5.5的兼容
            if($v instanceof \CURLFile || $v instanceof CURLFileCompat){
                return true;
            }
            
        }
        
        return false;
    }
    
    /**
     * 如果判断为文件上传，则对数组body进行检查和净化
     * @param array $param
     * @return array
     */
    protected function rawSendBuildCleanUploadBody($param){
        foreach($param as $k => $v){
            if($v instanceof \CURLFile || $v instanceof CURLFileCompat){
                continue;
            }
    
            //curl在CURLOPT_POSTFIELDS接收数组参数时，不支持field为数组
            if(is_array($v)){
                unset($param[$k]);
                continue;
            }
    
            if(is_string($v) && !empty($v) && $v{0} == '@'){
                unset($param[$k]);
                continue;
            }
    
        }
    
        return $param;
    }
    
    
    /**
     * 低版本PHP的curl_file_create函数兼容
     * @param string $filename
     * @param string $mimetype
     * @param string $postname
     * @return string
     */
    public function curl_file_create($filename, $mimetype = '', $postname = ''){
         
        if (!function_exists('curl_file_create')) {
            if(!class_exists('QcloudApi\Base\CURLFileCompat')){
                require_once __DIR__. '/CURLFileCompat.php';
            }
            return new CURLFileCompat($filename, $mimetype, ($postname ? $postname : basename($filename)));
        }
        
        return curl_file_create($filename, $mimetype, $postname);
    }
    
    
    /**
     * 创建url
     * @param string $actionName
     * @param string $param
     * @param string $requestMethod 请求方法，必须全大写
     * @return string
     */
    public function createUrl($actionName, $param = null, $requestMethod = 'GET'){
        if($requestMethod == 'GET'){
            return $this->protocol. '://'. $this->host. $this->uri. '?'. http_build_query($this->buildParam($actionName, $param, $requestMethod));
        }else{
            return $this->protocol. '://'. $this->host. $this->uri;
        }
    }
    
    /**
     * 创建完整参数
     * @param string $actionName
     * @param string $param
     * @param string $requestMethod 请求方法，必须全大写
     * @return string
     */
    public function buildParam($actionName, $param = null, $requestMethod = 'GET'){
        if(empty($param)){
            $param = array();
        }elseif(!is_array($param)){
            $paramTmp = array();
            parse_str($param, $paramTmp);
            $param = $paramTmp;
            unset($paramTmp);
        }
        
        $param['Action'] = ucfirst($actionName);
        if (!isset($param['Region'])){
            $param['Region'] = $this->defaultRegion;
        }
        $param['SecretId'] = $this->secretId;
        //$param['RequestClient'] = $this->requestClient;
        
        $param['Timestamp'] = time();
        $param['Nonce'] = mt_rand();
        $param['Signature'] = $this->buildSignature($param, $requestMethod);
        return $param;
    }
    
    /**
     * 对参数进行签名
     * @param array $param
     * @param string $requestMethod 请求方法，必须全大写
     */
    public function buildSignature(array $param, $requestMethod = 'GET'){
        $plainText = $requestMethod. $this->host. $this->uri;
        
        ksort($param);
        
        $isSegQue = true;
        foreach($param as $k => $v){
            //文件上传
            if ($requestMethod == 'POST' && $this->isUploadAtomCmd($v)) {
                continue;
            }
            
            if($isSegQue){
                $plainText .= '?';
                $isSegQue = false;
            }else{
                $plainText .= '&';
            }
            
            $plainText .= $k . '=' . $v;
            
        }
        
        return base64_encode(hash_hmac('sha1', $plainText, $this->secretKey, true));
        
    }
    
    /**
     * 是否是一个文件上传的原子指令？
     * @param mixed $v
     * @param bool
     */
    protected function isUploadAtomCmd($v){
    
        //CURLFileCompat为低于php 5.5的兼容
        if($v instanceof \CURLFile || $v instanceof CURLFileCompat){
            return true;
        }
    
        return false;
    }
    
    public function __destruct(){
        if ($this->_curlInit) {
		    curl_close($this->_curlInit);
        }
    }
    
}