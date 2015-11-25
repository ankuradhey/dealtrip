<?php
class Form_Specification extends Zend_Form
{
	public function __construct($specId="")
	{
		$this->init($specId);
	}
    public function init($specId)
	{   
		global $mySession;
        $db=new Db();
		
		$amenity_value=""; $description_value = "";$no_of_options_value = "";$mandatory_value = "";
		
		
		if($specId!="")
		{
			$specData=$db->runQuery("select * from ".SPECIFICATION." as s 
			                         left join ".SPEC_CHILD." as sc on	sc.spec_id = s.spec_id
									 inner join ".PROPERTY_SPEC_CAT." as psc on	psc.cat_id = s.cat_id
									 where s.spec_id ='".$specId."' ");
			

			$question_value = $specData[0]['question'];
			$input_type_value = $specData[0]['spec_type'];
			$spec_status_value = $specData[0]['status'];
			$category_value = $specData[0]['cat_id'];
 			$no_of_options_value = count($specData);
			$mandatory_value = $specData[0]['mandatory'];
			
			
			if($input_type_value == '1' || $input_type_value == '0' || $input_type_value == '4')
 			{
				$i = 1;
				foreach($specData as $value)
				{
					$options_value[$i] = $value['option'];
					$i++;
				}
				
			}
		}
		
			$categoryArr[0]['key'] = "";
			$categoryArr[0]['value'] = "- - Select - -";
		
		
			$catArr = $db->runQuery("select * from ".PROPERTY_SPEC_CAT."  ");
			$i=1;
			foreach($catArr as $values)
			{
				$categoryArr[$i]['key'] = $values['cat_id'];
				$categoryArr[$i]['value'] = $values['cat_name'];
				$i++;
			}
		
		
		
			$category =  new Zend_Form_Element_Select('category');
			$category->setRequired(true)
			->addMultiOptions($categoryArr)
			->addDecorator('Errors', array('class'=>'error'))
			->setAttrib("class","mws-textinput")
			->setValue($category_value);
			
			$question = new Zend_Form_Element_Text('question');
			$question->setRequired(true)
			->addValidator('NotEmpty',true,array('messages' =>'Question is required.'))
			->addDecorator('Errors', array('class'=>'error'))
			->setAttrib("class","mws-textinput required")	
			->setAttrib("tabindex",'1')
			->setValue($question_value);
			
			$typeArr[0]['key'] = "";
			$typeArr[0]['value'] = '- - Select --';
			$typeArr[1]['key'] = '0';
			$typeArr[1]['value'] = 'Radio';
			$typeArr[2]['key'] = '1';
			$typeArr[2]['value'] = 'Selectbox';
			$typeArr[3]['key'] = '2';
			$typeArr[3]['value'] = 'Textarea';
			$typeArr[4]['key'] = '3';
			$typeArr[4]['value'] = 'Textbox';
			$typeArr[5]['key'] = '4';
			$typeArr[5]['value'] = 'Checkbox';
			
			
			
			
			
			
			
			
			$input_type = new Zend_Form_Element_Select('input_type');
			$input_type->setRequired(true)
			->addMultiOptions($typeArr)
			->addDecorator('Errors', array('class'=>'error'))
			->setAttrib("class","mws-textinput")
			->setAttrib("onchange","displayoption(this.value)")				
			->setValue($input_type_value);
			
			
			/** no of options code **/
			
			$numberArr[0]['key'] = "";
			$numberArr[0]['value'] = "- - select - -";
				
				for($i = 1,$k = 2;$k <= 54;$i++,$k++)
				{
					$numberArr[$i]['key'] = $k;
					$numberArr[$i]['value'] = $k;
				}
				
			$no_of_options = new Zend_Form_Element_Select('no_of_options');
			$no_of_options->addMultiOptions($numberArr)
			->addDecorator('Errors', array('class'=>'error'))
			->setAttrib("class","mws-textinput required")
			->setAttrib("onchange","addoptions(this.value)")					
			->setValue($no_of_options_value);
			$this->addElement($no_of_options);
				
			
			
			/* options code **/
				for($j=1;$j<=54;$j++)
					{
	
						$options_add[$j] = 	new Zend_Form_Element_Text('options_add'.$j);
						$options_add[$j]->addDecorator('Errors', array('class'=>'error'))
						->setAttrib("class","mws-textinput required")	
						->setAttrib("tabindex",'1')
						->setValue($options_value[$j]);
							
						$this->addElement($options_add[$j]);
					}

			
			
			$statusArr[0]['key'] = '0';
			$statusArr[0]['value'] = 'Disable';
			$statusArr[1]['key'] = '1';
			$statusArr[1]['value'] = 'Enable';
			
			
			$spec_status = new Zend_Form_Element_Select('spec_status');
			$spec_status->addDecorator('Errors', array('class'=>'error'))
			->addMultiOptions($statusArr)
			->setAttrib("class","mws-textinput required")	
			->setValue($spec_status_value);
			
			
			$mandatoryArr[0]['key'] = '0';
			$mandatoryArr[0]['value'] = 'Not Mandatory';
			$mandatoryArr[1]['key'] = '1';
			$mandatoryArr[1]['value'] = 'Mandatory';
			
			$mandatory = new Zend_Form_Element_Radio('mandatory');
			$mandatory->addDecorator('Errors', array('class'=>'error'))
			->addMultiOptions($mandatoryArr)
			->setAttrib("class","mws-textinput required")	
			->setValue($mandatory_value);
			
			
			$this->addElements(array($question,$category,$input_type,$spec_status,$mandatory));
			
			
		
	}
}




?>