<?php

namespace app\controllers;

use yii\filters\auth\HttpBearerAuth;
use yii\rest\Controller;

class CommonController extends Controller
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
        ];
        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::class,
            'cors' => [
                'Origin' => [
                    'http://localhost:4200',
                    'http://localhost:8100',
                    'http://tv.injini.ru',
                ],
                'Access-Control-Allow-Origin' => true,
                'Access-Control-Allow-Credentials' => true,
                'Access-Control-Request-Method' => ['POST'],
                'Access-Control-Allow-Headers' => ['Origin', 'Content-Type', 'X-Auth-Token', 'Authorization', 'x-compress']
            ],

        ];
        return $behaviors;
    }
}
