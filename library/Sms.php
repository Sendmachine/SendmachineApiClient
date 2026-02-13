<?php

class Sms {
	private $master;
	
	public function __construct(SendmachineApiClient $master) {
		$this->master = $master;
	}

	/**
	 * send sms
	 * @param array $details
	 * @return array
	 * {
	 *    "sent"
	 *    "status"
	 * }
	 */
	public function send($details) {

		return $this->master->request('/sms/send', 'POST', $details);
	}

}