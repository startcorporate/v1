<?php

namespace common\modules\article\actions;

use Yii;
use yii\base\Action;
use kartik\form\ActiveForm;
use common\modules\article\models\Article;
use common\modules\comment\models\CommentForm;

/**
 * bejegyzés végoldal és kommentelés
 *
 * @author makszipeter
 */
class ViewAction extends Action {

    /**
     * 
     * @param type string 
     * the slug attribute of the article
     * @return render to view page
     */
    public function run($slug) {
        $viewModel = Article::findBySlug($slug);
        $viewModel->updateCounters(['visits' =>1]);

        $commentModel = new CommentForm();
        if (!Yii::$app->user->isGuest) {
            if (Yii::$app->request->isAjax && $commentModel->load(Yii::$app->request->post())) {
                \Yii::info(Yii::$app->request->post());
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($commentModel);
            } elseif ($commentModel->load(Yii::$app->request->post())) {
                $commentModel->user_id = Yii::$app->user->identity->id;
                $commentModel->article_id = $viewModel->id;
                $comment = new \common\modules\comment\models\Comment();
                $comment->setAttributes($commentModel->getAttributes());
                $comment->save();
                $commentModel = new CommentForm();
            }
        }
        $viewModel = Article::findBySlug($slug);
        return $this->controller->render('view', [
                    'model' => $viewModel,
                    'commentModel' => $commentModel,
        ]);
    }

}
