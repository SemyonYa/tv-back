<?php

namespace app\models\view;

use yii\base\Model;

class LoginView extends Model
{
    public $login;
    public $password;
    public $role_id;

    public function __construct()
    {
    }
}
