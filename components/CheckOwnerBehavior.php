<?php

namespace app\components;

use Yii;
use yii\base\Behavior;
use yii\rest\Controller;
use yii\web\ForbiddenHttpException;

class CheckOwnerBehavior extends Behavior {
    public $user_id;  

    public function events() {
        return [
            Controller::EVENT_BEFORE_ACTION => 'check'
        ];
    }

    public function check() {
        if ($this->user_id != Yii::$app->user->identity->id) {
            throw new ForbiddenHttpException();
        }
    }
}