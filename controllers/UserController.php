<?php

namespace app\controllers;

use app\models\domain\Role;
use Yii;
use yii\rest\Controller;
use yii\web\Response;

class UserController extends Controller
{
    protected function verbs()
    {
        return [
            'index' => ['GET', 'HEAD'],
            'roles' => ['GET', 'HEAD'],
        ];
    }

    public function actionIndex()
    {
        // return User
    }

    public function actionRoles()
    {
        return Role::find()->all();
    }

    public function actionRole($id)
    {
        return Role::findOne($id);
    }
}
