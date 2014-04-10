<?php
/**
 *  TinyPNG API v1
 *
 *  Michael Wright - @michaelw90
 *  Copyright (c) 2013, Fabian Schlieper
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *  * Redistributions of source code must retain the above copyright
 * notice, this list of conditions and the following disclaimer.
 *  * Redistributions in binary form must reproduce the above copyright
 * notice, this list of conditions and the following disclaimer in the
 * documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY UNITEDHEROES.NET ''AS IS'' AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL UNITEDHEROES.NET BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */


class TinyPNG
{

    private $url = 'https://api.tinypng.com/shrink';
    private $curl = null;
    private $lastResult = null;

    /**
     * Constructor
     * @param strong $key API key for all requests
     */
    public function __construct($key)
    {
        if ($this->curl === null) {
            $this->curl = curl_init();
            $curlOpts = array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => $this->url,
                CURLOPT_USERAGENT => 'TinyPNG PHP API v1',
                CURLOPT_POST => 1,
                CURLOPT_USERPWD => 'api:' . $key,
                CURLOPT_BINARYTRANSFER => 1
            );
            curl_setopt_array($this->curl, $curlOpts);
        }
    }

    /**
     * Send image shrink request
     * @param  string $file path to file to shrink
     * @return boolean|exception       Is HTTP response 200
     */
    public function shrink($file)
    {
        if (file_exists($file) === false) {
            throw new Exception('File does not exist');
        }
        curl_setopt($this->getCurl(), CURLOPT_POSTFIELDS, file_get_contents($file));
        $this->lastResult = curl_exec($this->getCurl());
        return curl_getinfo($this->getCurl(), CURLINFO_HTTP_CODE) === 201;
    }

    /**
     * Return API response object
     * @return object|exception
     */
    public function getResult() {
        return $this->_getResult();
    }

    /**
     * Return API response as JSON
     * @return string|exception
     */
    public function getResultJson() {
        return json_decode($this->_getResult());
    }

    /**
     * Return API response object
     * @return object|exception
     */
    protected function _getResult()
    {
        if ($this->lastResult === null) {
            throw new Exception('No current result');
        }
        return $this->lastResult;
    }

    /**
     * Return Curl object
     * @return object|exception
     */
    protected function getCurl()
    {
        if ($this->curl === null) {
            throw new Exception('cURL not yet initialized.');
        }

        return $this->curl;
    }
}