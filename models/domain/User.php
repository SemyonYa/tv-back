<?php

namespace app\models\domain;

use Yii;

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
 * @property int $blocked
 * 
 * @property Role $role
 */
class User extends \yii\db\ActiveRecord
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
        $new->auth_token = Yii::$app->security->generateRandomString(128);

        return $new;
    }
    /**
     * {@inheritdoc}
     */
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
            [['role_id', 'blocked'], 'integer'],
            [['blocked'], 'default', 'value' => 0],
            [['login'], 'string', 'max' => 20],
            [['last_name'], 'string', 'max' => 100],
            [['first_name', 'middle_name'], 'string', 'max' => 50],
            [['auth_token'], 'string', 'max' => 300],
            [['login'], 'unique'],
            [['role_id'], 'exist', 'skipOnError' => true, 'targetClass' => Role::className(), 'targetAttribute' => ['role_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
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
            'blocked' => 'Заблокировать'
        ];
    }

    /**
     * Gets query for [[Role]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRole()
    {
        return $this->hasOne(Role::className(), ['id' => 'role_id']);
    }
}
