<?php

namespace docono\CommunicationBundle\Services;

class Slack {
    private $webhook;


    /**
     * Slack constructor
     */
    public function __construct() {}

    public function setWebhook($webhook) : Slack {
    	$this->webhook = $webhook;

    	return $this;
    }

    public function submitMessage(String $channel, String $botName, String $message, String $icon='', Array $attachments=[]) : bool {

	    $data = array(
		    'channel'     => $channel,
		    'username'    => $botName,
		    'text'        => $message,
		    'icon'        => $icon,
		    'attachments' => $attachments
	    );

	    $dataString = json_encode($data);

	    $ch = curl_init($this->webhook);
	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			    'Content-Type: application/json',
			    'Content-Length: ' . strlen($dataString))
	    );

	    //Execute CURL
	    $result = curl_exec($ch);
	    curl_close($ch);

	    return $result;
    }
}