<?php

namespace Nullform\ZeroBounce\Models;

/**
 * API usage statistics.
 *
 * @package Nullform\ZeroBounce
 * @see https://www.zerobounce.net/docs/email-validation-api-quickstart/v2-get-api-usage/ Validation API: API Usage
 */
class Usage extends AbstractModel
{
    /**
     * Total number of times the API has been called.
     * 
     * @var int
     */
    public $total;

    /**
     * Total valid email addresses returned by the API.
     * 
     * @var int
     */
    public $status_valid;

    /**
     * Total invalid email addresses returned by the API.
     * 
     * @var int
     */
    public $status_invalid;

    /**
     * Total catch-all email addresses returned by the API.
     * 
     * @var int
     */
    public $status_catch_all;

    /**
     * Total do not mail email addresses returned by the API.
     * 
     * @var int
     */
    public $status_do_not_mail;

    /**
     * Total spamtrap email addresses returned by the API.
     * 
     * @var int
     */
    public $status_spamtrap;

    /**
     * Total unknown email addresses returned by the API.
     * 
     * @var int
     */
    public $status_unknown;

    /**
     * Total number of times the API has a sub status of "toxic".
     * 
     * @var int
     */
    public $sub_status_toxic;

    /**
     * Total number of times the API has a sub status of "disposable".
     * 
     * @var int
     */
    public $sub_status_disposable;

    /**
     * Total number of times the API has a sub status of "role_based".
     * 
     * @var int
     */
    public $sub_status_role_based;

    /**
     * Total number of times the API has a sub status of "possible_trap".
     * 
     * @var int
     */
    public $sub_status_possible_trap;

    /**
     * Total number of times the API has a sub status of "global_suppression".
     * 
     * @var int
     */
    public $sub_status_global_suppression;

    /**
     * Total number of times the API has a sub status of "timeout_exceeded".
     * 
     * @var int
     */
    public $sub_status_timeout_exceeded;

    /**
     * Total number of times the API has a sub status of "mail_server_temporary_error".
     * 
     * @var int
     */
    public $sub_status_mail_server_temporary_error;

    /**
     * Total number of times the API has a sub status of "mail_server_did_not_respond".
     * 
     * @var int
     */
    public $sub_status_mail_server_did_not_respond;

    /**
     * Total number of times the API has a sub status of "greylisted".
     * 
     * @var int
     */
    public $sub_status_greylisted;

    /**
     * Total number of times the API has a sub status of "antispam_system".
     * 
     * @var int
     */
    public $sub_status_antispam_system;

    /**
     * Total number of times the API has a sub status of "does_not_accept_mail".
     * 
     * @var int
     */
    public $sub_status_does_not_accept_mail;

    /**
     * Total number of times the API has a sub status of "exception_occurred".
     * 
     * @var int
     */
    public $sub_status_exception_occurred;

    /**
     * Total number of times the API has a sub status of "failed_syntax_check".
     * 
     * @var int
     */
    public $sub_status_failed_syntax_check;

    /**
     * Total number of times the API has a sub status of "mailbox_not_found".
     * 
     * @var int
     */
    public $sub_status_mailbox_not_found;

    /**
     * Total number of times the API has a sub status of "unroutable_ip_address".
     * 
     * @var int
     */
    public $sub_status_unroutable_ip_address;

    /**
     * Total number of times the API has a sub status of "possible_typo".
     * 
     * @var int
     */
    public $sub_status_possible_typo;

    /**
     * Total number of times the API has a sub status of "no_dns_entries".
     * 
     * @var int
     */
    public $sub_status_no_dns_entries;

    /**
     * Total role based catch alls the API has a sub status of "role_based_catch_all".
     * 
     * @var int
     */
    public $sub_status_role_based_catch_all;

    /**
     * Total number of times the API has a sub status of "mailbox_quota_exceeded".
     * 
     * @var int
     */
    public $sub_status_mailbox_quota_exceeded;

    /**
     * Total forcible disconnects the API has a sub status of "forcible_disconnect".
     * 
     * @var int
     */
    public $sub_status_forcible_disconnect;

    /**
     * Total failed SMTP connections the API has a sub status of "failed_smtp_connection".
     * 
     * @var int
     */
    public $sub_status_failed_smtp_connection;

    /**
     * Start date of query.
     *
     * Example: 1/1/2018
     *
     * @var string
     */
    public $start_date;

    /**
     * End date of query.
     *
     * Example: 12/12/2019
     *
     * @var string
     */
    public $end_date;
}