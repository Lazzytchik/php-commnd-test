<?php

namespace Arse\PhpCommandTest;

class Console
{
    private static array $separators = [
        'comma' => ',',
        'semicolon' => ';'
    ];

    public static function execute($args){
        $function = $args[2];

        if (isset(self::$separators[$args[1]])){
            TextHandler::$separator = self::$separators[$args[1]];
        }
        else{
            return "Wrong separator";
        }

        if (method_exists(TextHandler::class, $function)){
            return TextHandler::$function();
        }

        return "Function does not exists";
    }
}