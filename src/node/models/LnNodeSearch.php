<?php

namespace lnpay\node\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use lnpay\node\models\LnNode;

/**
 * LnNodeSearch represents the model behind the search form of `lnpay\node\models\LnNode`.
 */
class LnNodeSearch extends LnNode
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'alias', 'ln_node_implementation_id', 'default_pubkey', 'uri', 'host', 'tls_cert', 'getinfo', 'json_data'], 'safe'],
            [['rpc_port', 'rest_port', 'ln_port', 'status_type_id', 'rpc_status_id', 'rest_status_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = LnNode::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'rpc_port' => $this->rpc_port,
            'rest_port' => $this->rest_port,
            'ln_port' => $this->ln_port,
            'status_type_id' => $this->status_type_id,
            'rpc_status_id' => $this->rpc_status_id,
            'rest_status_id' => $this->rest_status_id,
        ]);

        $query->andFilterWhere(['like', 'id', $this->id])
            ->andFilterWhere(['like', 'alias', $this->alias])
            ->andFilterWhere(['like', 'ln_node_implementation_id', $this->ln_node_implementation_id])
            ->andFilterWhere(['like', 'default_pubkey', $this->default_pubkey])
            ->andFilterWhere(['like', 'uri', $this->uri])
            ->andFilterWhere(['like', 'host', $this->host])
            ->andFilterWhere(['like', 'tls_cert', $this->tls_cert])
            ->andFilterWhere(['like', 'getinfo', $this->getinfo])
            ->andFilterWhere(['like', 'json_data', $this->json_data]);

        return $dataProvider;
    }
}
