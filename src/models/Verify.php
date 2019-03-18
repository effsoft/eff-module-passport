<?php
namespace effsoft\eff\module\passport\models;

use yii\mongodb\ActiveRecord;

class Verify extends ActiveRecord {

    public static function collectionName()
    {
        return 'Verify';
    }

    public function attributes()
    {
        return ['_id','uid', 'token', 'code', 'data', 'date_created'];
    }

    public function beforeSave($insert)
    {
        if(parent::beforeSave($insert)){
            $now = time();
            Verify::deleteAll(['<', 'date_created' , (time() - 30 * 60)]);
            $this->date_created = $now;
            return true;
        }
        return false;
    }

    public function isExpired(){
        return $this->date_created < (time() - 30 * 60);
    }

}