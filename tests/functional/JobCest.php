<?php

use lnpay\jobs\AnalyticsLogJob;

class JobCest
{
    public function _before(\FunctionalTester $I)
    {

    }

    public function testAnalyticsLogJob(\FunctionalTester $I)
    {
        $id = Yii::$app->queue->push(new AnalyticsLogJob([
            'userId' => 174,
            'eventName' => 'TestEvent',
            'params'=>[]
        ]));

        expect_that($id);

        // Check whether the job is waiting for execution.
        expect(Yii::$app->queue->isWaiting($id))->true();

        Yii::$app->queue->run($repeat=false);

        //Since this event ships to amplitude...not much to test here

        // Check whether the job is waiting for execution.
        expect(Yii::$app->queue->isDone($id))->true();
    }

}