<?php

class MM_Mutex
{
    private $lockName; 

    /**
     * Instantiate a new mutex object to handle your lock requirements.
     * 
     * $mutex = new MM_Mutex($key, $uniquePrefix);
     * if($mutex->obtainLock())
     * {
     *      //// do work
     *      $mutex->releaseLock();
     * }
     * 
     * Check if another lock is available:
     * 
     * if(!$mutex->isLocked())
     * {
     *      /// obtain new lock or ...
     * }
     * 
     * @param $lockName string is the name of the lock
     * @param $prefix string is the name of the unique prefix for mysql server.  Typically can be $wpdb->dbname to ensure 'unqiueness' amongst multiple databases.
     */
    public function __construct($lockName, $prefix = null)
    {
        $this->lockName = self::prepareLockName($lockName, $prefix);
    }

    /**
     * Utility function to ensure lock names do not violate the 64 character limit
     * imposed by mysql.  This is set as static as to assist with migration of existing code over to leveraging this object.
     * 
     * @param $name string is the name of the lock
     * @param $prefix string is the name of the unique prefix for mysql server.  Typically can be $wpdb->dbname to ensure 'unqiueness' amongst multiple databases.
     * @param $shouldMd5Prefix bool if true md5 the prefix before using.
     */
    public static function prepareLockName($name, $prefix = null, $shouldMd5Prefix = false)
    {  
        if(!is_null($prefix))
        {
            $prefix = ($shouldMd5Prefix)?md5($prefix):$prefix;
            $name = $prefix."_".$name;
        }

        if(strlen($name)>64)
        {
            return substr($name,0,63);
        } 

        return $name;
    }


    /**
     * Allo new name to be set for instantiated object (ease of use)
     * 
     * @param $lockName string is the name of the lock
     * @param $prefix string is the name of the unique prefix for mysql server.  Typically can be $wpdb->dbname to ensure 'unqiueness' amongst multiple databases.
     */
    public function setName($lockName, $prefix = null)
    {
        $this->lockName = self::prepareLockName($lockName, $prefix);
    }


    /**
     * acquire a lock based on a given class lockName
     * 
     * @param $timeout int number of seconds until lock times out (Optional)
     * @return true if mysql has obtained lock for given key, otherwise false.
     */
    public function acquireLock($timeout = 10)
    { 
        global $wpdb;
        $locked = $wpdb->get_var("SELECT COALESCE(GET_LOCK('{$this->lockName}',{$timeout}),0)");
        return ($locked=="1")?true:false;
    }


    /**
     * @return true if mysql has lock for given key, otherwise false.
     */
    public function isLocked()
    {
        global $wpdb; 
        $locked = $wpdb->get_var($wpdb->prepare("SELECT IF(IS_FREE_LOCK(%s),COALESCE(GET_LOCK(%s,0),0),0)",$this->lockName,$this->lockName));
        if ($locked != "1")
        {
            return true;
        }
        return false;
    }

    /**
     * relelase lock based on class lockName.
     */
    public function releaseLock()
    {
        global $wpdb;
        $wpdb->query("SELECT RELEASE_LOCK('{$this->lockName}')"); 
    }
}