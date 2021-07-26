<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\modules\node\models\LnNode;
use yii\console\Controller;

use Yii;


class NodeController extends Controller
{


    /**
     * Fixes any node listener problems
     * @param $nodeId
     * @throws \Exception
     */
    public function actionRefreshListeners($nodeId=null)
    {
        if ($nodeId == 'all') {
            foreach (LnNode::find()->all() as $n) {
                $n->removeLndRpcSubscribers();
                $n->spawnLndRpcSubscribers();
                sleep(3);
                echo "Refreshed:".$n->id."\n";
            }
        } else {
            $n = LnNode::findOne($nodeId);
            $n->removeLndRpcSubscribers();
            $n->spawnLndRpcSubscribers();
        }

    }
}
