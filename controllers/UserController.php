<?php

namespace app\controllers;

use app\components\CheckOwnerBehavior;
use app\components\TvAccessRule;
use app\models\domain\Role;
use app\models\domain\User;
use app\models\helper\Helper;
use app\models\helper\UserRole;
use app\models\helper\UserStatus;
use app\models\view\RegisterView as RegisterView;
use Yii;
use yii\filters\AccessControl;
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
            'create' => ['POST'],
            'update' => ['POST'],
            'reset-password' => ['POST'],
            'set-status' => ['POST'],
            'roles' => ['GET', 'HEAD'],
            'role' => ['GET', 'HEAD'],
            'delete' => ['POST'],
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
                    'roles' => [UserRole::ADMIN],
                ],
                [
                    'allow' => true,
                    'actions' => ['update'],
                    // 'roles' => [UserRole::INNER_MANAGER, ],
                ],
            ],
        ];

        $behaviors['checkOwner'] = [
            'class' => CheckOwnerBehavior::class,
            'user_id' => Yii::$app->request->get('id')
        ];

        return $behaviors;
    }

    public function actionIndex()
    {
        return User::find()->all();
    }

    public function actionOne(int $id)
    {
        return $this->findOne($id);
    }

    public function actionCreate()
    {
        $request = Yii::$app->request;

        $login = $request->post('login');
        $password = $request->post('password');
        $role_id = $request->post('roleId');
        $last_name = $request->post('lastName');
        $first_name = $request->post('firstName');
        $middle_name = $request->post('middleName');

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
                    return;
                } else {
                    throw new HttpException(500, 'DB exception');
                }
            } else {
                throw new BadRequestHttpException(Helper::convertHttpErrorToString($user->errors));
            }
        } else {
            throw new BadRequestHttpException(Helper::convertHttpErrorToString($register_view->errors));
        }
    }

    public function actionUpdate(int $id)
    {
        // return [
        //     Yii::$app->request->get('id') == Yii::$app->user->identity->id
        // ];

        $request = Yii::$app->request;

        $role_id = $request->post('roleId');
        $last_name = $request->post('lastName');
        $first_name = $request->post('firstName');
        $middle_name = $request->post('middleName');

        $user = $this->findOne($id);
        $user->updateData($role_id, $last_name, $first_name, $middle_name);
        if ($user->validate()) {
            if ($user->save()) {
                return;
            } else {
                throw new HttpException(500, 'Что-то пошло не так');
            }
        } else {
            throw new BadRequestHttpException(Helper::convertHttpErrorToString($user->errors));
        }
    }

    public function actionResetPassword(int $id)
    {
        $request = Yii::$app->request;

        $password = $request->post('password');
        $password_confirm = $request->post('passwordConfirm');

        $user = $this->findOne($id);
        // if ($user) {
        if ($password === $password_confirm) {
            if ($user->resetPassword($password)) {
                if ($user->validate()) {
                    if ($user->save()) {
                        return;
                    }
                } else {
                    throw new BadRequestHttpException(Helper::convertHttpErrorToString($user->errors));
                }
            }
            throw new HttpException(500, 'Что-то пошло не так');
        } else {
            throw new BadRequestHttpException('Введенные пароли не совпадают');
        }
        // } else {
        //     throw new NotFoundHttpException('Пользователя с ID ' . $id . ' не существует');
        // }
    }

    public function actionSetStatus(int $id)
    {
        $status = Yii::$app->request->post('status');

        $user = $this->findOne($id);
        // if ($user) {
        if ($user->status === UserStatus::DELETED) {
            throw new NotFoundHttpException('Пользователя с ID ' . $id . ' удален. Изменение статуса невозможно.');
        } else {
            if ($user->updateStatus($status)) {
                if ($user->validate()) {
                    if ($user->save()) {
                        return;
                    }
                } else {
                    throw new BadRequestHttpException(Helper::convertHttpErrorToString($user->errors));
                }
            }
            throw new HttpException(500, 'Что-то пошло не так');
        }
        // } else {
        //     throw new NotFoundHttpException('Пользователя с ID ' . $id . ' не существует');
        // }
    }

    public function actionRoles()
    {
        return Role::find()->all();
    }

    public function actionRole($id)
    {
        return Role::findOne($id);
    }

    private function findOne($id)
    {
        $user = User::findOne($id);
        if ($user) {
            return $user;
        } else {
            throw new NotFoundHttpException('Пользователя с ID ' . $id . ' не существует');
        }
    }
}
