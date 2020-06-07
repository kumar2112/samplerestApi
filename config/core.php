<?php
error_reporting(E_ALL);

// set your default time-zone
date_default_timezone_set('Asia/Manila');

// variables used for jwt
$key = "kumar2112";
$iss = "http://localhost/rest-api";
$aud = "http://localhost/rest-api/";
$iat = 1356999524;
$nbf = 1357000000;
