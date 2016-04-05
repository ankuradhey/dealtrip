<?php

class Form_Subscriber extends Zend_Form {

    public function __construct($subscriberId = "") {
        $this->init($subscriberId);
    }

    public function init($subscriberId) {
        global $mySession;
        $db = new Db();

        $CountryId = "";
        $StateId = "";
        $telephone_value =
        $subscriber_address_box1_value =
                $subscriber_address_box2_value =
                $subscriber_address_box3_value =
                $subscriber_address_box4_value =
                $subscriber_address_box5_value = ""
        ;

        $CityName = "";

        $latitude_value = "41.659";
        $longitude_value = "-4.714";

        if ($subscriberId != "") {
            $subscriberData = $db->runQuery("select * from subscriber where subscriber_id='" . $subscriberId . "'");
            $subscriber_name_value = $subscriberData[0]['subscriber_name'];
            $subscriber_url_value = $subscriberData[0]['subscriber_url'];
            $subscriber_api_username_value = $subscriberData[0]['subscriber_api_username'];
            $subscriber_api_password_value = $subscriberData[0]['subscriber_api_password'];
            $email_address_value = $subscriberData[0]['subscriber_email'];
            $email_address1_value = $subscriberData[0]['subscriber_email_alt'];
            $dealatrip_page_value = $subscriberData[0]['subscriber_dealatrip_webpage'];
            $telephone_value = $subscriberData[0]['subscriber_dealatrip_webpage'];
            
            $latitude = $subscriberData[0]['subscriber_lat_lng'];
            if (!empty($latitude)) {
                $latitude = explode(',', $latitude);
                $latitude_value = $latitude[0];
                $longitude_value = $latitude[1];
            }

            $_contact_name_value = explode(',', $subscriberData[0]['contact_person']);
            $contact_name_value = $_contact_name_value[0];
            $contact_name_value1 = isset($_contact_name_value[1])?$_contact_name_value[1]:'';

            //customer support values
            $customerSupport = explode(',', $subscriberData[0]['subscriber_customer_support']);
            $customer_support_value = isset($customerSupport[0])?$customerSupport[0]:'';
            $customer_support_value1 = isset($customerSupport[1]) ? $customerSupport[0] : '';
            $customer_support_value2 = isset($customerSupport[2]) ? $customerSupport[2] : '';
            $customer_support_value3 = isset($customerSupport[3]) ? $customerSupport[3] : '';
            $customer_support_value4 = isset($customerSupport[4]) ? $customerSupport[4] : '';

            //address
            $subscriber_address = explode(',',$subscriberData[0]['subscriber_address']);
            $subscriber_address_box1_value = isset($subscriber_address[0])?$subscriber_address[0]:'';
            $subscriber_address_box2_value = isset($subscriber_address[1])?$subscriber_address[1]:'';
            $subscriber_address_box3_value = isset($subscriber_address[2])?$subscriber_address[2]:'';
            $subscriber_address_box4_value = isset($subscriber_address[3])?$subscriber_address[3]:'';
            $subscriber_address_box5_value = isset($subscriber_address[4])?$subscriber_address[4]:'';;
        }


        $subscriber_name = new Zend_Form_Element_Text('subscriber_name');
        $subscriber_name->setRequired(true)
                ->addValidator('NotEmpty', true, array('messages' => 'Subscriber Name is required.'))
                ->addDecorator('Errors', array('class' => 'error'))
                ->addFilter('StringTrim')
                ->setAttrib("class", "mws-textinput required")
                ->setValue($subscriber_name_value);

        $subscriber_url = new Zend_Form_Element_Text('subscriber_url');
        $subscriber_url
                ->setRequired(true)
                ->addValidator('NotEmpty', true, array('messages' => 'Subscriber url is required.'))
                ->addDecorator('Errors', array('class' => 'error'))
                ->addFilter('StringTrim')
                ->setAttrib("class", "mws-textinput required")
                ->setValue($subscriber_url_value);

        $subscriber_api_username = new Zend_Form_Element_Text('subscriber_api_username');
        $subscriber_api_username
//                ->setRequired(true)
//                ->addValidator('NotEmpty', true, array('messages' => 'Subscriber API username is required.'))
//                ->addDecorator('Errors', array('class' => 'error'))
                ->addFilter('StringTrim')
                ->setAttrib("class", "mws-textinput required")
                ->setValue($subscriber_api_username_value);

        $subscriber_api_password = new Zend_Form_Element_Text('subscriber_api_password');
        $subscriber_api_password
//                ->setRequired(true)
//                ->addValidator('NotEmpty', true, array('messages' => 'Subscriber API password is required.'))
//                ->addDecorator('Errors', array('class' => 'error'))
                ->addFilter('StringTrim')
                ->setAttrib("class", "mws-textinput required")
                ->setValue($subscriber_api_password_value);


        $subscriber_address_box1 = new Zend_Form_Element_Text('subscriber_address_box1');
        $subscriber_address_box1->setRequired(true)
                ->addValidator('NotEmpty', true, array('messages' => 'Supplier Address is required'))
                ->addDecorator('Errors', array('class' => 'error'))
                ->addFilter('StringTrim')
                ->setAttrib("class", "mws-textinput required")
                ->setValue($subscriber_address_box1_value);

        $subscriber_address_box2 = new Zend_Form_Element_Text('subscriber_address_box2');
        $subscriber_address_box2
                ->addDecorator('Errors', array('class' => 'error'))
                ->addFilter('StringTrim')
                ->setAttrib("class", "mws-textinput")
                ->setValue($subscriber_address_box2_value);

        $subscriber_address_box3 = new Zend_Form_Element_Text('subscriber_address_box3');
        $subscriber_address_box3
                ->addFilter('StringTrim')
                ->setAttrib("class", "mws-textinput")
                ->setValue($subscriber_address_box3_value);

        $subscriber_address_box4 = new Zend_Form_Element_Text('subscriber_address_box4');
        $subscriber_address_box4
                ->addFilter('StringTrim')
                ->setAttrib("class", "mws-textinput")
                ->setValue($subscriber_address_box4_value);

        $subscriber_address_box5 = new Zend_Form_Element_Text('subscriber_address_box5');
        $subscriber_address_box5
                ->addFilter('StringTrim')
                ->setAttrib("class", "mws-textinput")
                ->setValue($subscriber_address_box5_value);

        $contact_name = new Zend_Form_Element_Text('contact_name');
        $contact_name->setRequired(true)
                ->addValidator('NotEmpty', true, array('messages' => 'Contact name is required'))
                ->addFilter('StringTrim')
                ->addDecorator('Errors', array('class' => 'error'))
                ->setAttrib("class", "mws-textinput required")
                ->setValue($contact_name_value);

        $contact_name1 = new Zend_Form_Element_Text('contact_name1');
        $contact_name1
                ->addFilter('StringTrim')
                ->addDecorator('Errors', array('class' => 'error'))
                ->setAttrib("class", "mws-textinput")
                ->setValue($contact_name_value1);

        $email_address = new Zend_Form_Element_Text('email_address');
        $email_address->setRequired(true)
                ->addValidator('NotEmpty', true, array('messages' => 'Email address is required'))
                ->addValidator('EmailAddress', true, array('messages' => 'Please enter correct email address'))
                ->addFilter('StringTrim')
                ->addDecorator('Errors', array('class' => 'error'))
                ->setAttrib("class", "mws-textinput")
                ->setValue($email_address_value);

        $email_address1 = new Zend_Form_Element_Text('email_address1');
        $email_address1
                ->addValidator('EmailAddress', true, array('messages' => 'Email address is required'))
                ->addFilter('StringTrim')
                ->addDecorator('Errors', array('class' => 'error'))
                ->setAttrib("class", "mws-textinput")
                ->setValue($email_address1_value);

        $telephone = new Zend_Form_Element_Text('telephone');
        $telephone->setRequired(true)
                ->addValidator('NotEmpty', true, array('messages' => 'Contact name is required'))
                ->addValidator('Regex', true, array('/^[0-9]+$/'))
                ->addFilter('StringTrim')
                ->addDecorator('Errors', array('class' => 'error'))
                ->setAttrib("class", "mws-textinput required")
                ->setValue($telephone_value);

        $website = new Zend_Form_Element_Text('website');
        $website->setRequired(true)
                ->addFilter('StringTrim')
                ->addDecorator('Errors', array('class' => 'error'))
                ->setAttrib("class", "mws-textinput required")
                ->setValue($website_value);

        $customer_support = new Zend_Form_Element_Text('customer_support');
        $customer_support->setRequired(true)
                ->addFilter('StringTrim')
                ->addDecorator('Errors', array('class' => 'error'))
                ->setAttrib("class", "mws-textinput required")
                ->setValue($customer_support_value);

        $customer_support1 = new Zend_Form_Element_Text('customer_support1');
        $customer_support1
                ->addFilter('StringTrim')
                ->addDecorator('Errors', array('class' => 'error'))
                ->setAttrib("class", "mws-textinput")
                ->setValue($customer_support_value1);

        $customer_support2 = new Zend_Form_Element_Text('customer_support2');
        $customer_support2
                ->addFilter('StringTrim')
                ->addDecorator('Errors', array('class' => 'error'))
                ->setAttrib("class", "mws-textinput")
                ->setValue($customer_support_value2);

        $customer_support3 = new Zend_Form_Element_Text('customer_support3');
        $customer_support3
                ->addFilter('StringTrim')
                ->addDecorator('Errors', array('class' => 'error'))
                ->setAttrib("class", "mws-textinput")
                ->setValue($customer_support_value3);

        $customer_support4 = new Zend_Form_Element_Text('customer_support4');
        $customer_support4
                ->addFilter('StringTrim')
                ->addDecorator('Errors', array('class' => 'error'))
                ->setAttrib("class", "mws-textinput")
                ->setValue($customer_support_value4);

        $additional_info = new Zend_Form_Element_Text('additional_info');
        $additional_info->setRequired(true)
                ->addFilter('StringTrim')
                ->addDecorator('Errors', array('class' => 'error'))
                ->setAttrib("class", "mws-textinput required")
                ->setValue($additional_info_value);

        $dealatrip_page = new Zend_Form_Element_Text('dealatrip_page');
        $dealatrip_page->setRequired(true)
                ->addFilter('StringTrim')
                ->addDecorator('Errors', array('class' => 'error'))
                ->setAttrib("class", "mws-textinput required")
                ->setValue($dealatrip_page_value);

        $latitude = new Zend_Form_Element_Hidden('latitude');
        $latitude->setRequired(true)
                ->addFilter('StringTrim')
                ->addDecorator('Errors', array('class' => 'error'))
                ->setAttrib("class", "mws-textinput required")
                ->setValue($latitude_value);

        $longitude = new Zend_Form_Element_Hidden('longitude');
        $longitude->setRequired(true)
                ->addFilter('StringTrim')
                ->addDecorator('Errors', array('class' => 'error'))
                ->setAttrib("class", "mws-textinput required")
                ->setValue($longitude_value);


        $this->addElements(array($subscriber_name, $subscriber_url, $subscriber_api_username, $subscriber_api_password,
            $subscriber_address_box1, $subscriber_address_box2, $subscriber_address_box3, $subscriber_address_box4, $subscriber_address_box5,
            $contact_name, $contact_name1, $email_address, $email_address1,
            $telephone, $website,
            $customer_support, $customer_support1, $customer_support2, $customer_support3, $customer_support4,
            $additional_info,
            $dealatrip_page,
            $latitude, $longitude
        ));
    }

}

?>