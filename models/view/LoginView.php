<?php

namespace app\models\view;

use app\models\domain\User;
use app\models\helper\UserStatus;
use Yii;
use yii\base\Model;

class LoginView extends Model
{
    public $login;
    public $password;

    public function __construct($login, $password)
    {
        $this->login = $login;
        $this->password = $password;
    }

    public function rules()
    {
        return [
            [['login', 'password'], 'required'],
            [['login', 'password'], 'trim'],
            [['login'], 'string', 'max' => 20],
            [['password'], 'string', 'max' => 30],
        ];
    }

    public function login()
    {
        $user = User::findOne(['login' => $this->login, 'status' => UserStatus::$active]);
        if ($user) {
            if (Yii::$app->security->validatePassword($this->password, $user->password_hash)) {
                return $user->updateToken();
            }
        }
        return null;
    }
}
