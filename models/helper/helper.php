<?php

namespace app\models\helper;

class Helper
{
    public static function convertHttpErrorToString(array $errors)
    {
        $msg = '';
        foreach ($errors as $id => $err) {
            $msg .= $id . ': ' . implode(', ', $err) . PHP_EOL;
        }
        return $msg;
    }
}
