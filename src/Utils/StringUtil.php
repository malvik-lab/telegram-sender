<?php

namespace MalvikLab\TelegramSender\Utils;

use MalvikLab\TelegramSender\TelegramSender;

class StringUtil
{
    /**
     * @param string $string
     * @return string
     */
    public static function camelCaseToSnakeCase(string $string): string
    {
        return strtoupper(preg_replace('/(?<!^)[A-Z]/', '_$0', $string));
    }

    /**
     * @param string $message
     * @return string
     */
    public static function exception(string $message): string
    {
        return sprintf('[ %s ] %s',
            TelegramSender::NAME,
            $message
        );
    }
}