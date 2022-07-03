<?php

use lnpay\models\LnNodeSearch;
use lnpay\node\models\NodeListener;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $searchModel lnpay\models\LnNodeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Connect';
$this->params['breadcrumbs'][] = $this->title;
?>

<h1>Connect to your node</h1>
<?php

echo \yii\widgets\DetailView::widget([
    'model' => $node,
    'attributes' => [
        'uri',
        [
            'label' => 'gRPC Host',
            'value' => $node->host.':'.$node->rpc_port
        ],
        [
            'label' => 'REST Host',
            'value' => 'https://'.$node->host.':'.$node->rest_port,
        ],
    ],
]);


echo \yii\bootstrap\Tabs::widget([
    'items' => [
        [
            'label' => 'Hex',
            'content' => $this->render('_connect-hex',compact('node')),
            'active' => true
        ],
        [
            'label' => 'Base64',
            'content' => $this->render('_connect-base64',compact('node')),
            'headerOptions' => [],
        ],
        [
            'label' => 'LNDConnect',
            'content' =>$this->render('_connect-lndconnect',compact('node'))
        ],
    ],
]);

?>
