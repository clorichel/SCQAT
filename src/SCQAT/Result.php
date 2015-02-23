<?php

namespace SCQAT;

/**
 * This is SCQAT result for current run
 */
class Result
{
    /**
     * Is the result a success or not
     * @var boolean
     */
    public $isSuccess;

    /**
     * The result value ("OK", "KO", "whatever")
     * @var string
     */
    public $value = null;

    /**
     * The result description, to better explain it
     * @var string
     */
    public $description = null;

    /**
     * The language name that was analyzed
     * @var string
     */
    public $languageName = null;

    /**
     * The analyzer name that was ran
     * @var string
     */
    public $analyzerName = null;

    /**
     * The filename that was analyzed
     * @var string
     */
    public $fileName = null;
}
