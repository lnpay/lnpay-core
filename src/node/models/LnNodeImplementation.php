<?php

namespace lnpay\node\models;

use Yii;

/**
 * This is the model class for table "ln_node_implementation".
 *
 * @property string $name
 * @property string|null $display_name
 * @property string|null $json_data
 *
 * @property LnNode[] $lnNodes
 */
class LnNodeImplementation extends \yii\db\ActiveRecord
{
    const LND_DEFAULT_REST_PORT = 8080;
    const LND_DEFAULT_GRPC_PORT = 10009;
    const LND_DEFAULT_LN_PORT = 9735;

    const LND_SUBNODE_DEFAULT_RPC_CONTROL_PORT = 10001;
    const LND_SUBNODE_DEFAULT_RPC_LN_PORT = 10002;
    const LND_SUBNODE_DEFAULT_REST_CONTROL_PORT = 10003;
    const LND_SUBNODE_DEFAULT_REST_LN_PORT = 10004;


    const LND_IMPLEMENTATION_ID = 'lnd';
    const LND_SUBNODE = 'lnd_cluster_subnode';


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ln_node_implementation';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['json_data'], 'safe'],
            [['name'], 'string', 'max' => 64],
            [['display_name'], 'string', 'max' => 255],
            [['name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Name',
            'display_name' => 'Display Name',
            'json_data' => 'Json Data',
        ];
    }

    /**
     * Gets query for [[LnNodes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLnNodes()
    {
        return $this->hasMany(LnNode::className(), ['ln_node_implementation_id' => 'name']);
    }
}
