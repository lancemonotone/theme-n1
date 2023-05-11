<?php

abstract class MM_Background
{
    ///// Pipeline identification, used to identify batches of queue items.
    private $id = null;

    //// This is the ajax key for the handler to start the queue/schedule - immediately
    private $actionKey = "mm_queue";

    //// This is the ajax key for the handler to start the queue/schedule - next cron iteration (1 min)
    private $actionKeySchedule = "mm_queue_schedule";
    private $mmQueueKey = null;

    /**
     * Setup the cron / healthcheck schedules
     * Setup the ajax handlers to run the queue (both scheduled and action now + schedule)
     * 
     * @id is the pipeline ID.
     */
    public function __construct($id)
    { 
        $this->logMe("start",__CLASS__.":".__FUNCTION__);
        $this->id = $id; 
        $this->mmQueueKey = $this->id."_mm_queue_start";
 
        add_action( 'wp_ajax_' . $this->actionKey, array( $this, 'doRunQueue' ) );
        add_action( 'wp_ajax_nopriv_' . $this->actionKey, array( $this, 'doRunQueue' ) );
        add_action( 'wp_ajax_' . $this->actionKeySchedule, array( $this, 'doScheduleQueue' ) );
        add_action( 'wp_ajax_nopriv_' . $this->actionKeySchedule, array( $this, 'doScheduleQueue' ) );

        add_action( $this->mmQueueKey, array( $this, 'cronHealthcheck' ) );
        add_filter( 'cron_schedules', array( $this, 'getHealthcheckConfig' ) ); 
    }

    /**
     * This function is called from the AsyncTaskManager to either
     * initiate the queue and the healthcheck cron job and run it OR
     * just initiate the cron job and await it's execution to start.
     * 
     * @$executeNow boolean to identify if you want to start processing queue immediately (if not already running).
     * @return MM_Response object for good measure.
     */
    public function dispatchAndReturnMMResponse($executeNow=true) 
    {  
        $this->doSetupSchedule(); 
        $data = ($executeNow)?$this->execute():$this->schedule();
        if($data===false)
        {
            return new MM_Response(0, MM_Status::$ERROR);
        }
        return new MM_Response(1);
    } 

    /**
     * Add item to mm-queue table
     */
    public function push($data)
    { 
        $q = new MM_Queue();
        $q->setPipeline($this->id);
        $q->setQueueData($data);
        $q->commitData();
    }
    
    public function doScheduleQueue() 
    {  
        session_write_close();  
        wp_die();
    }  
    
    /**
     * Initiate the queue mechansim if it isn't empty and no other process has started it.
     * Further, if there are no pending items check to see there aren't any subsequent orphaned
     * items that may have been left from a previous process.
     */
    public function doRunQueue() 
    {  
        session_write_close();  
        if(!$this->piplineIsStarted())
        {
            if ( $this->isEmpty() ) 
            {
                if(!MM_Queue::hasOrphanedQueueItems($this->id))
                {
                    wp_die();
                }
            }   
            $this->runQueue();    
        }
        wp_die();
    }  

    /**
     * Simple logging function for local debugging
     */
    
    protected function logMe($str, $ref=null)
    {
        // $referring = (!is_null($ref))?$ref:__CLASS__.":".__FUNCTION__;
        // file_put_contents(dirname(__FILE__)."/q.log", "[{$referring}][".Date("Y-m-d H:i:s") ."] ".$str."\n", FILE_APPEND);
    }
    
    /**
     * Relays an execute command that merely is used to 
     * initate the cron scheduler for queue processing within the next minute.
     */
    public function schedule()
    {    
        return $this->execute(true);
    }


    /**
     * 
     * @scheduleOnly flags whether we start the queue now or wait for the first
     * iteration of the cron job.
     * 
     * @return response from wp_remote_post
     */
    public function execute($scheduleOnly=false)
    {    
        $params = array(
            'cookies'   => $_COOKIE,
            'timeout'   => 0.02,
            'sslverify' => apply_filters( 'https_local_ssl_verify', false ),
            'blocking'  => false,
            'action' =>  (($scheduleOnly)?$this->actionKeySchedule:$this->actionKey),
            'nonce'  => wp_create_nonce( $this->actionKey )
        );
        $url = admin_url( 'admin-ajax.php' );
        $url  = add_query_arg( $params, $url ); 
        
        $response = wp_remote_post( esc_url_raw( $url ), $params ); 
        return $response;  
    }
   

