<?php
namespace CallingData\Config;

class Config {

    private static $instance;

    private static $config=[
        'url'=>'http://sandbox.teleii.com',
        'spId'=>'',//商户号
        'spKey'=>'',//密钥
        'notify_url'=>''//异步回调通知地址
    ];

    private function __construct()
    {
    }

    public static function make(){
        if(!self::$instance){
            self::$instance = new static();
        }
        return self::$instance;
    }

    public static function setAttr($name,$value){
        isset(self::$config[$name]) && self::$config[$name] = $value;
    }

    public static function getAttr($name=''){
        return empty($name) ? self::$config : (isset(self::$config[$name])?self::$config[$name]:'');
    }


}