<?php

namespace app\components;

class TvAccessRule extends \yii\filters\AccessRule
{
    protected function matchRole($user)
    {
        if (empty($this->roles)) return true;

        // if ADMIN can every action 
        // if ($user->identity->role_id === UserRole::ADMIN) return true;

        foreach ($this->roles as $role) {
            if ($role === $user->identity->role_id) {
                return true;
            }
        }
        return false;
    }
}
