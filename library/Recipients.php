<?php

class Recipients
{
	private $master;
	
	public function __construct(SendmachineApiClient $master)
	{
		$this->master = $master;
	}

	/**
	 * 
	 * @param string $email
	 * @return array
	 */
	public function get($email)
	{
		return $this->master->request('/recipient/' . $email, 'GET');
	}
}
