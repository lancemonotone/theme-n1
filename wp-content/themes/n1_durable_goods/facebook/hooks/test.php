<?php
define('SDK_DIR', __DIR__ . '/..'); // Path to the SDK directory
$loader = include SDK_DIR . '/vendor/autoload.php';

use FacebookAds\Api;
use FacebookAds\Logger\CurlLogger;
use FacebookAds\Object\ServerSide\Content;
use FacebookAds\Object\ServerSide\CustomData;
use FacebookAds\Object\ServerSide\DeliveryCategory;
use FacebookAds\Object\ServerSide\Event;
use FacebookAds\Object\ServerSide\EventRequest;
use FacebookAds\Object\ServerSide\Gender;
use FacebookAds\Object\ServerSide\UserData;

// Configuration.
// Should fill in value before running this script
$access_token = 'EAAIDqtKEn48BAPLlmmoHAtgSW9WVTv6z8anRV2wCZC30UkXtvGOijenASZAzloFVAfDWBpZC6LifYJrSLFU8atu3Jty2XaPiFHR1USEfy2R7pXYSowNR9oiBmZC7Fq46YcqBArPx9BtFb6gQXMgEZAaLp3q1t38yTM5jgpdqXRs3JrMwa0UVnPsZCiCD22mZCgZD';
$pixel_id = '1708719166025057';

if (is_null($access_token) || is_null($pixel_id)) {
    throw new Exception(
        'You must set your access token and pixel id before executing'
    );
}

// Initialize
Api::init(null, null, $access_token);
$api = Api::instance();
$api->setLogger(new CurlLogger());

$events = array();

$user_data_0 = (new UserData())
    ->setEmails(array("7b17fb0bd173f625b58636fb796407c22b3d16fc78302d79f0fd30c2fc2fc068"))
    ->setPhones(array());

$custom_data_0 = (new CustomData())
    ->setValue(142.52)
    ->setCurrency("USD");

$event_0 = (new Event())
    ->setEventName("Purchase")
    ->setEventTime(1629490051)
    ->setUserData($user_data_0)
    ->setCustomData($custom_data_0)
    ->setActionSource("email");
array_push($events, $event_0);

$request = (new EventRequest($pixel_id))
    ->setEvents($events);

$request->execute();