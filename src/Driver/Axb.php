<?php
/**
 * AXB模式
 * Created by WanDeHua.
 * User: WanDeHua 
 * Email:271920545@qq.com
 * Date: 2017/12/8
 * Time: 9:45
 */

namespace CallingData\Driver;

use CallingData\Lib\RequestHandler;
use CallingData\Lib\Http;
use CallingData\Config\Config;


class Axb implements IData {

    private static $instance;
    private static $reqHandler = null;
    private static $http = null;
    private static $cfg = null;

    private static $error= [
        'status'=>'',
        'message'=>'',
        'data'=>[],
    ];

    private static $success = [];

    private function __construct() {
        self::$reqHandler = RequestHandler::make();
        self::$http = Http::make();
        self::$cfg = Config::make();
    }

    public static function make(){

        if(!self::$instance){
            self::$instance = new static();
        }
        return self::$instance;
    }

    private static function setError($status,$message){
        self::$error['status'] = $status;
        self::$error['message'] = $message;
    }

    public static function getError(){
        return self::$error;
    }

    private static function setSuccess($value=[]){
        self::$success = $value;
    }

    public static function getSuccess(){
        return self::$success;
    }


    /**
     * 提交订单信息
     */
    public static function submit($post){

        $time = time().'000';
        self::$cfg->setAttr('spId',$post['spId']);
        self::$cfg->setAttr('spKey',$post['spKey']);

        self::$reqHandler->setParameter('spKey',self::$cfg->getAttr('spKey'));
        self::$reqHandler->setParameter('spId',self::$cfg->getAttr('spId'));
        self::$reqHandler->setParameter('timestamp',$time);
        self::$reqHandler->setParameter('seqId',$time);
        self::$reqHandler->setParameter('fm',$post['fm']);
        self::$reqHandler->setParameter('tm',$post['tm']);
        self::$reqHandler->setParameter('notifyUrl',self::$cfg->getAttr('notify_url'));
        self::$reqHandler->setParameter('gateUrl',$post['gateUrl']);
        self::$reqHandler->createSign();//创建签名
        self::$http->setReqContent(self::$reqHandler->getParameter('gateUrl'),self::submitData());
        if(self::$http->call()){
            self::setSuccess(json_decode(self::$http->getResContent(),true));
            return true;
        }else{
            self::setError(-1,self::$http->getErrInfo());
            return false;
        }

    }

    public static function submitData(){
        return "id=".self::$reqHandler->getParameter('spId')."&timestamp=".self::$reqHandler->getParameter('timestamp').
            "&seqId=".self::$reqHandler->getParameter('seqId')."&fm=".self::$reqHandler->getParameter('fm').
            "&tm=".self::$reqHandler->getParameter('tm')."&bindTime=".self::$reqHandler->getParameter('bindTime').
            "&virtualMobile=&sign=".self::$reqHandler->getParameter('sign');
    }


    /**
     * 后台异步通知处理
     */
    public static function callback(){


    }
}