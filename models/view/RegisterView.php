<?php

namespace app\models\view;

use app\models\domain\User;
use yii\base\Model;

class RegisterView extends Model
{
    public $login;
    public $password;
    public $role_id;
    public $last_name;
    public $first_name;
    public $middle_name;

    public function __construct(
        $login,
        $password,
        $role_id,
        $last_name,
        $first_name,
        $middle_name
    ) {
        $this->login = $login;
        $this->password = $password;
        $this->role_id = $role_id;
        $this->last_name = $last_name;
        $this->first_name = $first_name;
        $this->middle_name = $middle_name;
    }

    public function rules()
    {
        return [
            [['login', 'password', 'role_id', 'last_name', 'first_name'], 'required'],
            [['password'], 'string'],
            [['role_id'], 'integer'],
            [['login'], 'string', 'max' => 20],
            [['last_name'], 'string', 'max' => 100],
            [['first_name', 'middle_name'], 'string', 'max' => 50],
        ];
    }

    public function attributeLabels()
    {
        return [
            'login' => 'Логин',
            'password' => 'Пароль',
            'role_id' => 'Роль',
            'last_name' => 'Фамилия',
            'first_name' => 'Имя',
            'middle_name' => 'Отчество',
        ];
    }

    public function toUser()
    {
        return User::fromParams(
            $this->login,
            $this->password,
            $this->role_id,
            $this->last_name,
            $this->first_name,
            $this->middle_name
        );
    }
}
