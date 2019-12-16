<?php

namespace Nullform\ZeroBounce\Models;

/**
 * Validated email.
 *
 * @package Nullform\ZeroBounce
 * @see     https://www.zerobounce.net/docs/email-validation-api-quickstart/v2-validate-emails/ Validation API: Validate Emails
 */
class Email extends AbstractModel
{
    /**
     * These are emails that we determined to be valid and safe to email to, they will have a very low bounce rate of
     * under 2%. If you receive bounces it can be because your IP might be blacklisted where our IP was not.
     * Sometimes the email accounts exist, but they are only accepting mail from people in their contact lists.
     * Sometimes you will get throttle on number of emails you can send to a specific domain per hour.
     * It's important to look at the SMTP Bounce codes to determine why.
     */
    const STATUS_VALID = 'valid';

    /**
     * These are emails that we determined to be invalid, please delete them from your mailing list.
     * The results are 99.999% accurate.
     */
    const STATUS_INVALID = 'invalid';

    /**
     * These emails are impossible to validate without sending a real email and waiting for a bounce.
     * The term Catch-all means that the email server tells you that the email is valid, whether it's valid or invalid.
     * If you want to email these addresses, I suggest you segment them into a catch-all group and know that some of
     * these will most likely bounce.
     */
    const STATUS_CATCH_ALL = 'catch-all';

    /**
     * These emails are believed to be spamtraps and should not be mailed.
     * We have technology in place to determine if certain emails should be classified as spamtrap. We don't know all
     * the spamtrap email addresses, but we do know a lot of them.
     *
     * @see https://www.zerobounce.net/spam-traps.html
     */
    const STATUS_SPAMTRAP = 'spamtrap';

    /**
     * These emails are of people who are known to click the abuse links in emails, hence abusers or complainers.
     * We recommend not emailing these addresses.
     */
    const STATUS_ABUSE = 'abuse';

    /**
     * These emails are of companies, role-based, or people you just want to avoid emailing to.
     * They are broken down into 6 sub-categories "disposable","toxic", "role_based", "role_based_catch_all",
     * "global_suppression" and "possible_trap". Examine this file and determine if you want to email these address.
     * They are valid email addresses, but shouldn't be mailed in most cases.
     */
    const STATUS_DO_NOT_MAIL = 'do_not_mail';

    /**
     * These emails we weren't able to validate for one reason or another.
     * Typical cases are "Their mail server was down" or "the anti-spam system is blocking us".
     * In most cases, 80% unknowns are invalid/bad email addresses. We have the lowest "unknowns" of any email
     * validator, and we don't make this statement lightly. We paid and tested email lists at over 50 different
     * validation companies to compare results. If you do encounter a large number of unknowns, please submit those
     * for re-validation. Remember you are not charged for unknown results, credits will be credited back.
     * If you still have a large number, contact us and we will take a look and verify.
     */
    const STATUS_UNKNOWN = 'unknown';


    /**
     * (valid) These emails addresses act as forwarders/aliases and are not real inboxes, for example if you send an
     * email to forward@example.com and then the email is forwarded to realinbox@example.com. It's a valid email
     * address and you can send to them, it's just a little more information about the email address. We can sometimes
     * detect alias email addresses and when we do we let you know.
     */
    const SUBSTATUS_ALIAS_ADDRESS = 'alias_address';

    /**
     * (unknown) These emails have anti-spam systems deployed that are preventing us from validating these emails.
     * You can submit these to us through the contact us screen to look into.
     */
    const SUBSTATUS_ANTISPAM_SYSTEM = 'antispam_system';

    /**
     * (do_not_mail) These are temporary emails created for the sole purpose to sign up to websites without giving
     * their real email address. These emails are short lived from 15 minutes to around 6 months. There is only 2
     * values (True and False). If you have valid emails with this flag set to TRUE, you shouldn't email them.
     */
    const SUBSTATUS_DISPOSABLE = 'disposable';

    /**
     * (invalid) These domains only send mail and don't accept it.
     */
    const SUBSTATUS_DOES_NOT_ACCEPT_MAIL = 'does_not_accept_mail';

    /**
     * (unknown) These emails caused an exception when validating. If this happens repeatedly, please let us know.
     */
    const SUBSTATUS_EXCEPTION_OCCURRED = 'exception_occurred';

    /**
     * (unknown) These emails belong to a mail server that won't allow an SMTP connection. Most of the time, these
     * emails will end up being invalid.
     */
    const SUBSTATUS_FAILED_SMTP_CONNECTION = 'failed_smtp_connection';

