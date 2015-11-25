<?php

class Form_SpecificationUpdater extends Zend_Form {

    public function __construct($ppty_id = "") {
        $this->init($ppty_id);
    }
    
    public function init($ppty_id) {
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
                    for ($x = 0; $x < 1; $x++) {

//                        $ques_value_bath = explode("|||", $ques_value[$now][$x]);

                        $quest = 'ques' . $i . $x;
                        $ques[$i] = new Zend_Form_Element_MultiCheckbox($quest);
                        $ques[$i]->addMultiOptions($OptionsArr)
                                ->setAttrib("class", "mws-textinput");
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
                    
                    for ($x = 0; $x < 1; $x++) {


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
            $this->addElement($ques[$t]);

        $step = new Zend_Form_Element_Hidden("step");
        $step->setValue("2");

        $this->addElement($step);
    }
}