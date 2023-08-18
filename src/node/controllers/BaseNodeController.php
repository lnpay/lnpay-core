<?php

namespace lnpay\node\controllers;

use lnpay\node\models\LnNode;
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
            if ( (in_array($this->id,['rpc','authprofile','ln'])) && ($model = LnNode::findOne(\LNPay::$app->request->getQueryParam('id'))) !== null) {
                \LNPay::$app->session->set('ln_node_id',$model->id);
                $this->nodeObject = $model;
            } else if ($node_id = \LNPay::$app->session->get('ln_node_id')) {
                $this->nodeObject = LnNode::findOne($node_id);
            }

            if (@$this->nodeObject->user_id != \LNPay::$app->user->id) {
                \LNPay::$app->session->remove('ln_node_id');
                throw new BadRequestHttpException('Invalid node specified!');
            }

            \LNPay::$app->getView()->params['breadcrumbs'][] = ['label'=>'LN Nodes','url'=>\LNPay::$app->controller->module->homeUrl];
            \LNPay::$app->getView()->params['breadcrumbs'][] = ['label' => $this->nodeObject->alias, 'url' => ['index']];
            $this->module->sidebarView = '@app/node/views/_nav-node.php';

            return true;
        }

    }

}
    ?>