    /**
     * (invalid) Emails that fail RFC syntax protocols
     */
    const SUBSTATUS_FAILED_SYNTAX_CHECK = 'failed_syntax_check';

    /**
     * (unknown) These emails belong to a mail server that disconnects immediately upon connecting. Most of the time,
     * these emails will end up being invalid.
     */
    const SUBSTATUS_FORCIBLE_DISCONNECT = 'forcible_disconnect';

    /**
     * (do_not_mail) These emails are found in many popular global suppression lists (GSL), they consist of known ISP
     * complainers, direct complainers, purchased addresses, domains that don't send mail, and known litigators.
     */
    const SUBSTATUS_GLOBAL_SUPPRESSION = 'global_suppression';

    /**
     * (unknown) Emails where we are temporarily unable to validate them. A lot of times if you resubmit these emails
     * they will validate on a second pass.
     */
    const SUBSTATUS_GREYLISTED = 'greylisted';

    /**
     * (valid) If a valid gmail.com email address starts with a period '.' we will remove it, so the email address
     * is compatible with all mailing systems.
     */
    const SUBSTATUS_LEADING_PERIOD_REMOVED = 'leading_period_removed';

    /**
     * (invalid) These emails addresses are valid in syntax, but do not exist. These emails are marked invalid.
     */
    const SUBSTATUS_MAILBOX_NOT_FOUND = 'mailbox_not_found';

    /**
     * (invalid) These emails exceeded their space quota and are not accepting emails. These emails are marked invalid.
     */
    const SUBSTATUS_MAILBOX_QUOTA_EXCEEDED = 'mailbox_quota_exceeded';

    /**
     * (unknown) These emails belong to a mail server that is not responding to mail commands. Most of the time, these
     * emails will end up being invalid.
     */
    const SUBSTATUS_MAIL_SERVER_DID_NOT_RESPOND = 'mail_server_did_not_respond';

    /**
     * (unknown) These emails belong to a mail server that is returning a temporary error. Most of the time, these
     * emails will end up being invalid.
     */
    const SUBSTATUS_MAIL_SERVER_TEMPORARY_ERROR = 'mail_server_temporary_error';

    /**
     * (invalid) These emails are valid in syntax, but the domain doesn't have any records in DNS or have incomplete
     * DNS Records. Therefore, mail programs will be unable to or have difficulty sending to them. These emails are
     * marked invalid.
     */
    const SUBSTATUS_NO_DNS_ENTRIES = 'no_dns_entries';

    /**
     * (invalid) These are emails of commonly misspelled popular domains. These emails are marked invalid.
     */
    const SUBSTATUS_POSSIBLE_TYPO = 'possible_typo';

    /**
     * (do_not_mail) These emails contain keywords that might correlate to possible spam traps like spam@
     * or @spamtrap.com. Examine these before deciding to send emails to them or not.
     */
    const SUBSTATUS_POSSIBLE_TRAP = 'possible_trap';

    /**
     * (do_not_mail) These emails belong to a position or a group of people, like sales@ info@ and contact@.
     * Role-based emails have a strong correlation to people reporting mails sent to them as spam and abuse.
     */
    const SUBSTATUS_ROLE_BASED = 'role_based';

    /**
     * (do_not_mail) These emails are role-based and also belong to a catch_all domain.
     */
    const SUBSTATUS_ROLE_BASED_CATCH_ALL = 'role_based_catch_all';

    /**
     * (unknown) These emails belong to a mail server that is responding extremely slow. Most of the time, these emails
     * will end up being invalid.
     */
    const SUBSTATUS_TIMEOUT_EXCEEDED = 'timeout_exceeded';

    /**
     * (do_not_mail) These email addresses are known to be abuse, spam, or bot created emails. If you have valid emails
     * with this flag set to TRUE, you shouldn't email them.
     */
    const SUBSTATUS_TOXIC = 'toxic';

    /**
     * (invalid) These emails domains point to an un-routable IP address, these are marked invalid.
     */
    const SUBSTATUS_UNROUTABLE_IP_ADDRESS = 'unroutable_ip_address';


    /**
     * The email address you are validating.
     *
     * @var string
     */
    public $address;

    /**
     * Status.
     *
     * [valid, invalid, catch-all, unknown, spamtrap, abuse, do_not_mail]
     *
     * @var string
     * @see https://www.zerobounce.net/docs/email-validation-api-quickstart/v2-status-codes/ Validation API: Status Codes
     */
    public $status;

