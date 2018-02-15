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
	/**
	 * @var string
	 */
	private $customerKey = null;

	/**
	 * @var string
	 */
	private $customerSecret = null;

	/**
	 * @var string
	 */
	private $oAuthToken = null;

	/**
	 * @var string
	 */
	private $oAuthSecret = null;

	private $settings = [];

	/**
	 * @var \TwitterAPIExchange
	 */
	private $twitter = null;

	/**
	 * api url to get user tweets
	 * @var string
	 */
	private $urlUserTimeline = 'https://api.twitter.com/1.1/statuses/user_timeline.json';

	private $urlPostTweet = 'https://api.twitter.com/1.1/statuses/update.json';

	/**
	 * @param null $customerKey
	 * @return Twitter
	 */
	public function setCustomerKey($customerKey) : Twitter {
		$this->customerKey = $customerKey;

		return $this;
	}

	/**
	 * @param null $customerSecret
	 * @return Twitter
	 */
	public function setCustomerSecret($customerSecret) : Twitter {
		$this->customerSecret = $customerSecret;

		return $this;
	}

	/**
	 * @param null $oAuthToken
	 * @return Twitter
	 */
	public function setOAuthToken($oAuthToken) : Twitter {
		$this->oAuthToken = $oAuthToken;

		return $this;
	}

	/**
	 * @param null $oAuthSecret
	 * @return Twitter
	 */
	public function setOAuthSecret($oAuthSecret) : Twitter {
		$this->oAuthSecret = $oAuthSecret;
		return $this;

	}

	/**
	 * @param array $settings
	 * @return Twitter
	 */
	public function setSettings(array $settings) : Twitter {
		$this->settings = $settings;

		return $this;
	}

	private function getSettings() : array {
		return $this->settings + [
				'oauth_access_token' => $this->oAuthToken,
				'oauth_access_token_secret' => $this->oAuthSecret,
				'consumer_key' => $this->customerKey,
				'consumer_secret' => $this->customerSecret
			];
	}

	public function getAPI() : \TwitterAPIExchange {
		if(!$this->twitter) {
			$this->twitter = new \TwitterAPIExchange($this->getSettings());

			if(!$this->twitter)
				throw new \Exception('failed to init TwitterAPIExchange');
		}

		return $this->twitter;
	}

	/**
	 * @param string $userName
	 * @return \stdClass | array | false
	 * @throws \Exception
	 */
	public function getUserTweets(string $userName) {
		try {
			$response = $this->getAPI()->setGetfield('?screen_name=' . $userName)
				->buildOauth($this->urlUserTimeline, 'GET')
				->performRequest();
		} catch(\Exception $e) {
			Simple::log('docono_communication_twitter', $e->getMessage());
			return false;
		}

		return json_decode($response);
	}

	/**
	 * @param string $description
	 * @param string $status
	 * @return \stdClass | false
	 * @throws \Exception
	 */
	public function postTweet(string $description, string $status) {
		try {
			$response = $this->getAPI()->setPostfields(['description' => $description, 'status' => $status])
				->buildOauth($this->urlPostTweet, 'POST')
				->performRequest();
		} catch(\Exception $e) {
			Simple::log('docono_communication_twitter', $e->getMessage());
			return false;
		}


		return json_decode($response);
	}
}