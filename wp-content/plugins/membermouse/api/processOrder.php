<?php
require_once(dirname(__FILE__)."/../bootstrap/barebones.php");

$orderRequest = new MM_DefaultOrderRequest($_POST);
$orderRequest->processRequest();
$orderRequest->submitRequest();
?>