    /**
     * Substatus.
     *
     * [antispam_system, greylisted, mail_server_temporary_error, forcible_disconnect, mail_server_did_not_respond,
     * timeout_exceeded, failed_smtp_connection, mailbox_quota_exceeded, exception_occurred, possible_traps, role_based,
     * global_suppression, mailbox_not_found, no_dns_entries, failed_syntax_check, possible_typo, unroutable_ip_address,
     * leading_period_removed, does_not_accept_mail, alias_address, role_based_catch_all, disposable, toxic]
     *
     * @var string
     * @see https://www.zerobounce.net/docs/email-validation-api-quickstart/v2-status-codes/ Validation API: Status Codes
     */
    public $sub_status;

    /**
     * If the email comes from a free provider.
     *
     * @var bool
     */
    public $free_email;

    /**
     * Suggestive Fix for an email typo.
     *
     * @var string
     */
    public $did_you_mean;

    /**
     * The portion of the email address before the "@" symbol.
     *
     * @var string
     */
    public $account;

    /**
     * The portion of the email address after the "@" symbol.
     *
     * @var string
     */
    public $domain;

    /**
     * Age of the email domain in days or null.
     *
     * @var int|null
     */
    public $domain_age_days;

    /**
     * The SMTP Provider of the email or null (BETA).
     *
     * @var string|null
     */
    public $smtp_provider;

    /**
     * The preferred MX record of the domain.
     *
     * @var string|null
     */
    public $mx_record;

    /**
     * Does the domain have an MX record.
     *
     * @var bool
     */
    public $mx_found;

    /**
     * The first name of the owner of the email when available or null.
     *
     * @var string|null
     */
    public $firstname;

    /**
     * The last name of the owner of the email when available or null.
     *
     * @var string|null
     */
    public $lastname;

    /**
     * The gender of the owner of the email when available or null.
     *
     * @var string|null
     */
    public $gender;

    /**
     * The country of the IP passed in.
     *
     * @var string|null
     */
    public $country;

    /**
     * The region/state of the IP passed in.
     *
     * @var string|null
     */
    public $region;

    /**
     * The city of the IP passed in.
     *
     * @var string|null
     */
    public $city;

    /**
     * The zipcode of the IP passed in.
     *
     * @var string|null
     */
    public $zipcode;

    /**
     * The UTC time the email was validated.
     *
     * Example: 2019-12-10 19:39:52.624
     *
     * @var string
     */
    public $processed_at;


    /**
     * @inheritDoc
     */
    public function __construct(?\stdClass $obj = null)
    {
        parent::__construct($obj);

        if (!is_null($this->domain_age_days)) {
            $this->domain_age_days = (int)$this->domain_age_days;
        }
    }

    /**
     * If the email comes from a free provider.
     *
     * @return bool
     */
    public function isFree(): bool
    {
        return (bool)$this->free_email;
    }

    /**
     * These are emails that we determined to be valid and safe to email to, they will have a very low bounce rate of
     * under 2%.
     *
     * @return bool
     * @see Email::STATUS_VALID
     */
    public function isSafe(): bool
    {
        return $this->status === static::STATUS_VALID;
    }

    /**
     * These are emails that we determined to be invalid.
     *
     * @return bool
     * @see Email::STATUS_INVALID
     */
    public function isInvalid(): bool
    {
        return $this->status === static::STATUS_INVALID;
    }

    /**
     * These emails are impossible to validate without sending a real email and waiting for a bounce.
     *
     * @return bool
     * @see Email::STATUS_CATCH_ALL
     */
    public function isCatchAll(): bool
    {
        return $this->status === static::STATUS_CATCH_ALL;
    }

    /**
     * These emails are believed to be spamtraps and should not be mailed.
     *
     * @return bool
     * @see Email::STATUS_SPAMTRAP
     */
    public function isSpamtrap(): bool
    {
        return $this->status === static::STATUS_SPAMTRAP;
    }

    /**
     * These emails are of people who are known to click the abuse links in emails, hence abusers or complainers.
     *
     * @return bool
     * @see Email::STATUS_ABUSE
     */
    public function isAbuse(): bool
    {
        return $this->status === static::STATUS_ABUSE;
    }

    /**
     * These emails are of companies, role-based, or people you just want to avoid emailing to.
     *
     * @return bool
     * @see Email::STATUS_DO_NOT_MAIL
     */
    public function isDoNotMail(): bool
    {
        return $this->status === static::STATUS_DO_NOT_MAIL;
    }

    /**
     * These emails we weren't able to validate for one reason or another.
     *
     * @return bool
     * @see Email::STATUS_UNKNOWN
     */
    public function isUnknown(): bool
    {
        return $this->status === static::STATUS_UNKNOWN;
    }

