<?php

namespace lnpay\wallet\exceptions;

class UnableToGenerateLnurlpayException extends \yii\base\Exception
{
    /**
     * Constructor.
     *
     * @param string     $path
     * @param int        $code
     * @param \Exception $previous
     */
    public function __construct($program_name, $code = 0, \yii\base\Exception $previous = null)
    {
        parent::__construct('Unable to generate lnurl pay link: ' . $program_name, $code, $previous);
    }
}