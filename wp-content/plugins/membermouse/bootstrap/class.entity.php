<?php
/**
 * 
 * MemberMouse(TM) (http://www.membermouse.com)
 * (c) MemberMouse, LLC. All rights reserved.
 */
abstract class MM_Entity
{
	protected $id = 0;
	private $valid = false;
	private $authKey = "";
	protected $notifyServices = true;
	
	public function __construct($id="", $getData=true) 
 	{
 		if(!($id instanceof MM_Response))
 		{
	 		if(isset($id) && intval($id) > 0)
	 		{
	 			$this->id = $id;
	 			
	 			if($getData == true) 
	 			{
	 				$this->getData();
	 			}
	 		}
	 		else 
	 		{
	 			$id = "";
	 		}
 		}
 	}
 	
	abstract protected function getData();
	abstract public function setData($data);
	abstract protected function commitData();
 	
	public function setId($str)
 	{
 		$this->id = $str;
 	}
 	
	public function getId()
 	{
 		return $this->id;
 	}
	
	public function validate()
	{
		$this->valid = true;
		if ($this->id > 0)
		{
			$className = get_class($this);
			
			if (class_exists("MM_ObjectCache"))
			{
				MM_ObjectCache::set("{$className}_{$this->id}",$this);
			}
		}
	}
	
	public function invalidate() 
	{
		$this->valid = false;	
	}
	
	public function isValid()
	{
		return $this->valid;
	}
	
	public function getAuthKey()
	{
		return $this->authKey;
	}
	
	public function setAuthKey($str)
	{
		$this->authKey = $str;
	}
	
	public function notifiesServices($doServiceNotification)
	{
		if (is_bool($doServiceNotification))
		{
			$this->notifyServices = $doServiceNotification;
		}
	}
	
	
	/**
	 * Factory method, checks the object cache for the requested object and returns it if it exists, otherwise returns a new instance of the class
	 * 
	 * @param int $id The id of the requested entity
	 *  
	 * @return object The requested entity, or an invalid entity if the id was not found
	 */
	public static function create($id)
	{
		$className = get_called_class();
		if ($id > 0)
		{
			$cacheHit = MM_ObjectCache::get("{$className}_{$id}");
			if (($cacheHit != null) && ($cacheHit instanceof $className))
			{
				//error_log("Cache hit on {$className} id {$id}");
				return $cacheHit;
			}
		}
		return new $className($id);
	}
}
?>
