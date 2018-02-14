<?php
/**
 * DOCONO | digitale Problemlöser
 *
 * @author Renzo Müller <renzo@docono.io>
 * @copyright  Copyright (c) DOCONO  (https://docono.io)
 * @since 1.0.0
 */

namespace CommunicationBundle\Services;

class Gitlab {
    private $token = null;
    private $user = null;
	private $repo = null;


    /**
     * Gitlab constructor
     */
    public function __construct() {}

	/**
	 * set GitLab token
	 * @param string $token
	 * @return Gitlab
	 */
    public function setToken(string $token) : Gitlab {
    	$this->token = $token;

    	return $this;
    }

	/**
	 * set GitLab user
	 * @param string $user
	 * @return Gitlab
	 */
    public function setUser(string $user): Gitlab {
    	$this->user = $user;

    	return $this;
    }

	/**
	 * set repo
	 * @param string $repo
	 * @return Gitlab
	 */
    public function setRepo(string $repo): Gitlab {
    	$this->repo = $repo;

    	return $this;
    }

	/**
	 * init GitLab connection
	 * @param string $token
	 * @param string $user
	 * @param string $repo
	 * @return Gitlab
	 */
    public function init(string $token, string $user, string $repo): Gitlab {
    	$this->token = $token;
    	$this->user = $user;
    	$this->repo = $repo;

    	return $this;
    }

	/**
	 * get GitLab token
	 * @throws \Exception
	 * @return string
	 */
    protected function getToken() {
    	if(!$this->token)
    		throw new Exception('No GitLab Token is set.');

    	return $this->token;
    }

    public function getUser() : string {
	    if(!$this->user)
		    throw new Exception('No GitLab user is set.');

    	return $this->user;
    }

    public function getRepo() : string {
	    if(!$this->repo)
		    throw new Exception('No GitLab repo is set.');

    	return $this->repo;
    }

    public function getLastCommit() {

	    $ch = curl_init();

	    curl_setopt($ch, CURLOPT_URL, 'https://gitlab.com/' . $this->getUser() . '/' . $this->getRepo() . '/commits');

	    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , 'PRIVATE-TOKEN: ' . $this->getToken()));
	    //Set CURLOPT_RETURNTRANSFER so that the content is returned as a variable.
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		//Set CURLOPT_FOLLOWLOCATION to true to follow redirects.
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	    $result = curl_exec($ch);

	    var_dump(curl_error($ch));

	    var_dump($result);

	    curl_close($ch);

    }

}