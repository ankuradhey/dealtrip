<?php

//created and developed by tech biz sol
    $_CONFIG['environment'] = 'development';

    
    if($_SERVER['REMOTE_ADDR'] == '127.0.0.1')
    define("APPLICATION_URL", "http://localhost/work/dealatrip/");
    else
        define("APPLICATION_URL", "http://www.dealatrip.co.uk/");

    //define('APPLICATION_URL', "http://www.dealatrip.co.uk/");
    define('APPLICATION_URL_ADMIN', APPLICATION_URL . "admin/");

    define('IMAGES_URL', APPLICATION_URL . 'images/');
    define('CSS_URL', APPLICATION_URL . 'css/');
    define('JS_URL', APPLICATION_URL . 'js/');

    define('IMAGES_URL_ADMIN', APPLICATION_URL_ADMIN . 'images/');
    define('CSS_URL_ADMIN', APPLICATION_URL_ADMIN . 'css/');
    define('JS_URL_ADMIN', APPLICATION_URL_ADMIN . 'js/');
    $_CONFIG['homeDir'] = realpath(dirname(dirname(dirname(__FILE__)))) . '/';
    define('SITE_ROOT', $_CONFIG['homeDir']);
    define('DATEFORMAT', 'D d M, Y');

    /* database.params.dbname = "tbs11co_toddlez"
      database.params.host = "localhost"
      database.params.username = "tbs11co_toddlez"
      database.params.password = "g.g%e5#iX^NA" */
//bathroom and bedroom id

    if ($_SERVER['REMOTE_ADDR'] == '127.0.0.1')
        define('IS_LIVE', false);
    else
        define('IS_LIVE', true);


    define("BEDROOM_ID", "22");
    define("BATHROOM_ID", "23");
    define("ADDITIONAL_BATHROOM_ID", "24");
    define("DEFAULT_PHOTO", "generic.gif");

//for air conditioning 664
// BBQ 378
//Cot 725
//Elevator 678
//Games Room 268
//High Chair 726
//Internet Access 287
//Smoking Permitted 676
//TV in all bedrooms 258
//Washing Machine 669
//Dishwasher 720
//Wifi 286
    define("FACILITIES", "664,378,725,678,268,726,287,676,258,669,720, 286");

//private simming pool 379
//jacuzzi 383

    define("SWIMMING_POOLS", "379,383");

    /*     * ****Amenitites List***** */
//clubhouse 5
//Computer with internet access 18
//Gated Entrance 26
//Swimmming Pool 6
//Children's pool 14
//Tennis 23
//Wifi 19

    define("ON_SITE", "5,18,26,6,14,23,19");
//Family 425
//Couples 426
//Activity 427
//Themed Park 428
//Water Parks 429
    define("THEMES", "425,426,427,428,429");


//allowed image extension
    define("IMAGE_EXTNSN", "gif,jpeg,png,tif,tiff,jpg");

//allowed instruction file extension
    define("INSTRUCTION_EXTNSN", "doc,docx,pdf,gif,jpeg,png,tif,tiff,jpg");


    define("FLOORPLAN_EXTNSN", "jpg,jpeg,pdf,gif");

//Database Tabels
    define("ADMINISTRATOR", "administrator");
    define("CITIES", "cities");
    define("CURRENCY", "currency");
    define("PROPERTYTYPE", "property_type");
    define("COUNTRIES", "countries");
    define("EMAIL_TEMPLATES", "email_templates");
    define("PAGES", "pages");
    define("PAGES1", "pages1");
    define("PAGE_CAT", "page_categories");
    define("STATE", "state");
    define("USERS", "users");
    define("GALLERY", "gallery");
//define("PHOTO","photo_gallery");
    define("MESSAGES", "messages");
    define("CATEGORIES", "categories");
    define("FEVARITE_PHOTO_VIDEO", "fevrait_photo_video");
    define("PROPERTY", "property");
    define("PROPERTY_TYPE", "property_type");
    define("PHOTO_GALLERY", "photo_gallery");
    define("AMENITY", "amenity");
    define("SPECIFICATION", "specification");
    define("SPEC_CHILD", "spec_child");
    define("SPEC_ANS", "spec_ans");
    define("AMENITY_PAGE", "amenity_page");
    define("AMENITY_ANS", "amenity_ans");
    define("CAL_AVAIL", "cal_avail");
    define("SUB_AREA", "sub_area");
    define("PROPERTY_SPEC_CAT", "property_spec_cat");
    define("LOCAL_AREA", "local_area");
    define("CAL_RATE", "cal_rate");
    define("EXTRAS", "extras");
    define("SPCL_OFFER_TYPES", "spcl_offer_types");
    define("SPCL_OFFERS", "spcl_offers");
    define("OWNER_REVIEW", "review");
    define("HOMEPAGEIMG", "homepageimg");
    define("NEWS", "news");

    define("BOOKING", "booking");
    define("BOOKING_EXTRA", "booking_extra");
    define("PAYMENT", "payment");
    define("SLIDES_PROPERTY", "slides_property");
    define("LATEST_REVIEW", "latest_review");
    define("META_INFO", 'meta_info');
    define("LIBRARY", 'library');
    define("LOCATION_PAGE", 'location_page');
    define("LOCATION_FEATURES", 'location_features');
    define("LOCATION_QUESTIONS", 'location_questions');
    define("ATTRIBUTE", 'attribute');
    define("ATTRIBUTE_ANS", 'attribute_ans');

    $_CONFIG['AddPosition'] = explode(",", "Top Left,Top Middle,Top Right,Bottom Left,Bottom Middle,Bottom Right,Left Middle,Right Middle");
    define("PHOTOTRY", "photo_try");
//Database Tabels

    /*
      Re Captcha usaable
      --------------------------- */
    define("RECAPTCHA_API_SERVER", "http://www.google.com/recaptcha/api");
    define("RECAPTCHA_API_SECURE_SERVER", "https://www.google.com/recaptcha/api");
    define("RECAPTCHA_VERIFY_SERVER", "www.google.com");

    $publickey = "6LfexOASAAAAAMxvZXESTcPl_ohRj9N8CFhL7KLU";
    $privatekey = "6LfexOASAAAAAJO08JHt_9jQVF0l608tjyAK6Ne4";
    define("CAPTCHA_PUBLLIC_KEY", $publickey);
    define("CAPTCHA_PRIVATE_KEY", $privatekey);
?>
