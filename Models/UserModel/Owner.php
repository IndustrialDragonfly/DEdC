<?php

class Owner
{
	private $userName;
	private $organization;
	
	public function __construct($userName, $organization)
	{
		$this->userName = $userName;
		$this->organization = $organization;
	}
	
	public function getUserName()
	{
		return $this->userName;
	}
	
	public function getOrganization()
	{
		return $this->organization;
	}
	
	public function authorize($user)
	{
		if ($user->getOrganization() == $this->organization)
		{
			return true;
		}
		
		return false;
	}
	
	public function getAssociativeArray()
	{
		return $array = array(
		    "userName" => $this->userName,
		    "organization" => $this->organization,
		);
	}
}