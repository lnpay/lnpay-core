<?php

namespace app\controllers\v1;

use app\components\HelperComponent;
use app\models\UserAccessKey;
use yii\filters\auth\QueryParamAuth;

use Yii;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\UnauthorizedHttpException;

class JobController extends BaseApiController
{
    public $modelClass = 'zhuravljov\yii\queue\monitor\records\ExecRecord';

    public function actions(){
        $actions = parent::actions();
        unset($actions['create']);

        unset($actions['view']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }

    protected function verbs(){
        return [
            //'create' => ['POST'],
            //'update' => ['PUT','PATCH','POST'],
            //'delete' => ['DELETE'],
            'view' =>   ['GET'],
            'index'=>   ['GET'],
        ];
    }

    public function checkPerm($push_id)
    {
        $r = \zhuravljov\yii\queue\monitor\records\PushRecord::findOne($push_id);
        if ($r) {
            $r = json_decode($r->job_data,TRUE);
            if (isset($r['access_key'])) {
                $uak = UserAccessKey::find()->where(['access_key'=>$r['access_key']])->one();
                if ($uak && ($uak->user_id == Yii::$app->user->id)) {
                    return true;
                }
            }
        }

        throw new UnauthorizedHttpException('You are not allowed to access this resource!');
    }
    public function actionView($id)
    {
        $this->checkPerm($id);
        $arr = [];
        $results = $this->modelClass::find()->where(['push_id'=>$id])->all();
        foreach ($results as $r) {
            unset($r['worker_id']);
            unset($r['memory_usage']);
            if ($r['error']) {
                if (stripos("in /app",$r['error'])!==0) {
                    $r['error'] = @explode("in /app",$r['error'])[0]; //this is so hood
                }
            }
            $arr[] = $r;
        }
        return $arr;
    }



}
