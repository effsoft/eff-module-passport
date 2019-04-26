<?php
namespace effsoft\eff\module\passport\models;

use effsoft\eff\EffActiveRecord;
use effsoft\eff\EffModel;

class SocialAuthModel extends EffActiveRecord {

    public static function collectionName()
    {
        return 'SocialAuth';
    }

    public function attributes()
    {
        return ['_id', 'uid', 'client', 'social_uid', 'profile',
             'token', 'date_created', 'date_updated',
        ];
    }

    public function beforeSave($insert)
    {
        if(parent::beforeSave($insert)){
            if($this->isNewRecord){
                $this->date_created = $this->date_updated = time();
            }
            return true;
        }
        return false;
    }
}