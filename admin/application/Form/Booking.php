<?PHP

class Form_Booking extends Zend_Form {

    public function __construct($pptyId, $bookId = "", $dateFrom = "", $dateTo = "") {
        global $mySession;
        $this->init($pptyId, $bookId, $dateFrom, $dateTo);
    }

    public function init($pptyId, $bookId = "", $dateFrom, $dateTo) {
        global $mySession;
        $db = new Db();


        $full_name_value = "";
        $location_value = "";
        $check_in_value = "";
        $rating_value = "";
        $headline_value = "";
        $comment_value = "";
        $review_value = "";


        $pptyArr = $db->runQuery("select * from " . PROPERTY . " 
									  inner join " . COUNTRIES . " on " . COUNTRIES . ".country_id = " . PROPERTY . ".country_id
									  inner join " . PROPERTY_TYPE . " on " . PROPERTY . ".property_type = " . PROPERTY_TYPE . ".ptyle_id
									  left join " . STATE . "  on " . STATE . ".state_id = " . PROPERTY . ".state_id
         							  left join " . CITIES . " on " . CITIES . ".city_id = " . PROPERTY . ".city_id
									  left join " . SUB_AREA . " on " . SUB_AREA . ".sub_area_id = " . PROPERTY . ".sub_area_id
									  left join " . LOCAL_AREA . " on " . LOCAL_AREA . ".local_area_id = " . PROPERTY . ".local_area_id
									  where " . PROPERTY . ".id = '" . $pptyId . "'");

        
        
        $propertyCode = new Zend_Form_Element_Text('propertyCode');
        $propertyCode
                ->setAttrib("placeholder", "Please enter")
                ->setAttrib("class", "mws-textinput")
                ->setAttrib("onblur", "addMembersCount(this.value)")
            ;
        $this->addElement($propertyCode);
        
        
        
        if ($dateFrom != "")
            $date_from_value = $dateFrom;

        $partySize = new Zend_Form_Element_Hidden('partySize');
        $partySize->setRequired(true)
                ->setValue($pptyArr[0]['maximum_occupancy']);
        $this->addElement($partySize);


        $adultsArr = array();


        $adultsArr[0]['key'] = "";
        $adultsArr[0]['value'] = "Please Select";
        for ($i = 1; $i <= $pptyArr[0]['maximum_occupancy']; $i++) {
            $adultsArr[$i]['key'] = $i;
            $adultsArr[$i]['value'] = $i;
        }

        $Adults = new Zend_Form_Element_Select('Adults');
        $Adults->setRequired(true)
                ->addMultiOptions($adultsArr)
                ->setAttrib("class", "mws-textinput required")
                ->setAttrib("min", "1")
                ->setValue($adults_value);
        $this->addElement($Adults);


        $childArr = array();
        $childArr[0]['key'] = "";
        $childArr[0]['value'] = "Please Select";

        for ($i = 0, $j = 1; $i <= $pptyArr[0]['maximum_occupancy']; $i++, $j++) {
            $childArr[$j]['key'] = $i;
            $childArr[$j]['value'] = $i;
        }

        $Children = new Zend_Form_Element_Select('Children');
        $Children->setRequired(true)
                ->addMultiOptions($childArr)
                ->setAttrib("class", "mws-textinput required")
                ->setValue($childs_value);
        $this->addElement($Children);


        $infantArr = array();
        $infantArr[$i]['key'] = "";
        $infantArr[$i]['value'] = "Please Select";


        for ($i = 0; $i <= $pptyArr[0]['maximum_occupancy']; $i++) {
            $infantArr[$i]['key'] = $i;
            $infantArr[$i]['value'] = $i;
        }

        $Infants = new Zend_Form_Element_Select('Infants');
        $Infants->setRequired(true)
                ->addMultiOptions($infantArr)
                ->setAttrib("class", "mws-textinput required")
                ->setValue($infants_value);
        $this->addElement($Infants);

        $date_from_value = $mySession->arrivalDate != "" ? $mySession->arrivalDate : $date_from_value;

        $date_from = new Zend_Form_Element_Text('date_from');
        $date_from->setRequired(true)
                ->addValidator('NotEmpty', true, array('messages' => "Please enter check in date"))
                ->addDecorator('Errors', array('class' => 'error'))
                ->setAttrib("onchange", "departureDate();")
                ->setAttrib("class", "mws-textinput required mws-datepicker")
                ->setAttrib("placeholder", "Please Select")
//		->setAttrib("style","height:20px;")		
                ->setValue($date_from_value ? date('d-m-Y', strtotime($date_from_value)) : "");
        $this->addElement($date_from);


        $date_to_value = $mySession->noOfNights ? $mySession->noOfNights : "";

        $date_to_value = $dateTo != '' ? $dateTo : $date_to_value;

        $departureDates = new Zend_Form_Element_Text('departureDates');
        $departureDates->setValue($date_from_value ? date('d-m-Y', strtotime($date_from_value . ' + ' . ($date_to_value) . ' day')) : "")
                ->setAttrib("readonly", "readonly")
                ->setAttrib("placeholder", "Please Select")
                ->setAttrib("class", "mws-textinput");
//		->setAttrib("style","height:20px;");

        $this->addElement($departureDates);


        $datetoArr = array();

        $datetoArr[6]['key'] = "";
        $datetoArr[6]['value'] = "Please Select";

        for ($i = 7; $i <= 60; $i++) {
            $datetoArr[$i]['key'] = $i;
            $datetoArr[$i]['value'] = $i;
        }


        $date_to = new Zend_Form_Element_Select('date_to');
        $date_to->setRequired(true)
                ->addMultiOptions($datetoArr)
                ->setAttrib("onchange", "departureDate();")
                ->setAttrib("class", "mws-textinput required checkAvailability")
                ->setValue($date_to_value);
        $this->addElement($date_to);

        /* $check = new Zend_Form_Element_Hidden('check');
          $check->setRequired(true)
          ->setValue($text); */
    }

}

?>