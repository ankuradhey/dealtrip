<?php

class Form_OverviewUpdater extends Zend_Form {

    public function __construct($ppty_id = "") {
        $this->init($ppty_id);
    }

    public function init($ppty_id) {
        $db = new Db();
        global $mySession;
        if ($ppty_id != "") {
            $pptyData = $db->runQuery("select * from " . PROPERTY . " where id = '" . $ppty_id . "' ");
            $title_value = $pptyData[0]['property_title'];
            $introduction_value = $pptyData[0]['brief_desc'];
            $country_id_value = $pptyData[0]['country_id'];
            $state_id_value = $pptyData[0]['state_id'];
            $city_id_value = $pptyData[0]['city_id'];
            $sub_area_value = $pptyData[0]['sub_area_id'];
            $local_area_value = $pptyData[0]['local_area_id'];
            $zipcode_value = $pptyData[0]['zipcode'];
            $accomodation_type_value = $pptyData[0]['property_type'];
            $no_of_bedroom_value = $pptyData[0]['bedrooms'];
            $no_of_bathroom_value = $pptyData[0]['bathrooms'];
            $no_of_en_suite_bathroom_value = $pptyData[0]['en_bedrooms'];
            $occupancy_value = $pptyData[0]['maximum_occupancy'];
            $meta_keywords_value = $pptyData[0]['meta_keywords'];
            $meta_description_value = $pptyData[0]['meta_description'];

            //prd($pptyData[0]);
            //$rating_value = $pptyData[0]['star_rating'];
        }

        $db = new Db();
        $title = new Zend_Form_Element_Text('title');
        $title->setRequired(true)
                ->addValidator('NotEmpty', true, array('messages' => 'Title is required.'))
                ->addDecorator('Errors', array('class' => 'errmsg'))
                ->setAttrib("class", "mws-textinput required limitmatch")
                ->setAttrib("placeholder", "Up to 100 Characters")
                ->setAttrib("maxlength", "100")
                    ->setValue($title_value)
        ;

        $meta_keywords = new Zend_Form_Element_Text('meta_keywords');
        $meta_keywords
//                    ->setRequired(true)
                ->addValidator('NotEmpty', true, array('messages' => 'Keywords is required.'))
                ->addDecorator('Errors', array('class' => 'errmsg'))
                ->setAttrib("class", "mws-textinput  limitmatch")
                ->setAttrib("placeholder", "Up to 100 Characters")
                ->setAttrib("maxlength", "100")
                ->setValue($meta_keywords_value)
        ;

        $meta_description = new Zend_Form_Element_Text('meta_description');
        $meta_description
//                    ->setRequired(true)
                ->addValidator('NotEmpty', true, array('messages' => 'Description is required.'))
                ->addDecorator('Errors', array('class' => 'errmsg'))
                ->setAttrib("class", "mws-textinput  limitmatch")
                ->setAttrib("placeholder", "Up to 200 Characters")
                ->setAttrib("maxlength", "200")
                    ->setValue($meta_description_value)
        ;

        $introduction = new Zend_Form_Element_Textarea('introduction');
        $introduction->setRequired(true)
                ->addValidator('NotEmpty', true, array('messages' => 'Title is required.'))
                ->addDecorator('Errors', array('class' => 'errmsg'))
                ->setAttrib("class", "mws-textinput required limitmatch1")
                ->setAttrib("placeholder", "Up to 300 Characters ")
                ->setAttrib("maxlength", "300")
                    ->setValue($introduction_value)
        ;




        $CountryArr = array();
        $CountryArr[0]['key'] = "";
        $CountryArr[0]['value'] = "- - Country - -";

        $CountryData = $db->runQuery("select * from " . COUNTRIES . "  order by country_name");

        if ($CountryData != "" and count($CountryData) > 0) {
            $i = 1;
            foreach ($CountryData as $key => $CountryValues) {
                $CountryArr[$i]['key'] = $CountryValues['country_id'];
                $CountryArr[$i]['value'] = $CountryValues['country_name'];
                $i++;
            }
        }



        $country_id = new Zend_Form_Element_Select('country_id');
        $country_id->setRequired(true)
                ->addMultiOptions($CountryArr)
                ->addValidator('NotEmpty', true, array('messages' => 'Country is required.'))
                ->addDecorator('Errors', array('class' => 'errmsg'))
                ->setAttrib("class", "mws-textinput required")
                ->setAttrib("onchange", "getCountryState(this.value);")
                    ->setValue($country_id_value)
        ;



        $stateArr = array();
        $stateArr[0]['key'] = "";
        $stateArr[0]['value'] = "- - State - -";

        /* if($userId != "")
          {
          $stateData=$db->runQuery("select * from ".USERS." as u inner join ".STATE." as s on s.state_id = u.state_id
          where u.user_id='".$userId."'");
          $stateArr[0]['key'] = $stateData[0]['state_id'];
          $stateArr[0]['value'] = $stateData[0]['state_name'];
          $state_id_value = $adminData[0]['state_id'];

          } */

//		echo @$_REQUEST['country_id']; exit; 

        if (@$_REQUEST['country_id'] != "" || $ppty_id != "") {
            if (@$_REQUEST['country_id'] != "") {
                $stateData = $db->runQuery("select * from " . STATE . " as s inner join " . COUNTRIES . " as c on s.country_id = c.country_id
			 						  where s.country_id='" . @$_REQUEST['country_id'] . "'");
                $state_id_value = @$_REQUEST['state_id'];
            } else {
                $stateData = $db->runQuery("select * from " . STATE . " as s inner join " . COUNTRIES . " as c on s.country_id = c.country_id
	 						  where s.country_id='" . $country_id_value . "'");
            }

            $i = 1;
            foreach ($stateData as $values) {
                $stateArr[$i]['key'] = $values['state_id'];
                $stateArr[$i]['value'] = $values['state_name'];

                $i++;
            }
        }


        $state_id = new Zend_Form_Element_Select('state_id');
        $state_id->setRequired(true)
                ->addMultiOptions($stateArr)
                ->addValidator('NotEmpty', true, array('messages' => 'State is required.'))
                ->addDecorator('Errors', array('class' => 'errmsg'))
                ->setRegisterInArrayValidator(false)
                ->setAttrib("class", "mws-textinput required")
                ->setAttrib("onchange", "getStateCity(this.value);")
                    ->setValue($state_id_value)
        ;



        $cityArr = array();
        $cityArr[0]['key'] = "";
        $cityArr[0]['value'] = "- - City - -";

        if (@$_REQUEST['state_id'] != "" || $ppty_id != "") {
            if (@$_REQUEST['state_id'] != "") {
                $cityData = $db->runQuery("select * from " . CITIES . " where state_id='" . @$_REQUEST['state_id'] . "'");
                $city_id_value = @$_REQUEST['city_id'];
            } else {
                $cityData = $db->runQuery("select * from " . CITIES . " where state_id='" . $state_id_value . "'");
            }

            $i = 1;
            foreach ($cityData as $values) {
                $cityArr[$i]['key'] = $values['city_id'];
                $cityArr[$i]['value'] = $values['city_name'];

                $i++;
            }
        }


        $city_id = new Zend_Form_Element_Select('city_id');
        $city_id->setRequired(true)
                ->addMultiOptions($cityArr)
                ->setRegisterInArrayValidator(false)
                ->addValidator('NotEmpty', true, array('messages' => 'City is required.'))
                ->setAttrib("class", "mws-textinput required")
                ->setAttrib("onchange", "getCitySub(this.value);")
                    ->setValue($city_id_value)
        ;


        /*         * ** SUB AREA[OPTIONAL] **** */
        /*         * ************************** */

        $subareaArr = array();
        $subareaArr[0]['key'] = "";
        $subareaArr[0]['value'] = "- - Sub Area - -";

        if (@$_REQUEST['city_id'] != "" || $ppty_id != "") {
            if (@$_REQUEST['city_id'] != "") {
                $subareaData = $db->runQuery("select * from " . SUB_AREA . " where city_id ='" . @$_REQUEST['city_id'] . "'");
                $sub_area_value = @$_REQUEST['sub_area'];
            } else {
                $subareaData = $db->runQuery("select * from " . SUB_AREA . " where city_id ='" . $city_id_value . "'");
            }

            $i = 1;
            foreach ($subareaData as $values) {
                $subareaArr[$i]['key'] = $values['sub_area_id'];
                $subareaArr[$i]['value'] = $values['sub_area_name'];
                $i++;
            }
        }

        //prd($sub_area_value);

        $sub_area = new Zend_Form_Element_Select('sub_area');
        $sub_area->addMultiOptions($subareaArr)
                ->setRegisterInArrayValidator(false)
                ->setAttrib("class", "mws-textinput")
                ->setAttrib("onchange", "getSubLocal(this.value);")
                    ->setValue($sub_area_value)
        ;



        /*         * ** LOCAL AREA[OPTIONAL] ** */
        /*         * ************************** */

        $localareaArr = array();
        $localareaArr[0]['key'] = "";
        $localareaArr[0]['value'] = "- - Local Area - -";

        if (@$_REQUEST['sub_area'] != "" || $ppty_id != "") {
            if (@$_REQUEST['sub_area'] != "") {
                $localareaData = $db->runQuery("select * from " . LOCAL_AREA . " where sub_area_id ='" . @$_REQUEST['sub_area'] . "'");
                $local_area_value = @$_REQUEST['local_area'];
            } else {
                $localareaData = $db->runQuery("select * from " . LOCAL_AREA . " where sub_area_id ='" . $sub_area_value . "'");
            }

            $i = 1;
            foreach ($localareaData as $values) {
                $localareaArr[$i]['key'] = $values['local_area_id'];
                $localareaArr[$i]['value'] = $values['local_area_name'];
                $i++;
            }
        }



        $local_area = new Zend_Form_Element_Select('local_area');
        $local_area->addMultiOptions($localareaArr)
                ->setRegisterInArrayValidator(false)
                ->setAttrib("class", "mws-textinput")
                    ->setValue($local_area_value)
        ;


        $zipcode = new Zend_Form_Element_Text('zipcode');
        $zipcode
                //->setRequired(true)
//                    ->addValidator('NotEmpty', true, array('messages' => 'Zipcode is required.'))
                ->addDecorator('Errors', array('class' => 'errmsg'))
                ->setAttrib("class", "mws-textinput")
                ->setAttrib("maxlength", "7")
                    ->setValue($zipcode_value)
        ;

        $accomodationData = $db->runQuery("select * from " . PROPERTY_TYPE . " order by ptyle_name");
        $accomodationArr[0]['key'] = "";
        $accomodationArr[0]['value'] = "- - select -- ";

        $i = 1;
        foreach ($accomodationData as $key => $Data) {
            $accomodationArr[$i]['key'] = $Data['ptyle_id'];
            $accomodationArr[$i]['value'] = $Data['ptyle_name'];
            $i++;
        }


        $accomodation_type = new Zend_Form_Element_Select('accomodation_type');
        $accomodation_type
                ->addMultiOptions($accomodationArr)
                ->setAttrib("class", "mws-textinput")
                    ->setValue($accomodation_type_value)
        ;



        /** number of bedrooms * */
        $no_of_bedroomArr[0]['key'] = "";
        $no_of_bedroomArr[0]['value'] = "- - Select - -";

        for ($i = 1; $i <= 10; $i++) {
            $no_of_bedroomArr[$i]['key'] = $i;
            $no_of_bedroomArr[$i]['value'] = $i;
        }



        $no_of_bedroom = new Zend_Form_Element_Select('no_of_bedroom');
        $no_of_bedroom
                ->addMultiOptions($no_of_bedroomArr)
                ->setAttrib("class", "mws-textinput")
                    ->setValue($mySession->filteredCriteria['no_of_bedroom'])
        ;

        $step = new Zend_Form_Element_Hidden('step');
        $step->setRequired(true)
                ->setValue(1);

        $supplierArr[0]['key'] = "";
        $supplierArr[0]['value'] = "- - select -- ";
        $supplierArr[1]['key'] = "1";
        $supplierArr[1]['value'] = "Ciirus";
        $supplierArr[2]['key'] = "2";
        $supplierArr[2]['value'] = "Global Resort Homes";
        $supplierArr[3]['key'] = "3";
        $supplierArr[3]['value'] = "Contempo Vacation";
        
        $supplier = new Zend_Form_Element_Select('supplier');
        $supplier
                ->addMultiOptions($supplierArr)
                ->setRegisterInArrayValidator(false)
                ->setAttrib("class", "mws-textinput")
                ->setValue($mySession->filteredCriteria['supplier']);
//                ->setAttrib("onchange", "getStateCity(this.value);")
        ;

        $pageArr[0]['key'] = "";
        $pageArr[0]['value'] = "- - select -- ";
        $pageArr[1]['key'] = "1";
        $pageArr[1]['value'] = "Specification";
        $pageArr[2]['key'] = "2";
        $pageArr[2]['value'] = "Area";
        $pageArr[3]['key'] = "3";
        $pageArr[3]['value'] = "Rental Rates";
        $pageArr[4]['key'] = "4";
        $pageArr[4]['value'] = "Work Page";
        
        $page = new Zend_Form_Element_Select('page');
        $page->addMultiOptions($pageArr)
             ->setRequired(true)
             ->setRegisterInArrayValidator(false)
             ->addDecorator('Errors', array('class' => 'errmsg'))
             ->setAttrib("class", "mws-textinput required")
             ->setAttrib("onchange", "pageSelect(this.value)")
             ->setValue($mySession->filteredCriteria['supplier']);
        
        $this->addElements(array($title, $introduction, $country_id, $state_id, $city_id, $accomodation_type, $no_of_bedroom, $sub_area, $local_area, $zipcode, $step, $supplier, $page));
    }

}