<?php

class Form_Updater extends Zend_Form {

    public function __construct($ppty_id = "") {
        $this->init($ppty_id);
    }

    public function init($ppty_id) {
        global $mySession;
        $db = new Db();
    }

    public function getOverviewForm($ppty_id) {
        $db = new Db();
        $title = new Zend_Form_Element_Text('title');
        $title->setRequired(true)
                ->addValidator('NotEmpty', true, array('messages' => 'Title is required.'))
                ->addDecorator('Errors', array('class' => 'errmsg'))
                ->setAttrib("class", "mws-textinput required limitmatch")
                ->setAttrib("placeholder", "Up to 100 Characters")
                ->setAttrib("maxlength", "100")
//                    ->setValue($title_value)
        ;

        $meta_keywords = new Zend_Form_Element_Text('meta_keywords');
        $meta_keywords
//                    ->setRequired(true)
                ->addValidator('NotEmpty', true, array('messages' => 'Keywords is required.'))
                ->addDecorator('Errors', array('class' => 'errmsg'))
                ->setAttrib("class", "mws-textinput  limitmatch")
                ->setAttrib("placeholder", "Up to 100 Characters")
                ->setAttrib("maxlength", "100")
//                    ->setValue($meta_keywords_value)
        ;

        $meta_description = new Zend_Form_Element_Text('meta_description');
        $meta_description
//                    ->setRequired(true)
                ->addValidator('NotEmpty', true, array('messages' => 'Description is required.'))
                ->addDecorator('Errors', array('class' => 'errmsg'))
                ->setAttrib("class", "mws-textinput  limitmatch")
                ->setAttrib("placeholder", "Up to 200 Characters")
                ->setAttrib("maxlength", "200")
//                    ->setValue($meta_description_value)
        ;

        $introduction = new Zend_Form_Element_Textarea('introduction');
        $introduction->setRequired(true)
                ->addValidator('NotEmpty', true, array('messages' => 'Title is required.'))
                ->addDecorator('Errors', array('class' => 'errmsg'))
                ->setAttrib("class", "mws-textinput required limitmatch1")
                ->setAttrib("placeholder", "Up to 300 Characters ")
                ->setAttrib("maxlength", "300")
//                    ->setValue($introduction_value)
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
//                    ->setValue($country_id_value)
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
//                    ->setValue($state_id_value)
        ;



        $cityArr = array();
        $cityArr[0]['key'] = "";
        $cityArr[0]['value'] = "- - City - -";


        /* if($userId != "")
          {
          $cityData=$db->runQuery("select * from ".USERS." as u inner join ".CITIES." as c on c.city_id = u.city_id
          where u.user_id='".$userId."'");
          $cityArr[0]['key'] = $cityData[0]['city_id'];
          $cityArr[0]['value'] = $cityData[0]['city_name'];
          $city_id_value = $cityData[0]['city_id'];

          } */

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
//                    ->setValue($city_id_value)
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
//                    ->setValue($sub_area_value)
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
//                    ->setValue($local_area_value)
        ;


        $zipcode = new Zend_Form_Element_Text('zipcode');
        $zipcode
                //->setRequired(true)
//                    ->addValidator('NotEmpty', true, array('messages' => 'Zipcode is required.'))
                ->addDecorator('Errors', array('class' => 'errmsg'))
                ->setAttrib("class", "mws-textinput")
                ->setAttrib("maxlength", "7")
//                    ->setValue($zipcode_value)
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
        $accomodation_type->setRequired(true)
                ->addMultiOptions($accomodationArr)
                ->addValidator('NotEmpty', true, array('messages' => 'Country is required.'))
                ->addDecorator('Errors', array('class' => 'errmsg'))
                ->setAttrib("class", "mws-textinput required")
//                    ->setValue($accomodation_type_value)
        ;



        /** number of bedrooms * */
        $no_of_bedroomArr[0]['key'] = "";
        $no_of_bedroomArr[0]['value'] = "- - Select - -";

        for ($i = 1; $i <= 10; $i++) {
            $no_of_bedroomArr[$i]['key'] = $i;
            $no_of_bedroomArr[$i]['value'] = $i;
        }



        $no_of_bedroom = new Zend_Form_Element_Select('no_of_bedroom');
        $no_of_bedroom->setRequired(true)
                ->addMultiOptions($no_of_bedroomArr)
                ->addValidator('NotEmpty', true, array('messages' => 'Country is required.'))
                ->addDecorator('Errors', array('class' => 'errmsg'))
                ->setAttrib("class", "mws-textinput required")
//                    ->setValue($no_of_bedroom_value)
        ;

        /** number of bathrooms * */
        $no_of_bathroomArr[0]['key'] = "";
        $no_of_bathroomArr[0]['value'] = "- - Select - -";

        for ($i = 1, $k = 1; $i <= 20; $k = $k + 0.5, $i++) {
            $no_of_bathroomArr[$i]['key'] = $k;
            $no_of_bathroomArr[$i]['value'] = $k;
        }


        $no_of_bathroom = new Zend_Form_Element_Select('no_of_bathroom');
        $no_of_bathroom->setRequired(true)
                ->addMultiOptions($no_of_bathroomArr)
                ->addDecorator('Errors', array('class' => 'errmsg'))
                ->setAttrib("class", "mws-textinput required")
//                    ->setValue($no_of_bathroom_value)
        ;

        /** number of En-Suite Bathrooms * */
        $no_of_nbathroomArr[0]['key'] = "";
        $no_of_nbathroomArr[0]['value'] = "- - Select - -";

        for ($i = 1, $k = 0; $k <= 8; $i++, $k++) {
            $no_of_nbathroomArr[$i]['key'] = $k;
            $no_of_nbathroomArr[$i]['value'] = $k;
        }



        $no_of_en_suite_bathroom = new Zend_Form_Element_Select('no_of_en_suite_bathroom');
        $no_of_en_suite_bathroom->setRequired(true)
                ->addMultiOptions($no_of_nbathroomArr)
                ->addValidator('NotEmpty', true, array('messages' => 'Country is required.'))
                ->addDecorator('Errors', array('class' => 'errmsg'))
                ->setAttrib("class", "mws-textinput required")
//                    ->setValue($no_of_en_suite_bathroom_value)
        ;


        /** maximum occupancy * */
        $occupancyArr[0]['key'] = "";
        $occupancyArr[0]['value'] = "- - Select - -";

        for ($i = 1; $i <= 20; $i++) {
            $occupancyArr[$i]['key'] = $i;
            $occupancyArr[$i]['value'] = $i;
        }

        $occupancy = new Zend_Form_Element_Select('occupancy');
        $occupancy->setRequired(true)
                ->addMultiOptions($occupancyArr)
                ->addValidator('NotEmpty', true, array('messages' => 'Country is required.'))
                ->addDecorator('Errors', array('class' => 'errmsg'))
                ->setAttrib("class", "mws-textinput required")
//                    ->setValue($occupancy_value)
        ;



        /** Rating * */
        /* 		
          for($i = 1; $i<=5;$i++)
          {
          $ratingArr[$i]['key'] = $i;
          }



          $rating = new Zend_Form_Element_Radio('rating');
          $rating->setRequired(true)
          ->addMultiOptions($ratingArr)
          ->setRegisterInArrayValidator(false)
          ->removeDecorator('label')
          ->setAttrib("class","star")
          ->setValue($rating_value);

         */

        $step = new Zend_Form_Element_Hidden('step');
        $step->setRequired(true)
                ->setValue(1);


        $overview->addElements(array($title, $introduction, $country_id, $state_id, $city_id, $accomodation_type, $no_of_bedroom, $no_of_bathroom, $no_of_en_suite_bathroom, $occupancy, $sub_area, $local_area, $zipcode, $meta_keywords, $meta_description, $step));
        return $overview;
    }

