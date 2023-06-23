<?php
namespace membermouse\resque;

use \Exception;
use \MM_PaymentServiceFactory;
use \MM_PaymentServiceResponse;
use \MM_ScheduledEvent;
use \MM_ScheduledPaymentEvent;
use \MM_DiagnosticLog;

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class TestJob extends Job
{
    public function perform() 
    {
        return true;
    }
}

