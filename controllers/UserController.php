<?php

namespace app\controllers;

use app\models\domain\Role;
use app\models\domain\User;
use app\models\helper\Helper;
use app\models\view\ErrorResponse;
use app\models\view\RegisterView as RegisterView;
use Exception;
use Yii;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

class UserController extends CommonController
{
    protected function verbs()
    {
        return [
            'index' => ['GET', 'HEAD'],
            'one' => ['GET', 'HEAD'],
            'roles' => ['GET', 'HEAD'],
            'create' => ['POST'],
            'update' => ['POST'],
            'reset-password' => ['POST'],
        ];
    }

    public function actionIndex()
    {
        return User::find()->all();
    }

    public function actionOne($id)
    {
        return User::findOne($id);
    }

    public function actionCreate()
    {
        $request = Yii::$app->request;

        $login = $request->post('login');
        $password = $request->post('password');
        $role_id = $request->post('roleId');
        $last_name = $request->post('last_name');
        $first_name = $request->post('first_name');
        $middle_name = $request->post('middle_name');

        $register_view = new RegisterView(
            $login,
            $password,
            $role_id,
            $last_name,
            $first_name,
            $middle_name
        );


        if ($register_view->validate()) {
            $user = $register_view->toUser();
            if ($user->validate()) {
                if ($user->save()) {
                    return $user->auth_token;
                } else {
                    throw new HttpException('DB exception');
                }
            } else {
                throw new BadRequestHttpException(Helper::convertHttpErrorToString($user->errors));
            }
        } else {
            throw new BadRequestHttpException(Helper::convertHttpErrorToString($register_view->errors));
        }
    }

    public function actionUpdate()
    {
        $request = Yii::$app->request;

        $id = $request->post('id');
        $role_id = $request->post('roleId');
        $last_name = $request->post('last_name');
        $first_name = $request->post('first_name');
        $middle_name = $request->post('middle_name');

        $user = User::findOne($id);
        if ($user) {
            $user->role_id = $role_id;
            $user->last_name = $last_name;
            $user->first_name = $first_name;
            $user->middle_name = $middle_name;
            if ($user->validate()) {
                if ($user->save()) {
                    return;
                } else {
                    throw new HttpException(500, 'Что-то пошло не так');
                }
            } else {
                throw new BadRequestHttpException(Helper::convertHttpErrorToString($user->errors));
            }
        } else {
            throw new NotFoundHttpException('Пользователя с ID ' . $id . ' не существует');
        }
    }

    public function actionResetPassword()
    {
        $request = Yii::$app->request;

        $id = $request->post('id');
        $password = $request->post('password');
        $password_confirm = $request->post('password_confirm');

        $user = User::findOne($id);
        if ($user) {
            if ($password === $password_confirm) {
                $user->password_hash = Yii::$app->security->generatePasswordHash($password);
                if ($user->validate()) {
                    if ($user->save()) {
                        return;
                    } else {
                        throw new HttpException(500, 'Что-то пошло не так');
                    }
                } else {
                    throw new BadRequestHttpException(Helper::convertHttpErrorToString($user->errors));
                }
            } else {
                throw new BadRequestHttpException('Введенные пароли не совпадают');
            }
        } else {
            throw new NotFoundHttpException('Пользователя с ID ' . $id . ' не существует');
        }
    }

    public function actionBlock($id)
    {
        // TODO: unlock
        // $block = Yii::$app->request->post('block');
        $user = User::findOne($id);
        if ($user) {
            $user->blocked = 1;
            if ($user->validate()) {
                if ($user->save()) {
                    return;
                } else {
                    throw new HttpException(500, 'Что-то пошло не так');
                }
            } else {
                throw new BadRequestHttpException(Helper::convertHttpErrorToString($user->errors));
            }
        } else {
            throw new NotFoundHttpException('Пользователя с ID ' . $id . ' не существует');
        }
    }

    public function actionDelete()
    {
        // TODO: 
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
