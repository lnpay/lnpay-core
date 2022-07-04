<?php

namespace lnpay\models\log;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use lnpay\models\log\UserApiLog;

use Yii;

/**
 * UserApiLogSearch represents the model behind the search form of `lnpay\models\log\UserApiLog`.
 */
class UserApiLogSearch extends UserApiLog
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'created_at', 'status_code'], 'integer'],
            [['external_hash', 'api_key', 'ip_address', 'sdk', 'method', 'base_url', 'request_path', 'request_body', 'request_headers', 'response_body', 'response_headers'], 'safe'],
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
        $query = UserApiLog::find();
        $this->user_id = \LNPay::$app->user->id;

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
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
            'status_code' => $this->status_code,
            'method'=>$this->method
        ]);

        $query->andFilterWhere(['like', 'external_hash', $this->external_hash])
            ->andFilterWhere(['like', 'api_key', $this->api_key])
            ->andFilterWhere(['like', 'ip_address', $this->ip_address])
            ->andFilterWhere(['like', 'sdk', $this->sdk])
            ->andFilterWhere(['like', 'base_url', $this->base_url])
            ->andFilterWhere(['like', 'request_path', $this->request_path])
            ->andFilterWhere(['like', 'request_body', $this->request_body])
            ->andFilterWhere(['like', 'request_headers', $this->request_headers])
            ->andFilterWhere(['like', 'response_body', $this->response_body])
            ->andFilterWhere(['like', 'response_headers', $this->response_headers]);

        return $dataProvider;
    }
}