    /**
     * This logs the start of a new pipeline queue processing.
     * 
     * @return start time of the process or false otherwise.
     */
    private function startTime()
    { 
        $queueStarted = get_option($this->mmQueueKey);
        if($queueStarted===false || $queueStarted=="-1")
        {
            $timeStart = time();
            add_option($this->mmQueueKey,$timeStart);
            return $timeStart;
        }
        return false;
    }

    /**
     * Convenience function to determine if there is a pipeline processing in the queue.
     * 
     * @return true if start time was found, otherwise false.
     */
    private function piplineIsStarted()
    {
        return (get_option($this->mmQueueKey) !== false && get_option($this->mmQueueKey) !== "-1"); 
    }

    /**
     * This will return the running time of the given queue.
     * 
     * @return int seconds if time is found, otherwise false.
     */
    private function getRunningTime()
    { 
        $queueStarted = get_option($this->mmQueueKey);
        if($queueStarted !== false && $queueStarted!="-1")
        { 
            return $queueStarted;
        }
        return false;
    }

    /**
     * Removes the time from options and cleansup completed queue items. 
     * Should also revert abandoned items to pending.
     */
    private function cleanup()
    { 
        $this->releaseLock(MM_TABLE_QUEUE);
        $this->endTime();
        MM_Queue::cleanup($this->id);
    }
    

    /**
     * Removes the time from options
     */
    private function endTime()
    {
        // update_option($this->mmQueueKey,"-1"); 
        delete_option($this->mmQueueKey); 
    }


    /**
     * This grabs the next batch of items from mm_queue and tries to process them in the object
     * that has extended (MM_QueueProcess).  
     * 
     * If time elapsed or memory usage has been surpassed will exit queue processing and cleanup() as needed. 
     */
    private function runQueue()
    {   
        if($this->getLock(MM_TABLE_QUEUE))
        {    
            $this->logMe("Run this queue ... {$this->id}",__CLASS__.":".__FUNCTION__);
            $this->startTime();
            $batch = MM_Queue::getBatch($this->id);
            if($batch===false)
            {
                $batch = MM_Queue::getAbandonedBatch($this->id);
            }
            $this->releaseLock(MM_TABLE_QUEUE);
            if($batch!==false && count($batch)>0)
            { 
                $this->logMe("Queue size ".count($batch)." ...",__CLASS__.":".__FUNCTION__);
                foreach($batch as $row)
                {  
                    $row->data = (is_serialized($row->data))?unserialize($row->data):$row->data; 
                    MM_Queue::changeStatus($row->id, $this->runTask($row->data)); 

                    if ( $this->exceededTimeThreshold() || $this->exceededMemoryThreshold() ) 
                    { 
                        break;
                    } 
                } 
            } 
            $this->cleanup();
			wp_die(); 
        }
    } 

    /**
     * Looks at memory_limit via php ini an tries to determine the amount allowed.
     * Then we compare that to current usage and return true if exceeds limitations.
     * 
     * @return true if current memory usage > limit.
     */
    private function exceededMemoryThreshold() 
    { 
        $memoryLimit = ini_get( 'memory_limit' );

        if ( is_null($memoryLimit) || $memoryLimit === false || intval( $memoryLimit ) <0 ) 
        { 
            $memoryLimit = 32000;
        }

        $memoryLimit =  ($memoryLimit * 1024 * 1024)* 0.9; 
        $current = memory_get_usage( true );  
        return ($current >= $memoryLimit);
    }  

    /**
     * Take the start time of current queue processing and compare +20 seconds.
     * 
     * @return true if time elapsed is > 20 seconds, false otherwise.
     */
    protected function exceededTimeThreshold() 
    { 
        $start = $this->getRunningTime();
        if($start===false)
        {
            return false;
        }
        $finish = $start+20; // 20 seconds running time
        return ( time() >= $finish ); 
    }
        
