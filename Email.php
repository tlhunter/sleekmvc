<?php
namespace Sleek;

/**
 * Class for building and sending emails. Works with text and HTML emails.
 * @todo Add file attachment capability
 */
class Email {
    /**
     * @var string
     */
    protected $recipient    = '';

    /**
     * @var string
     */
    protected $subject      = '';

    /**
     * @var string
     */
    protected $body         = '';

    /**
     * @var string
     */
    protected $sender       = '';

    /**
     * @var string
     */
    protected $lastError    = '';

    /**
     * @var bool
     */
    protected $html         = \FALSE;

    /**
     * @param string $recipient
     * @param string $subject
     * @param string $body
     * @param string $sender
     */
    public function __construct($recipient = NULL, $subject = NULL, $body = NULL, $sender = NULL) {
        if ($recipient) {
            $this->setRecipient($recipient);
        }
        if ($subject) {
            $this->setSubject($subject);
        }
        if ($body) {
            $this->setBody($body);
        }
        if ($sender) {
            $this->setSender($sender);
        }
    }

    /**
     * @param string $recipient
     * @return Email
     */
    public function setRecipient($recipient) {
        $recipient = trim($recipient);
        if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            $this->recipient = NULL;
            $this->lastError = "Invalid Email set as Recipient";
        } else {
            $this->recipient = $recipient;
        }
        return $this;
    }

    /**
     * @param string $subject
     * @return Email
     */
    public function setSubject($subject) {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @param string $body
     * @return Email
     */
    public function setBody($body) {
        $this->body = $body;
        return $this;
    }

    /**
     * @return Email
     */
    public function setTypeHtml() {
        $this->html = TRUE;
        return $this;
    }

    /**
     * @return Email
     */
    public function setTypeText() {
        $this->html = FALSE;
        return $this;
    }

    /**
     * @param string $sender
     * @return Email
     */
    public function setSender($sender) {
        $sender = trim($sender);
        if (!filter_var($sender, FILTER_VALIDATE_EMAIL)) {
            $this->sender = NULL;
            $this->lastError = "Invalid Email set as Sender";
        } else {
            $this->sender = $sender;
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getLastError() {
        return $this->lastError;
    }

    /**
     * @return bool
     */
    public function send() {
        if ($this->recipient && $this->subject && $this->body && $this->sender) {
            $headers = "From: {$this->sender}";
            if ($this->html) {
                $headers .= "\r\nContent-type: text/html";
            }
            return mail($this->recipient, $this->subject, $this->body, $headers);
        }
        return FALSE;
    }

    /**
     * @param string $filename
     * @return Email
     */
    public function attachFile($filename) {
        // TODO: Handle file attachments
        return $this;
    }

    /**
     * @param string $filename
     * @param mixed $content
     * @return Email
     */
    public function attachFileFromString($filename, $content) {
        // TODO: Handle fiel attachments
        return $this;
    }

    /**
     * @return Email
     */
    public function debug() {
        echo "<pre>";
        var_dump($this);
        echo "</pre>";
        return $this;
    }

}
