<?php

namespace lnpay\node\controllers\api\v1;

use lnpay\node\models\LnNode;
use Yii;
use yii\web\BadRequestHttpException;

class LncliController extends NodeApiController
{
    public $modelClass = 'lnpay\node\models\LnNode';

    public function actionLookupinvoice($r_hash_str,$node_id)
    {
        try {
            return $this->nodeObject->getLndConnector()->lookupInvoice($r_hash_str);
        } catch (\Throwable $t) {
            throw new BadRequestHttpException($t->getMessage());
        }
    }

    public function actionGetinfo()
    {
        try {
            return json_decode($this->nodeObject->getLndConnector()->getInfo());
        } catch (\Throwable $t) {
            throw new BadRequestHttpException($t->getMessage());
        }
    }

    public function actionListchannels($chan_id=null)
    {
        try {
            $array = $this->nodeObject->getLndConnector()->listChannels();
            $arr = [];
            foreach ($array['channels'] as $channel) {
                $channel['nodeInfo'] = json_decode($this->nodeObject->getLndConnector()->nodeInfo(['pub_key'=>$channel['remotePubkey']]),TRUE);
                $arr[] = $channel;

                if ($chan_id) {
                    if ($chan_id == $channel['chanId']) {
                        return $channel;
                    }
                }
            }



            return $arr;
        } catch (\Throwable $t) {
            throw new BadRequestHttpException($t->getMessage());
        }
    }

    public function actionNodeinfo($pub_key='',$include_channels=false)
    {
        try {
            return json_decode($this->nodeObject->getLndConnector()->nodeInfo(compact('pub_key','include_channels')));
        } catch (\Throwable $t) {
            throw new BadRequestHttpException($t->getMessage());
        }
    }
}