    /**
     * These emails addresses are valid in syntax, but do not exist.
     *
     * @return bool
     * @see Email::SUBSTATUS_MAILBOX_NOT_FOUND
     */
    public function isNotFound(): bool
    {
        return $this->sub_status === static::SUBSTATUS_MAILBOX_NOT_FOUND;
    }

    /**
     * These emails are valid in syntax, but the domain doesn't have any records in DNS or have incomplete DNS Records.
     *
     * @return bool
     * @see Email::SUBSTATUS_NO_DNS_ENTRIES
     */
    public function isNoDnsEntries(): bool
    {
        return $this->sub_status === static::SUBSTATUS_NO_DNS_ENTRIES;
    }

    /**
     * These are emails of commonly misspelled popular domains.
     *
     * @return bool
     * @see Email::SUBSTATUS_POSSIBLE_TYPO
     */
    public function isPossibleTypo(): bool
    {
        return $this->sub_status === static::SUBSTATUS_POSSIBLE_TYPO;
    }

    /**
     * These are temporary emails created for the sole purpose to sign up to websites without giving their real email
     * address.
     *
     * @return bool
     * @see Email::SUBSTATUS_DISPOSABLE
     */
    public function isDisposable(): bool
    {
        return $this->sub_status === static::SUBSTATUS_DISPOSABLE;
    }

    /**
     * These email addresses are known to be abuse, spam, or bot created emails.
     *
     * @return bool
     */
    public function isToxic(): bool
    {
        return $this->sub_status === static::SUBSTATUS_TOXIC;
    }

    /**
     * Is there any IP address information.
     *
     * @return bool
     */
    public function hasAnyIpInfo(): bool
    {
        return !empty($this->country) || !empty($this->city) || !empty($this->region) || !empty($this->zipcode);
    }

    /**
     * The time the email was validated (Unix timestamp).
     *
     * @return int
     */
    public function processedTimestamp(): int
    {
        return (int)strtotime($this->processed_at);
    }

    /**
     * Status description.
     *
     * @return string
     * @see https://www.zerobounce.net/docs/email-validation-api-quickstart/v2-status-codes/ Validation API: Status Codes
     */
    public function statusDescription(): string
    {
        $description = '';

        $statuses = [
            static::STATUS_VALID       => 'These are emails that we determined to be valid and safe to email to, they will have a very low bounce rate of under 2%. If you receive bounces it can be because your IP might be blacklisted where our IP was not. Sometimes the email accounts exist, but they are only accepting mail from people in their contact lists. Sometimes you will get throttle on number of emails you can send to a specific domain per hour. It\'s important to look at the SMTP Bounce codes to determine why.',
            static::STATUS_INVALID     => 'These are emails that we determined to be invalid, please delete them from your mailing list. The results are 99.999% accurate.',
            static::STATUS_CATCH_ALL   => 'These emails are impossible to validate without sending a real email and waiting for a bounce. The term Catch-all means that the email server tells you that the email is valid, whether it\'s valid or invalid. If you want to email these addresses, I suggest you segment them into a catch-all group and know that some of these will most likely bounce.',
            static::STATUS_SPAMTRAP    => 'These emails are believed to be spamtraps and should not be mailed. We have technology in place to determine if certain emails should be classified as spamtrap. We don\'t know all the spamtrap email addresses, but we do know a lot of them.',
            static::STATUS_ABUSE       => 'These emails are of people who are known to click the abuse links in emails, hence abusers or complainers. We recommend not emailing these addresses.',
            static::STATUS_DO_NOT_MAIL => 'These emails are of companies, role-based, or people you just want to avoid emailing to. They are broken down into 6 sub-categories "disposable","toxic", "role_based", "role_based_catch_all", "global_suppression" and "possible_trap". Examine this file and determine if you want to email these address. They are valid email addresses, but shouldn\'t be mailed in most cases.',
            static::STATUS_UNKNOWN     => 'These emails we weren\'t able to validate for one reason or another. Typical cases are "Their mail server was down" or "the anti-spam system is blocking us". In most cases, 80% unknowns are invalid/bad email addresses. We have the lowest "unknowns" of any email validator, and we don\'t make this statement lightly. We paid and tested email lists at over 50 different validation companies to compare results. If you do encounter a large number of unknowns, please submit those for re-validation. Remember you are not charged for unknown results, credits will be credited back. If you still have a large number, contact us and we will take a look and verify.',
        ];

        if (isset($statuses[$this->status])) {
            $description = $statuses[$this->status];
        }

        return $description;
    }

