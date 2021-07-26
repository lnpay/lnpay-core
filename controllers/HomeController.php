<?php
namespace app\controllers;

use Yii;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\models\LoginForm;
use app\models\PasswordResetRequestForm;
use app\models\ResetPasswordForm;
use app\models\SignupForm;


/**
 * Home controller
 */
class HomeController extends Controller
{
    const TIM_USER_ID = 147;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup', 'wallet', 'confirm-verification'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout','wallet','confirm-verification'],
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }


    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction'
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => !YII_ENV_PROD ? 'testme' : null,
            ],
            'mfa-verify' => [
                'class' => 'vxm\mfa\VerifyAction',
                'viewFile' => 'mfa-verify', // the name of view file use to render view. If not set an action id will be use, in this case is `mfa-verify`
                'formVar' => 'model', // the name of variable use to parse [[\vxm\mfa\OtpForm]] object to view file.
                'retry' => true, // allow user retry when type wrong otp
                'successCallback' => [$this, 'mfaPassed'], // callable call when user type valid otp if not set [[yii\web\Controller::goBack()]] will be call.
                //'invalidCallback' => [$this, 'mfaOtpInvalid'], // callable call when user type wrong otp if not set and property `retry` is false [[yii\web\User::loginRequired()]] will be call, it should be use for set flash notice to user.
            ]
        ];
    }

    public function actionIndex()
    {
        if (Yii::$app->user->isGuest)
            return $this->render('index');
        else
            return $this->redirect(['/dashboard/home']);
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['/dashboard/home']);
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            $this->mfaPassed();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     *
     * @return \yii\web\Response
     */
    public function mfaPassed()
    {
        $returnUrl = Yii::$app->user->returnUrl;

        if ($returnUrl == '/') {
            $returnUrl = '/dashboard/home';
        }

        return $this->redirect($returnUrl);
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $sessionData = Yii::$app->session;

        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    Yii::$app->session->setFlash('new_user',1);
                    return $this->redirect(['/dashboard/home']);
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->redirect(Yii::$app->request->getReferrer());
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (\InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }


}
