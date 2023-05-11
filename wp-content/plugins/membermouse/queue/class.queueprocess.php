<?php
class MM_QueueProcess extends MM_Background 
{ 
    /**
     * Override runTask and define what to look for and how to process.
     * 
     * @return MM_Queue status 
     */
    const MM_QUEUE_MAIN = "mm-queue";
    protected function runTask( $item ) 
    {   
        if(isset($item["type"]))
        {
            $async = new MM_AsyncTaskManager();
            $type = intval($item["type"]);
            switch($type)
            {
                case MM_AsyncTaskManager::PUSH_NOTIFICATION:  
                    $async->handlePushNotificationDispatch($item);
                    break;
                case MM_AsyncTaskManager::PAYMENT_SERVICE_MEDIATOR_EVENT:   
                    $async->handlePaymentServiceMediatorEvent($item);
                    break;
                case MM_AsyncTaskManager::WORDPRESS_ACTION:   
                    $async->handleAsyncWordpressAction($item);
                    break;
            }
        }
        return MM_Queue::MM_STATUS_COMPLETE;
    }  
}