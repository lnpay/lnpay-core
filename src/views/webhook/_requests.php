<?php


$items = [];
foreach ($model->getWebhookRequests()->orderBy('created_at DESC')->limit(20)->all() as $iwhr) {
    if ($iwhr->response_status_code >= 200 && $iwhr->response_status_code <= 299)
        $responseBadge = '<span class="badge badge-success">'.$iwhr->response_status_code.'</span>';
    else
        $responseBadge = '<span class="badge badge-danger">'.$iwhr->response_status_code.'</span>';

    $items[] = [
        'label' => date('Y-m-d H:i:s T', $iwhr->created_at).' - '.$iwhr->integrationWebhook->http_method.' '.$iwhr->integrationWebhook->endpoint_url.' '.$responseBadge,
        'content' => $this->render('_requests-pane',compact('iwhr')),
        'encode'=>false
    ];
}

echo \yii\bootstrap4\Accordion::widget([
    'items' => $items
]);

