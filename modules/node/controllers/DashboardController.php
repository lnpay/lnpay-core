<?php

namespace app\modules\node\controllers;

use app\modules\node\models\NodeAddForm;
use app\modules\node\models\NodeCreateForm;
use app\modules\node\models\NodeListener;
use Yii;
use app\modules\node\models\LnNode;
use app\models\LnNodeSearch;
use yii\data\ActiveDataProvider;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * NodeController implements the CRUD actions for LnNode model.
 */
class DashboardController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Finds the LnNode model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return LnNode the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = LnNode::findOne($id)) !== null) {
            if ($model->user_id == Yii::$app->user->id)
                return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist');
    }

    /**
     * Sends user to first node, otherwise to add node
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionIndex()
    {
        $nodes = Yii::$app->user->identity->getLnNodeQuery();

        $nodeDp = new \yii\data\ActiveDataProvider([
            'query' => $nodes,
            'pagination' => [
                'pageSize' => 100,
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ]
            ],
        ]);

        if ($nodes->exists()) {
            return $this->render('index',compact('nodeDp','nodes'));
        } else {
            return $this->redirect('/node/dashboard/add');
        }

    }

    /**
     * Displays a single LnNode model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('node', [
            'node' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new LnNode model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionAdd()
    {
        $model = new NodeAddForm();
        $submittedMacaroonObject = null;

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                $submittedMacaroonObject = $model->submittedMacaroonObject;

                if ($model->readyToAdd) {
                    if ($node = $model->addNode()) {
                        return $this->redirect(['/node/ln/index','id'=>$node->id]);
                    } else {
                        $model = $node;
                    }
                }
            }
        }

        return $this->render('add', [
            'model' => $model,
            'submittedMacaroonObject'=>$submittedMacaroonObject,
            'nodeInfo'=>$model->nodeInfo
        ]);
    }

    /**
     * Creates a new LnNode model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new NodeCreateForm();

        if (YII_ENV_PROD && Yii::$app->user->identity->getLnNodeQuery()->count() > 2) {
            Yii::$app->session->setFlash('error','Maxing out at 3 nodes per user for now');
            return $this->redirect(Yii::$app->request->referrer);
        }

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                if ($node = $model->createNode()) {
                    Yii::$app->session->setFlash('new_node_details',$node);
                    Yii::$app->session->setFlash('success','Node: '.$node['node_id'].' is launching!');
                    return $this->redirect(['/node/dashboard/index']);
                } else {
                    $model = $node;
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing LnNode model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing LnNode model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $node = $this->findModel($id);
        $node->delete();

        return $this->redirect(['/node/dashboard']);
    }




}
