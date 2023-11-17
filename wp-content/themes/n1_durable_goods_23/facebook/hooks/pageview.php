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
$access_token = 'EAAIDqtKEn48BAAGT6sV0BIM03tqJL1xAjZCZBRtZByuBaGZATLVLMhD8gf5d3oQUevoLMzFbdAAWqbS3ZA8kIrWx80ZCSehEswm0ehQZAgocJbZBZASStkB5ybPAW6gj2GLvsX3MoeYLQIQG9aTseJVrIzXBXfnZAHjSQYr7iY3IVDQP7Tk0MyCgtTUcDlyCxnUewZD';
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
    ->setPhones(array("721fe13c4a6b840bbebe67761a07f104c203beb7b442a3027393b134b797bfbf"))
    ->setLastNames(array("9f542590100424c92a6ae40860f7017ac5dfbcff3cb49b36eace29b068e0d8e1"))
    ->setFirstNames(array("6dd8b7d7d3c5c4689b33e51b9f10bc6a9be89fe8fa2a127c8c6c03cd05d68ace"))
    ->setCities(array("ba5b3cf69bccb23478b319ee3dd25dfaa629ee25b178b40174c80a831993d3c8"))
    ->setStates(array("4b650e5c4785025dee7bd65e3c5c527356717d7a1c0bfef5b4ada8ca1e9cbe17"))
    ->setZipCodes(array("095a81b7afdcecdb68fa67624f09277ca58c15e4caafcbb585bd18ac496a4a3d"))
    ->setCountryCodes(array("9b202ecbc6d45c6d8901d989a918878397a3eb9d00e8f48022fc051b19d21a1d"));

$event_0 = (new Event())
    ->setEventName("PageView_NEW")
    ->setEventTime(1629853440)
    ->setUserData($user_data_0)
    ->setCustomData($custom_data_0)
    ->setActionSource("website")
    ->setEventSourceUrl("<here>")
    ->setEventId("<here>");
array_push($events, $event_0);

$request = (new EventRequest($pixel_id))
    ->setEvents($events);

$request->execute();
