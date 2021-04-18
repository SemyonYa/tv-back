<?php

namespace app\models\domain;

use app\models\helper\UserStatus;
use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $login
 * @property string $password_hash
 * @property int $role_id
 * @property string $last_name
 * @property string $first_name
 * @property string|null $middle_name
 * @property string|null $auth_token
 * @property int $status
 * 
 * @property Role $role
 */
class User extends ActiveRecord implements IdentityInterface
{

    public static function fromParams(
        $login,
        $password,
        $role_id,
        $last_name,
        $first_name,
        $middle_name
    ) {
        $new = new User();
        $new->login = $login;
        $new->password_hash = Yii::$app->security->generatePasswordHash($password);
        $new->role_id = $role_id;
        $new->last_name = $last_name;
        $new->first_name = $first_name;
        $new->middle_name = $middle_name;
        $new->status = UserStatus::ACTIVE;

        return $new;
    }


    public static function tableName()
    {
        return 'user';
    }

    public function fields()
    {
        $fields = parent::fields();
        unset($fields['auth_token'], $fields['password_hash']);
        return $fields;
    }
    public function rules()
    {
        return [
            [['login', 'password_hash', 'role_id', 'last_name', 'first_name'], 'required'],
            [['password_hash'], 'string'],
            [['role_id', 'status'], 'integer'],
            [['status'], 'default', 'value' => 1],
            [['login'], 'string', 'max' => 20],
            [['last_name'], 'string', 'max' => 100],
            [['first_name', 'middle_name'], 'string', 'max' => 50],
            [['auth_token'], 'string', 'max' => 300],
            [['login'], 'unique'],
            [['role_id'], 'exist', 'skipOnError' => true, 'targetClass' => Role::class, 'targetAttribute' => ['role_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'login' => 'Логин',
            'password_hash' => 'Пароль',
            'role_id' => 'Роль',
            'last_name' => 'Фамилия',
            'first_name' => 'Имя',
            'middle_name' => 'Отчество',
            'auth_token' => 'Auth Token',
            'status' => 'Статус'
        ];
    }

    public function getRole()
    {
        return $this->hasOne(Role::class, ['id' => 'role_id']);
    }


    public function updateData($role_id, $last_name, $first_name, $middle_name)
    {
        $this->role_id = $role_id;
        $this->last_name = $last_name;
        $this->first_name = $first_name;
        $this->middle_name = $middle_name;
        // $this->auth_token = Yii::$app->security->generateRandomString(128);
        $this->save();
        return $this->auth_token;
    }

    public function updateToken()
    {
        $this->auth_token = Yii::$app->security->generateRandomString(128);
        $this->save();
        return $this->auth_token;
    }

    public function updateStatus(int $status)
    {
        $this->status = $status;
        if ($status === UserStatus::DELETED) {
            $this->login = '__deleted__' . $this->login;
        }
        return $this->save();
    }

    public function resetPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
        return $this->save();
    }

    ///
    /// Authentication
    /// OVERRIDES IdentityInterface
    ///

    public static function findIdentity($id)
    {
        // only for SESSIONS
        // return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::find()->where(['auth_token' => $token, 'status' => 1])->one();
    }

    function getAuthKey()
    {
        // ONLY with COOKIE
        // return $this->auth_token;
    }

    function getId()
    {
        return $this->id;
    }

    public function validateAuthKey($auth_token)
    {
        // ONLY with COOKIE
        // return $this->auth_token == $auth_token;
    }

    //     ///
    //     /// Authorization
    //     ///

    //     public function can($user_id)
    //     {
    //         return $this->id === $user_id;
    //     }
}
