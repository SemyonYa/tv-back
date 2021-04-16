<?php

namespace app\models\view;

use yii\base\Model;

class ErrorResponse extends Model
{
    public $code;
    public $errors;

    public function __construct($code, $errors)
    {
        $this->code = $code;
        $this->errors = $errors;
    }
}
