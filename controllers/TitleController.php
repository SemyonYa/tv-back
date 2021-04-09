<?php

namespace app\controllers;

use app\models\domain\Role;
use Yii;
use yii\rest\Controller;
use yii\web\Response;

class TitleController extends Controller
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
}
