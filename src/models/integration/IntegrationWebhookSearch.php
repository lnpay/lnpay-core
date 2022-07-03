<?php

namespace lnpay\models\integration;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use lnpay\models\integration\IntegrationWebhook;
use Yii;

/**
 * IntegrationWebhookSearch represents the model behind the search form of `lnpay\models\integration\IntegrationWebhook`.
 */
class IntegrationWebhookSearch extends IntegrationWebhook
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'integration_service_id', 'status_type_id', 'created_at', 'updated_at'], 'integer'],
            [['external_hash', 'action_name_id', 'secret', 'http_method', 'content_type', 'endpoint_url', 'json_data'], 'safe'],
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
        $query = IntegrationWebhook::find();
        $query->where(['user_id'=>\LNPay::$app->user->id]);

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
            'id' => $this->id,
            'user_id' => $this->user_id,
            'integration_service_id' => $this->integration_service_id,
            'status_type_id' => $this->status_type_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'external_hash', $this->external_hash])
            ->andFilterWhere(['like', 'action_name_id', $this->action_name_id])
            ->andFilterWhere(['like', 'secret', $this->secret])
            ->andFilterWhere(['like', 'http_method', $this->http_method])
            ->andFilterWhere(['like', 'content_type', $this->content_type])
            ->andFilterWhere(['like', 'endpoint_url', $this->endpoint_url])
            ->andFilterWhere(['like', 'json_data', $this->json_data]);

        return $dataProvider;
    }
}
