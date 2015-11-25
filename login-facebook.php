<?php

require 'facebook.php';

$facebook = new Facebook(array(
            'appId' => '396402080392725',
            'secret' => '16ca20dc667256d610f47a4047961fa6',
            ));

$user = $facebook->getUser();

if ($user) {
	$logoutUrl=$facebook->getLogoutUrl();
  try {
    // Proceed knowing you have a logged in user who's authenticated.
    $user_profile = $facebook->api('/me');
  } catch (FacebookApiException $e) {
    error_log($e);
    $user = null;
  }






    if (!empty($user_profile)) {
	echo "<pre>";print_r($user_profile);exit();
	} else {
        # For testing purposes, if there was an error, let's kill the script
        die("There was an error.");
    }
} else {
    # There's no active session, let's generate one
	$loginUrl = $facebook->getLoginUrl(array( 'scope' => 'email'));
    header("Location: " . $loginUrl);
}
?>
