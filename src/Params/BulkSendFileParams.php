<?php

namespace Nullform\ZeroBounce\Params;

/**
 * @package Nullform\ZeroBounce
 * @see     https://www.zerobounce.net/docs/email-validation-api-quickstart/v2-send-file/ Validation API: Send File
 * @see     https://www.zerobounce.net/docs/ai-scoring-api/send-file/ AI Scoring API: Send File
 */
class BulkSendFileParams extends AbstractParams
{
    /**
     * The URL will be used to call back when the validation is completed.
     *
     * @var string
     */
    public $return_url;

    /**
     * The column index of the email address in the file. Index starts from 1.
     * Required.
     *
     * @var int
     */
    public $email_address_column;

    /**
     * If the first row from the submitted file is a header row.
     *
     * @var bool
     */
    public $has_header_row;

    /**
     * The column index of the first name column.
     *
     * @var int
     */
    public $first_name_column;

    /**
     * The column index of the last name column.
     *
     * @var int
     */
    public $last_name_column;

    /**
     * The column index of the gender column.
     *
     * @var int
     */
    public $gender_column;

    /**
     * The IP Address the email signed up from.
     *
     * @var int
     */
    public $ip_address_column;
}