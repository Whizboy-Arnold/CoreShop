<?php

namespace CoreShop;

use CoreShop\Base;

class User extends Base
{
    public static function create()
    {
        $class = self::getUserClass();
        
        return new $class();
    }
    
    public static function getUserClass()
    {
        return \CoreShop\Tool::getModelClassMapping("CoreShop_User", "CoreShop\Plugin\User");
    }

    public static function __callStatic($name, $arguments)
    {
        $class = self::getUserClass();

        return call_user_func_array(array($class, $name), $arguments);
    }
}