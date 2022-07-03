<?php

namespace lnpay\wallet\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use lnpay\wallet\models\Wallet;

/**
 * WalletSearch represents the model behind the search form of `lnpay\wallet\models\Wallet`.
 */
class WalletSearch extends Wallet
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'created_at', 'updated_at', 'balance','wallet_type_id'], 'integer'],
            [['ln_node_id'],'string'],
            [['user_label', 'external_hash', 'json_data'], 'safe'],
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
        $query = Wallet::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user_id' => $this->user_id,
            'balance' => $this->balance,
            'ln_node_id' => $this->ln_node_id,
            'wallet_type_id' => $this->wallet_type_id
        ]);

        $query->andFilterWhere(['like', 'user_label', $this->user_label])
            ->andFilterWhere(['like', 'external_hash', $this->external_hash])
            ->andFilterWhere(['like', 'json_data', $this->json_data]);

        return $dataProvider;
    }
}