    /**
     * See if mm_queue is empty
     * 
     * @return true if empty,otherwise false.
     */
    private function isEmpty()
    {  
        return MM_Queue::getCount()<=0;
    } 

    /**
     * Release current lock for processing.
     */
    private function releaseLock($table)
    {
        global $wpdb;  
        $lockName = MM_Mutex::prepareLockName($table,$wpdb->dbname);
        $wpdb->query("SELECT RELEASE_LOCK('{$lockName}')"); 
    } 

    /**
     * Grab lock to avoid concurrent grabs.
     */
    private function getLock($table)
    {
        global $wpdb;  
        $lockName = MM_Mutex::prepareLockName($table,$wpdb->dbname);
        $lockAcquired = $wpdb->get_var("SELECT COALESCE(GET_LOCK('{$lockName}',10),0)");
        return $lockAcquired == 1;
    }
 
    /**
     * This is specifically used to setup the health check cron.  We define
     * a new interval that runs every minute.
     * 
     * @return array with cron description.
     */
    public function getHealthcheckConfig( $schedules ) 
    { 
        $this->logMe("",__CLASS__.":".__FUNCTION__);
        // every 1 min
        // $cron = array();
        $schedules[ $this->id . '_cron_interval' ] = array(
            'interval' => 60,
            'display'  => _( 'Every minute' ),
        ); 
        return $schedules;
    }


    /**
     * Check to see if there is more to do in the queue, the queue isn't running, the queue isn't empty, and 
     * if all apply, invoke the queue again. 
     */
    public function cronHealthcheck() 
    { 
        $this->logMe("",__CLASS__.":".__FUNCTION__);
        if ( $this->piplineIsStarted() ) 
        {
            $this->logMe("Leave healthcheck alone, pipeline being executed.",__CLASS__.":".__FUNCTION__);
            exit;    
        }

        if ( $this->isEmpty() ) 
        {
            $this->logMe("There is nothing left to do, clear healthcheck.",__CLASS__.":".__FUNCTION__);
            $this->clearCronItem();
            exit;
        }

        $this->logMe("Healthcheck finds some things to complete, lets get going.",__CLASS__.":".__FUNCTION__);
        $this->runQueue(); 
        exit;
    }

    /**
     * Setup the cron schedule with interval defined in getHealthcheckConfig()
     */
    protected function doSetupSchedule() 
    {
        $this->logMe("start",__CLASS__.":".__FUNCTION__);
        if ( ! wp_next_scheduled( $this->mmQueueKey ) ) 
        { 
            $this->logMe($this->id . '_cron_interval',__CLASS__.":".__FUNCTION__);
            $ret = wp_schedule_event( time(), $this->id . '_cron_interval', $this->mmQueueKey );
            if(!$ret)
            {
                $this->logMe($this->id . '_cron_interval could not schedule event',__CLASS__.":".__FUNCTION__);
            }
        }
    } 
    
    /**
     * Clear cron when nothing left to process.
     */
    protected function clearCronItem()
    {
        $this->logMe("start",__CLASS__.":".__FUNCTION__);
        $timestamp = wp_next_scheduled( $this->mmQueueKey );

        if ( $timestamp ) {
            $this->logMe("",__CLASS__.":".__FUNCTION__);
            wp_unschedule_event( $timestamp, $this->mmQueueKey );
        }
    }

    /**
     * Cancel cron job (utility function)
     */
    public function cancelCron() 
    {
        $this->logMe("start",__CLASS__.":".__FUNCTION__);
        if ( ! $this->isEmpty() ) 
        {  
            $this->logMe("",__CLASS__.":".__FUNCTION__);
            MM_Queue::removeByPipeline($this->mmQueueKey);
            wp_clear_scheduled_hook( $this->mmQueueKey );
        } 
    }

    /** Object that extends this one will need to define what to do with the task. */
    abstract protected function runTask($task);   
}