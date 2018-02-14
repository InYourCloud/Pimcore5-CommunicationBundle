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

class Facebook {

	private $appId = null;
	private $appSecret = null;
	private $token = null;

	private $fb = null;

	/**
	 * @param int $appId
	 * @return Facebook
	 */
	public function setAppId(int $appId)
	{
		$this->appId = $appId;

		return $this;
	}

	/**
	 * @param string $appSecret
	 * @return Facebook
	 */
	public function setAppSecret(string $appSecret)
	{
		$this->appSecret = $appSecret;

		return $this;
	}

	/**
	 * @param string $token
	 * @return Facebook
	 */
	public function setToken(string $token)
	{
		$this->token = $token;

		return $this;
	}

	/**
	 * @param int $appId
	 * @param string $appSecret
	 * @param string $token
	 * @return Facebook
	 */
	public function init(int $appId, string $appSecret, string $token) {
		$this->appId = $appId;
		$this->appSecret = $appSecret;
		$this->token = $token;

		return $this;
	}

	/**
	 * @return \Facebook\Facebook|null
	 * @throws \Facebook\Exceptions\FacebookSDKException
	 */
	public function getFb() {
		if(!$this->fb) {
			$this->fb =  new \Facebook\Facebook([
				'app_id' => $this->appId,
				'app_secret' => $this->appSecret
			]);
		}

		return $this->fb;
	}

	/**
	 * @param string $endpoint
	 * @param array $data
	 * @return false |\Facebook\GraphNodes\GraphNode
	 * @throws \Facebook\Exceptions\FacebookSDKException
	 */
	public function post(string $endpoint, array $data) {
		try {
			$response = $this->getFb()->post($endpoint, $data, $this->token);
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
			Simple::log('docono_communication_facebook', 'Graph returned an error: ' . $e->getMessage());
			return false;
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
			Simple::log('docono_communication_facebook', 'Facebook SDK returned an error: ' . $e->getMessage());
			return false;
		}

		return $response->getGraphNode();
	}
}