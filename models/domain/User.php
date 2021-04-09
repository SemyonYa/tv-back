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
 * @property string $fio
 *
 * @property Role $role
 */
class User extends \yii\db\ActiveRecord
{
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
            [['id', 'login', 'password_hash', 'role_id', 'fio'], 'required'],
            [['id', 'role_id'], 'integer'],
            [['password_hash'], 'string'],
            [['login'], 'string', 'max' => 20],
            [['fio'], 'string', 'max' => 100],
            [['id'], 'unique'],
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
            'login' => 'Login',
            'password_hash' => 'Password Hash',
            'role_id' => 'Role ID',
            'fio' => 'Fio',
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
