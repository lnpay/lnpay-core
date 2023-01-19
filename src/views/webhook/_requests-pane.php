<?php

if ($iwhr->response_status_code >= 200 && $iwhr->response_status_code <= 299)
    $responseBadge = '<span class="label label-success">'.$iwhr->response_status_code.'</span>';
else
    $responseBadge = '<span class="label label-danger">'.$iwhr->response_status_code.'</span>';


echo \yii\bootstrap4\Tabs::widget([
    'items' => [
        [
            'label' => 'Request',
            'content' => $this->render('_requests-pane-request',compact('iwhr')),
            'active' => true
        ],
        [
            'label' => 'Response '.$responseBadge,
            'encode'=>false,
            'content' => $this->render('_requests-pane-response',compact('iwhr')),
        ],
        [
            'label' => 'Redeliver',
            'url' => ['redeliver','iwhr_id'=>$iwhr->external_hash],
            'linkOptions'=>['data-confirm'=>'Resending request to: '.$iwhr->integrationWebhook->endpoint_url,'style'=>'background-color:green;color:white']
        ],
    ],
]);

?>