<?php

namespace effsoft\eff\module\passport\widgets;

use effsoft\eff\EffWidget;
use effsoft\eff\module\passport\models\SocialAuthModel;
use effsoft\eff\module\passport\models\UserModel;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\Regex;
use yii\data\Pagination;

class SocialsWidget extends EffWidget {

    public $uid = false;

    public function run(){

        if (!empty($uid)){
            $query = SocialAuthModel::find()
                ->where(['uid' => new ObjectId($this->uid)])
                ->orderBy(['_id' => SORT_DESC]);
        }else{
            $query = SocialAuthModel::find()
                ->orderBy(['_id' => SORT_DESC]);
        }
        $total_count = $query->count();
        $pagination = new Pagination(['totalCount' => $total_count]);
        $socials = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        return $this->render('//passport/widgets/socials-widget', [
            'socials' => $socials,
            'pagination' => $pagination,
        ]);
    }
}