    public function getSpecificationForm() {
        $db = new Db();
        $specification = new Zend_Form_SubForm();
        $specQues = $db->runQuery("select * from " . SPECIFICATION . " as s inner join " . PROPERTY_SPEC_CAT . " as psc on psc.cat_id = s.cat_id  where psc.cat_status = '1' 
								  and s.status = '1' order by psc.cat_id, s.spec_order asc");

        $valueVar = "";

        $i = 0;
        foreach ($specQues as $key => $value) {
            $ques_value[$i] = "";

            if ($value['spec_type'] == '4') {   //if specification type is checkbox
                $selectOptionArr = $db->runQuery("select * from " . SPEC_CHILD . " where spec_id = '" . $value['spec_id'] . "' ");
                $k = 1;

                if ($ppty_id != "") { //condition when edit is performed
                    $spec_ansData = $db->runQuery("select * from  " . SPEC_ANS . " where spec_id = '" . $value['spec_id'] . "' and property_id = '" . $ppty_id . "' ");
                    foreach ($spec_ansData as $valuest)
                        $ques_value[$i][] = $valuest['answer'];
                }

                $OptionsArr = array();
                foreach ($selectOptionArr as $values) {
                    $OptionsArr[$k]['key'] = $values['cid'];
                    $OptionsArr[$k]['value'] = $values['option'];
                    $k++;
                }




                if ($values['spec_id'] == ADDITIONAL_BATHROOM_ID) {

                    $bedroomArr = $db->runQuery("select * from " . PROPERTY . " where id = '" . $ppty_id . "' ");

                    $now = $i;

                    for ($x = 0; $x < $bedroomArr[0]['bedrooms']; $x++) {

                        $ques_value_bath = explode("|||", $ques_value[$now][$x]);

                        $quest = 'ques' . $i . $x;
                        $ques[$i] = new Zend_Form_Element_MultiCheckbox($quest);
                        $ques[$i]->addMultiOptions($OptionsArr)
                                ->setAttrib("class", "mws-textinput")
                                ->setValue($ques_value_bath);
                        $i++;
                    }
                    $i--;
                } else {

                    $quest = 'ques' . $i;
                    $ques[$i] = new Zend_Form_Element_MultiCheckbox($quest);
                    $ques[$i]->setAttrib("class", "mws-textinput")
                            ->addMultiOptions($OptionsArr)
                            ->setAttrib('label_style', '')
                            ->setAttrib('padding', '5px;')
                            ->setValue($ques_value[$i]);
                }

                if ($value['mandatory'])
                    $ques[$i]->setAttrib("class", "required");
            }

            if ($value['spec_type'] == '3') {   //if specification type is textbox
                if ($ppty_id != "") { //condition when edit is performed
                    $spec_ansData = $db->runQuery("select * from  " . SPEC_ANS . " where spec_id = '" . $value['spec_id'] . "' and property_id = '" . $ppty_id . "' ");
                    $ques_value[$i] = $spec_ansData[0]['answer'];
                }

                $quest = 'ques' . $i;
                $ques[$i] = new Zend_Form_Element_Text($quest);
                $ques[$i]->setAttrib("class", "mws-textinput")
                        ->setAttrib("maxlength", "100")
                        ->setValue($ques_value[$i]);

                if ($value['mandatory'])
                    $ques[$i]->setAttrib("class", "required");
            }



            if ($value['spec_type'] == '2') {   //if specification type is textarea
                if ($ppty_id != "") { //condition when edit is performed
                    $spec_ansData = $db->runQuery("select * from  " . SPEC_ANS . " where spec_id = '" . $value['spec_id'] . "' and property_id = '" . $ppty_id . "' ");
                    $ques_value[$i] = $spec_ansData[0]['answer'];
                }

                $quest = 'ques' . $i;
                $ques[$i] = new Zend_Form_Element_Textarea($quest);
                $ques[$i]->setAttrib("class", "mws-textinput")
                        ->setAttrib("maxlength", "300")
                        ->setAttrib("rows", "30")
                        ->setAttrib("cols", "60")
                        ->setValue($ques_value[$i]);

                if ($value['mandatory'])
                    $ques[$i]->setAttrib("class", "required");
            }


            if ($value['spec_type'] == '1') {   //if specification type is Select Box
                $selectOptionArr = $db->runQuery("select * from " . SPEC_CHILD . " where spec_id = '" . $value['spec_id'] . "' ");

                if ($ppty_id != "") { //condition when edit is performed
                    $spec_ansData = $db->runQuery("select * from  " . SPEC_ANS . " where spec_id = '" . $value['spec_id'] . "' and property_id = '" . $ppty_id . "' ");
                    //if($value['spec_id'] == '23')
                    //prd($spec_ansData);

                    $ques_value[$i] = $spec_ansData[0]['answer'];
                }

                $OptionsArr = array();
                $OptionsArr[0]['key'] = "";
                $OptionsArr[0]['value'] = "- - Select - -";

                $k = 1;

                foreach ($selectOptionArr as $values) {
                    $OptionsArr[$k]['key'] = $values['cid'];
                    $OptionsArr[$k]['value'] = $values['option'];
                    $k++;
                }


                if ($values['spec_id'] == BEDROOM_ID || $values['spec_id'] == BATHROOM_ID) {
                    $bedroomArr = $db->runQuery("select * from " . PROPERTY . " where id = '" . $ppty_id . "' ");

                    $ques_value_bath = explode("|||", $ques_value[$i]);

                    for ($x = 0; $x < $bedroomArr[0]['bedrooms']; $x++) {


                        $quest = 'ques' . $i . $x;
                        $ques[$i] = new Zend_Form_Element_Select($quest);
                        $ques[$i]->addMultiOptions($OptionsArr)
                                ->setAttrib("class", "mws-textinput")
                                ->setValue($ques_value_bath[$x]);
                        $i++;
                    }
                    $i--;
                } else {
                    $quest = 'ques' . $i;
                    $ques[$i] = new Zend_Form_Element_Select($quest);
                    $ques[$i]->addMultiOptions($OptionsArr)
                            ->setAttrib("class", "mws-textinput")
                            ->setValue($ques_value[$i]);
                }



                if ($value['mandatory'])
                    $ques[$i]->setAttrib("class", "required");
            }


            if ($value['spec_type'] == '0') {   //if specification type is Radio
                $radioArr = $db->runQuery("select * from " . SPEC_CHILD . " where spec_id = '" . $value['spec_id'] . "' ");

                if ($ppty_id != "") { //condition when edit is performed
                    $spec_ansData = $db->runQuery("select * from  " . SPEC_ANS . " where spec_id = '" . $value['spec_id'] . "' and property_id = '" . $ppty_id . "' ");
                    //echo "select * from  ".SPEC_ANS." where spec_id = '".$value['spec_id']."' and property_id = '".$ppty_id."' ";
                    if ($spec_ansData[0]['answer'])
                        $ques_value[$i] = $spec_ansData[0]['answer'];
                    else
                        $ques_value[$i] = $radioArr[0]['cid'];
                }
                else {
                    $ques_value[$i] = $radioArr[0]['cid'];
                }



                $k = 1;

                $OptionsArr = array();
                foreach ($radioArr as $values) {
                    $OptionsArr[$k]['key'] = $values['cid'];
                    $OptionsArr[$k]['value'] = $values['option'];
                    $k++;
                }


                $quest = 'ques' . $i;
                $ques[$i] = new Zend_Form_Element_Radio($quest);
                $ques[$i]->addMultiOptions($OptionsArr)
                        ->setAttrib('label_style', '')
                        ->setAttrib('style', 'padding:5px;')
                        ->setAttrib("class", "mws-textinput")
                        ->setAttrib("minlength", "1")
                        ->setValue($ques_value[$i]);

                if ($value['mandatory'])
                    $ques[$i]->setAttrib("class", "required");
            }




            $i++;
        }

        for ($t = 0; $t < $i; $t++)
            $specification->addElement($ques[$t]);

        $step = new Zend_Form_Element_Hidden("step");
        $step->setValue("2");

        $specification->addElement($step);
        return $specification;
    }

    /**
     * Prepare a sub form for display
     *
     * @param  string|Zend_Form_SubForm $spec
     * @return Zend_Form_SubForm
     */
    public function prepareSubForm($spec) {
        if (is_string($spec)) {
            $subForm = $this->{$spec};
        } elseif ($spec instanceof Zend_Form_SubForm) {
            $subForm = $spec;
        } else {
            throw new Exception('Invalid argument passed to ' .
            __FUNCTION__ . '()');
        }
        return $subForm;
    }

}