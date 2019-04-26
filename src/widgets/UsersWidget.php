<?php

namespace effsoft\eff\module\passport\widgets;

use effsoft\eff\EffWidget;
use effsoft\eff\module\passport\models\UserModel;
use MongoDB\BSON\Regex;
use yii\data\Pagination;

class UsersWidget extends EffWidget {

    public function run(){

        $q = \Yii::$app->request->get('q');
        if (empty($q)){
            $query = UserModel::find()
                ->orderBy(['_id' => SORT_DESC]);
        }else{
            $query = UserModel::find()->where(['email' => new Regex($q, 'i')])
                ->orderBy(['_id' => SORT_DESC]);
        }
        $total_count = $query->count();
        $pagination = new Pagination(['totalCount' => $total_count]);
        $users = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        return $this->render('//passport/widgets/users-widget', [
            'users' => $users,
            'pagination' => $pagination,
        ]);
    }
}