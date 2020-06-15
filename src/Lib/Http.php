<?php
namespace CallingData\Lib;
/**
 * http、https通信类
 * ============================================================================
 * api说明：
 * setReqContent($reqContent),设置请求内容，无论post和get，都用get方式提供
 * getResContent(), 获取应答内容
 * setMethod($method),设置请求方法,post或者get
 * getErrInfo(),获取错误信息
 * setCertInfo($certFile, $certPasswd, $certType="PEM"),设置证书，双向https时需要使用
 * setCaInfo($caFile), 设置CA，格式未pem，不设置则不检查
 * setTimeOut($timeOut)， 设置超时时间，单位秒
 * getResponseCode(), 取返回的http状态码
 * call(),真正调用接口
 * 
 * ============================================================================
 *
 */

class Http {

    private static $instance;

	//请求内容，无论post和get，都用get方式提供
    private static $reqContent = [
	    'url'=>'',
	    'data'=>'',
    ];
	//应答内容
    private static $resContent;
	
	//错误信息
    private static $errInfo = '';
	
	//超时时间
    private static $timeOut = 120;
	
	//http状态码
    private static $responseCode = 0;

    private function __construct() {
    }

    public static function make(){
        if(!self::$instance){
            self::$instance = new static();
        }
        return self::$instance;
    }

	
	//设置请求内容
    public static function setReqContent($url,$data) {
		self::$reqContent['url']=$url;
		self::$reqContent['data']=$data;
	}
	
	//获取结果内容
    public static function getResContent() {
		return self::$resContent;
	}
	
	//获取错误信息
    public static function setErrInfo($value) {
		self::$errInfo = $value;
	}

    //获取错误信息
    public static function getErrInfo() {
        return self::$errInfo;
    }
	//设置超时时间,单位秒
    public static function setTimeOut($timeOut) {
        self::$timeOut = $timeOut;
	}

    public static function getResponseCode() {
        return self::$responseCode;
    }

	//执行http调用
    public static function call() {
		//启动一个CURL会话
		$ch = curl_init();

		// 设置curl允许执行的最长秒数
		curl_setopt($ch, CURLOPT_TIMEOUT, self::$timeOut);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
		// 获取的信息以文件流的形式返回，而不是直接输出。
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		
        //发送一个常规的POST请求。
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, self::$reqContent['url']);
        //要传送的所有数据
        curl_setopt($ch, CURLOPT_POSTFIELDS, self::$reqContent['data']);
		
		// 执行操作
		$res = curl_exec($ch);
        self::$responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (!self::checkSuccess($res)) {
            echo self::checkError($res);exit;
            self::setErrInfo("请求地址错误 :" . self::checkErrorNo($res) . " - " . self::checkError($res) ."，请联系管理员解决");
            curl_close($ch);
            return false;

        }

		curl_close($ch);
        self::$resContent = $res;
		return true;
	}
    public static function checkSuccess($res){
        $error = json_decode($res,true);
        if(isset($error['result']) && $error['result']==0){
             return true;
        }else{
            return false;
        }
    }
	public static function checkError($res){
        $error = json_decode($res,true);
        $mes = '';

        if(isset($error['result'])){
            switch ($error['result']){
                case '-1':
                    $mes = '未知错误';
                    break;
                case '-2':
                    $mes = '服务暂停';
                    break;
                case '-3':
                    $mes = '无效参数';
                    break;
                case '-4':
                    $mes = '缺失参数';
                    break;
                case '-5':
                    $mes = '未授权的IP地址';
                    break;
                case '-6':
                    $mes = '未授权的Id帐号';
                    break;
                case '-7':
                    $mes = 'Id无效';
                    break;
                case '-8':
                    $mes = 'Id帐号状态异常';
                    break;
                case '-9':
                    $mes = '签名错误';
                    break;
                case '-10':
                    $mes = '并发请求超过限制的范围';
                    break;
                case '-11':
                    $mes = '未授权的商务号';
                    break;
                case '-12':
                    $mes = '时间戳格式错误';
                    break;
                case '-13':
                    $mes = '时间戳过期错误（冗余的时间范围前后12小时）';
                    break;
                case '-14':
                    $mes = 'bindMobile格式错误，非正确的号码';
                    break;
                case '-15':
                    $mes = 'bindMobile不能为商务号';
                    break;
                case '-16':
                    $mes = 'bindTime格式错误';
                    break;
                case '-17':
                    $mes = 'fm不能为商务号';
                    break;
                case '-18':
                    $mes = 'tm不能为商务号';
                    break;
                case '-19':
                    $mes = '短信余额不足';
                    break;
                case '-20':
                    $mes = '语音余额不足';
                    break;
                case '-21':
                    $mes = '未知错误';
                    break;
                case '-22':
                    $mes = '无法获取商务号';
                    break;
                case '-23':
                    $mes = 'fixTime不能大于bindTime';
                    break;
                case '-24':
                    $mes = '不存在的绑定关系，或关系已经解除';
                    break;
                case '-25':
                    $mes = '未授权的操作';
                    break;
                case '-26':
                    $mes = '存在绑定关系，无法重复绑定，解绑后可操作';
                    break;
                case '-27':
                    $mes = '黑名单号码，禁止操作';
                    break;
                case '-40':
                    $mes = 'tm不在白号码名单中';
                    break;
                case '-99':
                    $mes = '服务异常';
                    break;
                default:
                    $mes = '未知错误2';
                    break;
            }
        }else{
            $mes = '未知错误3';
        }
        return $mes;
    }
    public static function checkErrorNo($res){
        $error = json_decode($res,true);
        $no = '';
        if(isset($error['result'])){
            $no = $error['result'];
        }else{
            $no = '-999';
        }
        return $no;
    }
	
}
?>