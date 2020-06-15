<?php
/**
 * Created by WanDeHua.
 * User: WanDeHua 
 * Email:271920545@qq.com
 * Date: 2017/12/8
 * Time: 18:32
 */

namespace CallingData\Driver;

Interface IData {
    public static function submit($post);
    public static function callback();
}