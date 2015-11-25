<?php

    class Form_Amenity extends Zend_Form
    {

        public function __construct($ptyleId = "")
        {
            $this->init($ptyleId);
        }

        public function init($ptyleId)
        {
            global $mySession;
            $db = new Db();

            $amenity_value = "";
            $description_value = "";
            $box1View_value = "";
            $box2View_value = "";
            $location_status = 0;

            //** array used when adding new amenity otherwise in editing next one will be overwritten **//		
            $locationData = $db->runQuery("select * from " . LOCAL_AREA . " ");
            $i = 1;
            foreach ($locationData as $value)
            {
                $locationArr[$i]['key'] = $value['local_area_id'];
                $locationArr[$i]['value'] = $value['local_area_name'];
                $i++;
            }

            //$box2ViewArr = "";


            if ($ptyleId != "")
            {
                $ptyleData = $db->runQuery("select * from " . AMENITY . " where amenity_id ='" . $ptyleId . "'");
                $amenity_value = $ptyleData[0]['title'];
                $description_value = $ptyleData[0]['description'];
                $amenity_status_value = $ptyleData[0]['amenity_status'];

                $locations = explode(",", $ptyleData[0]['location_view']);


            }

            $amenity_name = new Zend_Form_Element_Text('amenity_name');
            $amenity_name->setRequired(true)
                    ->addValidator('NotEmpty', true, array('messages' => 'Amenity Name is required.'))
                    ->addDecorator('Errors', array('class' => 'error'))
                    ->setAttrib("class", "mws-textinput required")
                    ->setAttrib("maxlength", '65')
                    ->setAttrib("minlength", "3")
                    ->setAttrib("tabindex", '1')
                    ->setValue($amenity_value);

            $description = new Zend_Form_Element_Textarea('description');
            $description->addDecorator('Errors', array('class' => 'error'))
                    ->setAttrib("class", "mws-textinput")
                    ->setAttrib("maxlength", '100')
                    ->setValue($description_value);


            $statusArr[0]['key'] = '0';
            $statusArr[0]['value'] = 'Disable';
            $statusArr[1]['key'] = '1';
            $statusArr[1]['value'] = 'Enable';


            $amenity_status = new Zend_Form_Element_Select('amenity_status');
            $amenity_status->addDecorator('Errors', array('class' => 'error'))
                    ->addMultiOptions($statusArr)
                    ->setAttrib("class", "mws-textinput required")
                    ->setValue($amenity_status_value);



            $this->addElements(array($amenity_name, $description, $amenity_status));
        }

    }

?>