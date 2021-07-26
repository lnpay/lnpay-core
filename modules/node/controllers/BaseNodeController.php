<?php

namespace app\modules\node\controllers;

use app\modules\node\models\LnNode;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;


/**
 * LnNodeProfileController implements the CRUD actions for LnNodeProfile model.
 */
class BaseNodeController extends Controller
{
    public $nodeObject;

    public function beforeAction($event)
    {
        if (parent::beforeAction($event)) {
            if ( ($this->id == 'ln') && ($model = LnNode::findOne(Yii::$app->request->getQueryParam('id'))) !== null) {
                Yii::$app->session->set('ln_node_id',$model->id);
                $this->nodeObject = $model;
            } else if ($node_id = Yii::$app->session->get('ln_node_id')) {
                $this->nodeObject = LnNode::findOne($node_id);
            }

            if (@$this->nodeObject->user_id != Yii::$app->user->id) {
                Yii::$app->session->remove('ln_node_id');
                throw new BadRequestHttpException('Invalid node specified!');
            }



            Yii::$app->getView()->params['breadcrumbs'][] = ['label'=>'LN Nodes','url'=>Yii::$app->controller->module->homeUrl];
            Yii::$app->getView()->params['breadcrumbs'][] = ['label' => $this->nodeObject->alias, 'url' => ['index']];
            $this->module->sidebarView = '@app/modules/node/views/_nav-node.php';

            return true;
        }

    }

}
    ?>