    /**
     * Substatus description.
     *
     * @return string
     * @see https://www.zerobounce.net/docs/email-validation-api-quickstart/v2-status-codes/ Validation API: Status Codes
     */
    public function subStatusDescription(): string
    {
        $description = '';

        $sub_statuses = [
            static::SUBSTATUS_ALIAS_ADDRESS               => 'These emails addresses act as forwarders/aliases and are not real inboxes, for example if you send an email to forward@example.com and then the email is forwarded to realinbox@example.com. It\'s a valid email address and you can send to them, it\'s just a little more information about the email address. We can sometimes detect alias email addresses and when we do we let you know.',
            static::SUBSTATUS_ANTISPAM_SYSTEM             => 'These emails have anti-spam systems deployed that are preventing us from validating these emails. You can submit these to us through the contact us screen to look into.',
            static::SUBSTATUS_DOES_NOT_ACCEPT_MAIL        => 'These domains only send mail and don\'t accept it.',
            static::SUBSTATUS_EXCEPTION_OCCURRED          => 'These emails caused an exception when validating. If this happens repeatedly, please let us know.',
            static::SUBSTATUS_FAILED_SMTP_CONNECTION      => 'These emails belong to a mail server that won\'t allow an SMTP connection. Most of the time, these emails will end up being invalid.',
            static::SUBSTATUS_FAILED_SYNTAX_CHECK         => 'Emails that fail RFC syntax protocols.',
            static::SUBSTATUS_FORCIBLE_DISCONNECT         => 'These emails belong to a mail server that disconnects immediately upon connecting. Most of the time, these emails will end up being invalid.',
            static::SUBSTATUS_GLOBAL_SUPPRESSION          => 'These emails are found in many popular global suppression lists (GSL), they consist of known ISP complainers, direct complainers, purchased addresses, domains that don\'t send mail, and known litigators.',
            static::SUBSTATUS_GREYLISTED                  => 'Emails where we are temporarily unable to validate them. A lot of times if you resubmit these emails they will validate on a second pass.',
            static::SUBSTATUS_LEADING_PERIOD_REMOVED      => 'If a valid gmail.com email address starts with a period ' . ' we will remove it, so the email address is compatible with all mailing systems.',
            static::SUBSTATUS_MAIL_SERVER_DID_NOT_RESPOND => 'These emails belong to a mail server that is not responding to mail commands. Most of the time, these emails will end up being invalid.',
            static::SUBSTATUS_MAIL_SERVER_TEMPORARY_ERROR => 'These emails belong to a mail server that is returning a temporary error. Most of the time, these emails will end up being invalid.',
            static::SUBSTATUS_MAILBOX_QUOTA_EXCEEDED      => 'These emails exceeded their space quota and are not accepting emails. These emails are marked invalid.',
            static::SUBSTATUS_MAILBOX_NOT_FOUND           => 'These emails addresses are valid in syntax, but do not exist. These emails are marked invalid.',
            static::SUBSTATUS_NO_DNS_ENTRIES              => 'These emails are valid in syntax, but the domain doesn\'t have any records in DNS or have incomplete DNS Records. Therefore, mail programs will be unable to or have difficulty sending to them. These emails are marked invalid.',
            static::SUBSTATUS_POSSIBLE_TRAP               => 'These emails contain keywords that might correlate to possible spam traps like spam@ or @spamtrap.com. Examine these before deciding to send emails to them or not.',
            static::SUBSTATUS_POSSIBLE_TYPO               => 'These are emails of commonly misspelled popular domains. These emails are marked invalid.',
            static::SUBSTATUS_ROLE_BASED                  => 'These emails belong to a position or a group of people, like sales@ info@ and contact@. Role-based emails have a strong correlation to people reporting mails sent to them as spam and abuse.',
            static::SUBSTATUS_TIMEOUT_EXCEEDED            => 'These emails belong to a mail server that is responding extremely slow. Most of the time, these emails will end up being invalid.',
            static::SUBSTATUS_UNROUTABLE_IP_ADDRESS       => 'These emails domains point to an un-routable IP address, these are marked invalid.',
            static::SUBSTATUS_DISPOSABLE                  => 'These are temporary emails created for the sole purpose to sign up to websites without giving their real email address. These emails are short lived from 15 minutes to around 6 months. There is only 2 values (True and False). If you have valid emails with this flag set to TRUE, you shouldn\'t email them.',
            static::SUBSTATUS_TOXIC                       => 'These email addresses are known to be abuse, spam, or bot created emails. If you have valid emails with this flag set to TRUE, you shouldn\'t email them.',
            static::SUBSTATUS_ROLE_BASED_CATCH_ALL        => 'These emails are role-based and also belong to a catch_all domain.',
        ];

        if (isset($sub_statuses[$this->sub_status])) {
            $description = $sub_statuses[$this->sub_status];
        }

        return $description;
    }
}