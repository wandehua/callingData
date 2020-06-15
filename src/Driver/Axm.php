<?php
/**
 * AX+模式
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


class Axm implements IData {

    private static $instance;
    private static $reqHandler = null;
    private static $pay = null;
    private static $cfg = null;

    private static $error= [
        'status'=>'',
        'message'=>'',
        'data'=>[],
    ];

    private static $success = [];

    private function __construct() {
        self::$reqHandler = RequestHandler::make();
        self::$pay = Http::make();
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

    }

    public static function submitData(){

    }

    /**
     * 后台异步通知处理
     */
    public static function callback(){


    }
}