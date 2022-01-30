<?php

namespace lnpay\node\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use lnpay\node\models\LnNodeProfile;

/**
 * LnNodeProfileSearch represents the model behind the search form of `lnpay\node\models\LnNodeProfile`.
 */
class LnNodeProfileSearch extends LnNodeProfile
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'ln_node_id', 'user_label', 'macaroon_hex', 'username', 'password', 'access_key', 'json_data'], 'safe'],
            [['created_at', 'updated_at', 'user_id', 'is_default', 'status_type_id'], 'integer'],
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
        $query = LnNodeProfile::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user_id' => $this->user_id,
            'is_default' => $this->is_default,
            'status_type_id' => $this->status_type_id,
        ]);

        $query->andFilterWhere(['like', 'id', $this->id])
            ->andFilterWhere(['like', 'ln_node_id', $this->ln_node_id])
            ->andFilterWhere(['like', 'user_label', $this->user_label])
            ->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'password', $this->password])
            ->andFilterWhere(['like', 'access_key', $this->access_key])
            ->andFilterWhere(['like', 'json_data', $this->json_data]);

        return $dataProvider;
    }
}
