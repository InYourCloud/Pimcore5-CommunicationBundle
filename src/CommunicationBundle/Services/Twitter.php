<?php

/**
 * DOCONO | digitale Problemlöser
 *
 * @author Renzo Müller <renzo@docono.io>
 * @copyright  Copyright (c) DOCONO  (https://docono.io)
 * @since 1.0.0
 */

namespace CommunicationBundle\Services;

use Pimcore\Log\Simple;

class Twitter {
	private $customerKey = null;
	private $customerSecret = null;
	private $oAuthToken = null;
	private $oAuthSecret = null;

	private $requestType = null;
	private $endpoint = null;

	private $oAuth = null;
	private $header = null;



	public function buildOAuth(string $endpoint = 'https://api.twitter.com/1.1/search/tweets.json', $requestType = 'POST') {
		if(!$this->oAuth) {
			$this->endpoint = $endpoint;
			$this->requestType = $requestType;

			$this->oAuth = array('oauth_consumer_key' => $this->customerKey, 'oauth_nonce' => time(), 'oauth_signature_method' => 'HMAC-SHA1', 'oauth_token' => $this->oAuthToken, 'oauth_timestamp' => time(), 'oauth_version' => '1.0');

			$info = $this->requestType . '&' . rawurlencode($this->endpoint);
			$compositeKey = rawurlencode($this->customerSecret) . '&' . rawurlencode($this->oAuthSecret);
			$this->oAuth['oauth_signature'] = base64_encode(hash_hmac('sha1', $info, $compositeKey, true));
		}

		return $this;
	}

	public function process() {
		$options = $curlOptions + [
				CURLOPT_HTTPHEADER => [$this->getHeader(), 'Expect:'],
				CURLOPT_HEADER => false,
				CURLOPT_URL => $this->endpoint,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_TIMEOUT => 10,
			];

		$ch = curl_init();
		curl_setopt_array($ch, $options);
		$response = curl_exec($ch);

		if (($error = curl_error($ch)) !== '') {
			curl_close($feed);
			throw new Exception($error);
		}

		curl_close($ch);

		return json_decode($response);
	}

	protected function getHeader() {
		if(!$this->header) {
			$values = [];

			foreach($this->oAuth as $key => $value) {
				$value[] = $key . ' = "' . rawurlencode($value) . '"';
			}

			$this->header = 'Authorization: OAuth ' .  implode(', ', $values);
		}

		return $this->header;
	}
}