<?php

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\TwiML\Voice;

use Twilio\TwiML\TwiML;

class Start extends TwiML {
    /**
     * Start constructor.
     *
     * @param array $attributes Optional attributes
     */
    public function __construct($attributes = []) {
        parent::__construct('Start', null, $attributes);
    }

    /**
     * Add Stream child.
     *
     * @param array $attributes Optional attributes
     * @return Stream Child element.
     */
    public function stream($attributes = []): Stream {
        return $this->nest(new Stream($attributes));
    }

    /**
     * Add Siprec child.
     *
     * @param array $attributes Optional attributes
     * @return Siprec Child element.
     */
    public function siprec($attributes = []): Siprec {
        return $this->nest(new Siprec($attributes));
    }

    /**
     * Add Transcription child.
     *
     * @param array $attributes Optional attributes
     * @return Transcription Child element.
     */
    public function transcription($attributes = []): Transcription {
        return $this->nest(new Transcription($attributes));
    }

    /**
     * Add Action attribute.
     *
     * @param string $action Action URL
     */
    public function setAction($action): self {
        return $this->setAttribute('action', $action);
    }

    /**
     * Add Method attribute.
     *
     * @param string $method Action URL method
     */
    public function setMethod($method): self {
        return $this->setAttribute('method', $method);
    }
}