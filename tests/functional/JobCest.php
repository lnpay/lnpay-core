<?php

use lnpay\jobs\AnalyticsLogJob;

class JobCest
{
    public function _before(\FunctionalTester $I)
    {

    }

    public function testAnalyticsLogJob(\FunctionalTester $I)
    {
        $id = \LNPay::$app->queue->push(new AnalyticsLogJob([
            'userId' => 174,
            'eventName' => 'TestEvent',
            'params'=>[]
        ]));

        expect_that($id);

        // Check whether the job is waiting for execution.
        expect(\LNPay::$app->queue->isWaiting($id))->true();

        \LNPay::$app->queue->run($repeat=false);

        //Since this event ships to amplitude...not much to test here

        // Check whether the job is waiting for execution.
        expect(\LNPay::$app->queue->isDone($id))->true();
    }

}