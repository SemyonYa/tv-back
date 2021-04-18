<?php

namespace app\controllers;

use app\components\TvAccessRule;
use app\models\helper\UserRole;
use Yii;

class TitleController extends CommonController
{
    protected function verbs()
    {
        return [
            'index' => ['GET', 'HEAD'],
        ];
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['access'] = [
            'class' => AccessControl::class,
            'ruleConfig' => [
                'class' => TvAccessRule::class,
            ],
            'rules' => [
                [
                    'allow' => true,
                    'roles' => [
                        UserRole::INNER_MANAGER,
                        UserRole::OUTER_MANAGER,
                        UserRole::USER,
                    ],
                ],
            ],
        ];

        // TODO: 
        // Title->author_id
        // $behaviors['checkOwner'] = [
        //     'class' => CheckOwnerBehavior::class,
        //     'user_id' => Yii::$app->user->identity->role_id == UserRole::INNER_MANAGER ? Yii::$app->user->identity->id : Title::findOne(Yii::$app->request->get('id'))->author_id
        // ];

        return $behaviors;
    }


    // public function actionIndex()
    // {
    //     return Title::find()->all();
    // }

    // public function actionOne(int $id)
    // {
    //     return $this->findOne($id);
    // }

    public function actionCreate()
    {
        return null;
    }

    public function actionUpdate(int $id)
    {
        return null;
    }

    // private function findOne($id)
    // {
    //     $title = Title::findOne($id);
    //     if ($title) {
    //         return $title;
    //     } else {
    //         throw new NotFoundHttpException('Тайтл с ID ' . $id . ' не существует');
    //     }
    // }
}
