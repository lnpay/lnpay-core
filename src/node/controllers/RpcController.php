<?php

namespace lnpay\node\controllers;

use lnpay\models\integration\IntegrationWebhookSearch;
use lnpay\node\models\LnNode;
use lnpay\node\models\NodeListener;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\filters\VerbFilter;

/**
 * NodeController implements the CRUD actions for LnNode model.
 */
class RpcController extends BaseNodeController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all IntegrationWebhook models.
     * @return mixed
     */
    public function actionForwarder()
    {
        $node = \LNPay::$app->user->identity->lnNode;
        if (!$node) {
            return $this->redirect(['/node/dashboard']);
        }
        $searchModel = new IntegrationWebhookSearch();
        $dataProvider = $searchModel->search(\LNPay::$app->request->queryParams);


        return $this->render('rpc-forwarder', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all LnNode models.
     * @return mixed
     */
    public function actionListeners($action='',$nl_id='')
    {
        $node = $this->nodeObject;

        if ($action && $nl_id) {
            $nodeListener = NodeListener::findOne($nl_id);
            switch ($action) {
                case 'start':
                    $nodeListener->startListenerAndTurnOnAutostart();
                    break;
                case 'stop':
                    $nodeListener->stopListenerAndTurnOffAutostart();
                    break;
            }
            sleep(1);
            return $this->redirect(\LNPay::$app->request->referrer);
        }

        $provider = new ActiveDataProvider([
            'query' => $node->getNodeListeners()->andWhere(['!=','method',NodeListener::LND_RPC_SUBSCRIBE_CHANNEL_GRAPH]),
        ]);

        return $this->render('rpc-listeners', [
            'dataProvider' => $provider,
            'lnNode'=>$node
        ]);
    }

    public function actionRefreshListeners()
    {
        $n = $this->nodeObject;
        $n->removeLndRpcSubscribers();
        $n->spawnLndRpcSubscribers();
        sleep(5);
        return $this->redirect(\LNPay::$app->request->referrer);

    }
}