<?php

namespace Nullform\ZeroBounce\Models;

/**
 * Uploaded file info.
 *
 * @package Nullform\ZeroBounce
 * @see     https://www.zerobounce.net/docs/email-validation-api-quickstart/v2-file-status/ Validation API: File Status
 */
class BulkFile extends AbstractModel
{
    const TYPE_VALIDATION = 'validation';
    const TYPE_SCORING = 'scoring';

    const STATUS_QUEUED = 'Queued';
    const STATUS_PROCESSING = 'Processing';
    const STATUS_COMPLETE = 'Complete';

    /**
     * File ID.
     *
     * Example: 935bf1fc-27ab-40df-b2a6-f855eceae470.
     *
     * @var string
     */
    public $file_id;

    /**
     * Filename.
     *
     * Example: emails.csv.
     *
     * @var string
     */
    public $file_name;

    /**
     * Upload date.
     *
     * Example: 12/12/2019 8:40:05 PM.
     *
     * @var string
     */
    public $upload_date;

    /**
     * Upload date (timestamp).
     *
     * @var int
     */
    public $upload_date_ts;

    /**
     * Status.
     *
     * - Queued
     * - Processing
     * - Complete
     *
     * @var string
     * @see BulkFile::STATUS_QUEUED
     * @see BulkFile::STATUS_PROCESSING
     * @see BulkFile::STATUS_COMPLETE
     */
    public $file_status;

    /**
     * Сomplete percentage.
     *
     * Example: 20%.
     *
     * @var string
     */
    public $complete_percentage;

    /**
     * Сomplete percentage (integer).
     *
     * Example: 20.
     *
     * @var int
     */
    public $complete_percentage_int;

    /**
     * The URL will be used to call back when the validation is completed.
     *
     * @var string
     */
    public $return_url;

    /**
     * Is file queued.
     *
     * @return bool
     */
    public function isQueued(): bool
    {
        return $this->file_status === static::STATUS_QUEUED;
    }

    /**
     * Is file processing.
     *
     * @return bool
     */
    public function isProcessing(): bool
    {
        return $this->file_status === static::STATUS_PROCESSING;
    }

    /**
     * Is file complete.
     *
     * @return bool
     */
    public function isComplete(): bool
    {
        return $this->file_status === static::STATUS_COMPLETE;
    }

    /**
     * @inheritDoc
     */
    public function __construct(?\stdClass $obj = null)
    {
        parent::__construct($obj);

        if (!is_null($this->upload_date)) {
            $this->upload_date_ts = strtotime($this->upload_date);
        }
        if (!is_null($this->complete_percentage)) {
            $this->complete_percentage_int = (int)$this->complete_percentage;
        }
    }
}