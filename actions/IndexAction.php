<?php

namespace common\modules\article\actions;

use common\modules\article\models\Article;
use yii\data\Pagination;
use yii\base\Action;

/**
 * Description of IndexAction
 *
 * @author makszipeter
 */
class IndexAction extends Action {

    public function run() {
        $articleQuery = \common\modules\article\helpers\ArticleHelper::getActiveArticleQuery();

        $provider = new \yii\data\ActiveDataProvider([
            'query' => $articleQuery,
            'pagination' => [
                'pageSize' => 12,
            ],
            'sort' => [
                'defaultOrder' => [
                    'publicated_at' => SORT_DESC,
                ]
            ],
        ]);

        return $this->controller->render('index', [
                    'dataProvider' => $provider,
        ]);
    }

}
