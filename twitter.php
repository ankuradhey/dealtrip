<?php
    $token = '1898087952-hqF8rv2wpsn1JPFXIwYa9wnTf3UaX2ocmAByorX';
    $token_secret = 'iceYKt5TMcFt1hjObNll2xW0IMLYo1aH2KLgMpiLrubTi';
    $consumer_key = 'ux95v4OxuGpUzR11Fo9fw';
    $consumer_secret = 'CtU63LsMlXcehnUiix0LhDsf4Mpo1ysjfP9lsGPRs';

    $host = 'api.twitter.com';
    $method = 'GET';
    $path = '/1.1/statuses/user_timeline.json'; // api call path

    $screen_name = "dealatrip";
    $tweet_count = "6";

    $query = array(// query parameters
        'screen_name' => $screen_name,
        'count' => $tweet_count
    );

    $oauth = array(
        'oauth_consumer_key' => $consumer_key,
        'oauth_token' => $token,
        'oauth_nonce' => (string) mt_rand(), // a stronger nonce is recommended
        'oauth_timestamp' => time(),
        'oauth_signature_method' => 'HMAC-SHA1',
        'oauth_version' => '1.0'
    );

    $oauth = array_map("rawurlencode", $oauth); // must be encoded before sorting
    $query = array_map("rawurlencode", $query);

    $arr = array_merge($oauth, $query); // combine the values THEN sort

    asort($arr); // secondary sort (value)
    ksort($arr); // primary sort (key)
// http_build_query automatically encodes, but our parameters
// are already encoded, and must be by this point, so we undo
// the encoding step
    $querystring = urldecode(http_build_query($arr, '', '&'));

    $url = "https://$host$path";

// mash everything together for the text to hash
    $base_string = $method . "&" . rawurlencode($url) . "&" . rawurlencode($querystring);

// same with the key
    $key = rawurlencode($consumer_secret) . "&" . rawurlencode($token_secret);

// generate the hash
    $signature = rawurlencode(base64_encode(hash_hmac('sha1', $base_string, $key, true)));

// this time we're using a normal GET query, and we're only encoding the query params
// (without the oauth params)
    $url .= "?" . http_build_query($query);
    $url = str_replace("&amp;", "&", $url); //Patch by @Frewuill

    $oauth['oauth_signature'] = $signature; // don't want to abandon all that work!
    ksort($oauth); // probably not necessary, but twitter's demo does it
// also not necessary, but twitter's demo does this too

    function add_quotes($str)
    {
        return '"' . $str . '"';
    }

    $oauth = array_map("add_quotes", $oauth);

// this is the full value of the Authorization line
    $auth = "OAuth " . urldecode(http_build_query($oauth, '', ', '));

// if you're doing post, you need to skip the GET building above
// and instead supply query parameters to CURLOPT_POSTFIELDS
    $options = array(CURLOPT_HTTPHEADER => array("Authorization: $auth"),
        //CURLOPT_POSTFIELDS => $postfields,
        CURLOPT_HEADER => false,
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false);

// do our business
    $feed = curl_init();
    curl_setopt_array($feed, $options);
    $json = curl_exec($feed);

//print_r($json); die;
    curl_close($feed);

    $twitter_data = (array) json_decode($json);

    $i = 1;
    foreach ($twitter_data as $tval)
    {
        $noborder_class = "";
        if ($i == $tweet_count)
            $noborder_class = " noborder";
        ?>
        <div class="left_facebook_clmn <?php echo $noborder_class; ?>">
            <div class="floatL"><img src="<?php echo $tval->user->profile_image_url; ?>" width="50" alt="twitter user"/></div>
            <div class="left_facebook_dis_fb"><?php echo substr(twitterify($tval->text), 0, 150); ?> <a href="http://twitter.com/<?php echo $screen_name; ?>" class="link" target="_blank">read more...</a></div>
        </div>
        <?php
        $i++;
    }
?>
