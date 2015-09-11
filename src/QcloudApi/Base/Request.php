<?php

namespace QcloudApi\Base;

use CURLFile;

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
    protected $curl_disable_ssl_verify = true;
    
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
        
        if(null === $response){
            $response = new Response();
        }
        
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
     * @param string $actionName
     * @param string $param
     * @param string $requestMethod 请求方法，必须全大写
     * @param Response $response
     * @return Response $response
     */
    protected function rawSend($url, $bodyParam, $requestMethod, Response $response){
        if(null === $this->_curlInit){
            $this->_curlInit = curl_init();
        }
        
        $curlOpt = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_USERAGENT => (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'TDG_SRV no useragent'),
            CURLOPT_URL => $url,
        );
        
        if ($this->protocol == 'https' && $this->curl_disable_ssl_verify) {
            $curlOpt[CURLOPT_SSL_VERIFYPEER] = false;
            $curlOpt[CURLOPT_SSL_VERIFYHOST] = 0;
        }
        
        if($requestMethod == 'POST'){
            $curlOpt[CURLOPT_POST] = 1;
            $bodyParam = is_array($bodyParam) ? http_build_query($bodyParam) : $bodyParam;
            curl_setopt($ch, CURLOPT_POSTFIELDS, $bodyParam);
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
            if ($requestMethod == 'POST' && ($v instanceof CURLFile || '@' == $v{0} )) {
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
    
    public function __destruct(){
        if ($this->_curlInit) {
		    curl_close($this->_curlInit);
        }
    }
    
}