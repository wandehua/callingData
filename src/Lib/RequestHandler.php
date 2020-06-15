<?php
namespace CallingData\lib;
/**
 * 请求类
 * ============================================================================
 * api说明：
 * setAttr()/getAttr(),获取/设置配置项
 * getParameter()/setParameter(),获取/设置参数值
 * getAllParameters(),获取所有参数
 * getRequestURL(),获取带参数的请求URL
 * getDebugInfo(),获取debug信息
 * 
 * ============================================================================
 *
 */
class RequestHandler {

    private static $instance;
    /** 请求的参数 */
    private static $parameters=[
        'spKey'=>'',
        'spId'=>'',
        'seqId'=>'',
        'timestamp'=>'',
        'fm'=>'',
        'tm'=>'',
        'virtualMobile'=>'',
        'bindTime'=>'5', //5分钟
        'sign'=>'',
        'notifyUrl'=>'',
        'gateUrl'=>''
    ];

    /** debug信息 */
    private static $debugInfo='';


    private function __construct() {
	}

    static function make(){
        if(!self::$instance){
            self::$instance = new static();
        }
        return self::$instance;
    }
    /**
     *获取参数值
     */
    public static function getParameter($name='') {
        return empty($name) ? self::$parameters : (isset(self::$parameters[$name])?self::$parameters[$name]:'');
    }

    /**
     *设置参数值
     */
    public static function setParameter($name,$value){
        self::$parameters[$name] = $value;
    }
	
	/**
	*获取带参数的请求URL
	*/
	public static function getRequestURL() {
	
		self::createSign();
		
		$reqPar = "";
		ksort(self::$parameters);
		foreach(self::$parameters as $k => $v) {
			$reqPar .= $k . "=" . urlencode($v) . "&";
		}
		
		//去掉最后一个&
		$reqPar = substr($reqPar, 0, strlen($reqPar)-1);
		
		$requestURL = self::getAttr('gateUrl') . "?" . $reqPar;
		
		return $requestURL;
		
	}
	
	/**
	*创建md5摘要
	*/
	public static function createSign() {
		self::setParameter('sign',md5(self::$parameters['spKey'].self::$parameters['spId'].self::$parameters['seqId'].self::$parameters['timestamp'].self::$parameters['fm'].self::$parameters['tm']));
	}

}

?>