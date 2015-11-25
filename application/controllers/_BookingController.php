<?php
__autoloadDB('Db');
$db = new Db();
class BookingController extends AppController
{
	#----------------------------------------------------------------#
	#  Index action function                                         #
	#----------------------------------------------------------------#		
	public $db;
	public $ppty;
	public $pptyArr = "";
	public $dateFrom, $dateTo;
	public $minrate = "";
	
	public function core()
	{
		$db = new Db();
		//$this->db = $db;
		$ppty = $this->getRequest()->getParam("ppty");
		
		
		
		$this->view->ppty = $ppty;
		$this->ppty = $ppty;
		

		
		$pptyArr = $db->runQuery("select * from ".PROPERTY." 
							  inner join ".COUNTRIES." on ".COUNTRIES.".country_id = ".PROPERTY.".country_id
							  inner join ".PROPERTY_TYPE." on ".PROPERTY.".property_type = ".PROPERTY_TYPE.".ptyle_id
							  left join ".STATE."  on ".STATE.".state_id = ".PROPERTY.".state_id
							  left join ".CITIES." on ".CITIES.".city_id = ".PROPERTY.".city_id
							  left join ".SUB_AREA." on ".SUB_AREA.".sub_area_id = ".PROPERTY.".sub_area_id
							  left join ".LOCAL_AREA." on ".LOCAL_AREA.".local_area_id = ".PROPERTY.".local_area_id
							  where ".PROPERTY.".id = '".$ppty."'");
							  
		$this->pptyArr = $pptyArr;
		$this->view->propertyData = $pptyArr;
		
	}

	public function indexAction()
	{
				global $mySession;
				$db=new Db();
				
				$ppty = $this->getRequest()->getParam("ppty");
				$step = $this->getRequest()->getParam("step");
				
				$dateFrom = $this->getRequest()->getParam("datefrom");
				$dateTo = $this->getRequest()->getParam("dateto");
				
				if($dateTo != "" && $step == '1' )
				{	
					__bookSessionClear();
					$mySession->arrivalDate = $dateFrom;
					$mySession->departureDate = $dateFrom?date('d-m-Y',strtotime($dateFrom." + $dateTo day")):"";
					if($dateFrom && $dateTo)
					{	
						$dateTo -= 1;
						$amt = new Booking();
						$mySession->totalCost = $amt->checkAmount(date('Y-m-d',strtotime($dateFrom)),date('Y-m-d',strtotime($dateFrom." + $dateTo day")), $dateTo, $ppty);	
						
					}
					$mySession->noOfNights = $dateFrom?$dateTo+1:"";
					$dateTo += 1;
				}
				
				$this->core();
			
				if($step == "")
				$step = $mySession->steps;
				else
				$mySession->steps = $step;
				

				
				switch($step)
				{
					case '':
					case '1':	if($dateFrom && $dateTo)
								$myform = new Form_Booking($ppty,"",$dateFrom,$dateTo);
								else
								$myform = new Form_Booking($ppty,"",$dateFrom,"");
								
								$this->view->step = '1';
								break;
					case '2':   $myform=new Form_User();
								$this->view->step = '2';
								break;
					case '3': 	if($this->getRequest()->isPost())
								{	
									if($_REQUEST['emailAddress'] != "")
									{
										$userArr = $db->runQuery("select * from ".USERS." where email_address = '".$_REQUEST['emailAddress']."' ");
										$mySession->bookingUser = $userArr[0];
									}
								}
								
								if(isLogged() && !isset($mySession->bookingUser) && !is_array($mySession->bookingUser))
								$mySession->bookingUser = $mySession->LoggedUser;
								
								$extras = $db->runQuery("select ".EXTRAS.".property_id, eid, ename, etype, stay_type, round(eprice*exchange_rate) as eprice from ".EXTRAS." 
														 inner join ".PROPERTY." on ".PROPERTY.".id = ".EXTRAS.".property_id
														 inner join ".CURRENCY." on ".PROPERTY.".currency_code = ".CURRENCY.".currency_code
														 where ".EXTRAS.".property_id = '".$this->ppty."' ");
								
								foreach($mySession->spclOfferId as $val)
								{	
									
									$spclOfferArr = $db->runQuery("select *, ".SPCL_OFFER_TYPES.".min_nights as MIN_NIGHTS from ".SPCL_OFFERS." inner join ".SPCL_OFFER_TYPES." on ".SPCL_OFFERS.".offer_id=".SPCL_OFFER_TYPES.".id 
															where ".SPCL_OFFERS.".spcl_offer_id = '".$val."' order by ".SPCL_OFFERS.".spcl_offer_id desc");
									$spclOfferData[] = $spclOfferArr[0]; 
								
								}
								
								$this->view->spclOfferArr = $spclOfferData;
								$this->view->extras = $extras;
								$this->view->step = '3';
								
								
								#-------------------------------------------------------------------------------------------#
								#                                   finding min rate starts								    #
								#-------------------------------------------------------------------------------------------#
								$datefrom = date('Y-m-d',strtotime($mySession->arrivalDate));
								$dateto = date('Y-m-d',strtotime( $datefrom.' + '.($mySession->noOfNights-1).' day'));
							
								$minrate = $db->runQuery("select round(min(prate)*exchange_rate) as PRATE from ".CAL_RATE." 
													  inner join ".PROPERTY." on ".PROPERTY.".id = ".CAL_RATE.".property_id
  													  inner join ".CURRENCY." on ".PROPERTY.".currency_code = ".CURRENCY.".currency_code
													  
								where property_id = '".$ppty."'  
								  and 
								  ( 
									(".CAL_RATE.".date_from >= '".$datefrom."' and ".CAL_RATE.".date_to <= '".$dateto."')
									or
									(".CAL_RATE.".date_from <= '".$datefrom."' and ".CAL_RATE.".date_to >= '".$dateto."') 	
									or
									(".CAL_RATE.".date_from <= '".$datefrom."' and ".CAL_RATE.".date_to >= '".$datefrom."' and ".CAL_RATE.".date_to <= '".$dateto."' )
									or
									(".CAL_RATE.".date_to >= '".$dateto."' and ".CAL_RATE.".date_from >= '".$datefrom."' and ".CAL_RATE.".date_from <= '".$dateto."')
								  )	
								");
								$mySession->minrate = $this->minrate = $this->view->minrate = $minrate[0]['PRATE'];
								#-------------------------------------------------------------------------------------------#
								#                                   finding min rate ends 									#
								#-------------------------------------------------------------------------------------------#
								break;
				case '4':   $this->view->step = '4';
							$mySession->extrasId = array();
							if($this->getRequest()->isPost())
							{	
									foreach($_REQUEST['check'] as $values)
									{
										$mySession->extrasId[] = 	$values;
		
									}
							}
							
//							$mySession->extraId = $extrasArr;

							#-------------------------------------------------------------------------------------------#
							#                                   finding min rate starts									#
							#-------------------------------------------------------------------------------------------#
							$datefrom = date('Y-m-d',strtotime($mySession->arrivalDate));
							$dateto = date('Y-m-d',strtotime( $datefrom.' + '.($mySession->noOfNights-1).' day'));
							
							$minrate = $db->runQuery("select round(min(prate)*exchange_rate) as PRATE from ".CAL_RATE." 
													  inner join ".PROPERTY." on ".PROPERTY.".id = ".CAL_RATE.".property_id
  													  inner join ".CURRENCY." on ".PROPERTY.".currency_code = ".CURRENCY.".currency_code
													  
							where property_id = '".$ppty."'  
							  and 
							  ( 
							  	(".CAL_RATE.".date_from >= '".$datefrom."' and ".CAL_RATE.".date_to <= '".$dateto."')
								or
								(".CAL_RATE.".date_from <= '".$datefrom."' and ".CAL_RATE.".date_to >= '".$dateto."') 	
								or
								(".CAL_RATE.".date_from <= '".$datefrom."' and ".CAL_RATE.".date_to >= '".$datefrom."' and ".CAL_RATE.".date_to <= '".$dateto."' )
								or
								(".CAL_RATE.".date_to >= '".$dateto."' and ".CAL_RATE.".date_from >= '".$datefrom."' and ".CAL_RATE.".date_from <= '".$dateto."')
							  )	
							");
							
							#-------------------------------------------------------------------------------------------#
							#                                   finding min rate ends									#
							#-------------------------------------------------------------------------------------------#
							
							foreach($mySession->spclOfferId as $val)
							{	
									
									$spclOfferArr = $db->runQuery("select *, ".SPCL_OFFER_TYPES.".min_nights as MIN_NIGHTS from ".SPCL_OFFERS." inner join ".SPCL_OFFER_TYPES." on ".SPCL_OFFERS.".offer_id=".SPCL_OFFER_TYPES.".id 
															where ".SPCL_OFFERS.".spcl_offer_id = '".$val."' order by ".SPCL_OFFERS.".spcl_offer_id desc");
									$spclOfferData[] = $spclOfferArr[0]; 
								
							}
								
							$this->view->spclOfferArr = $spclOfferData;
							$mySession->minrate = $this->minrate = $this->view->minrate = $minrate[0]['PRATE'];
							break;
					
				}
			
				
				$this->view->myform = $myform;			
				

				
	}
	
	public function processAction()
	{
		global $mySession;
		$db=new Db();
		$step = $this->getRequest()->getParam("step");
		$this->core();
		$myform = new Form_User();
		
		if($this->getRequest()->isPost())
		{
			$request=$this->getRequest();
			if ($myform->isValid($request->getPost()))
			{	
				$dataForm=$myform->getValues();
				//prd($dataForm);
				$sql = "select * from ".USERS." where email_address = '".trim($dataForm['email_address'])."'";
				$chkArr = $db->runQuery($sql);
				
				if(count($chkArr) == 0)
				{
					$myObj = new Users();
					$Result=$myObj->SaveUser($dataForm, 1);
					$mySession->steps = '3';
					$mySession->sucessMsg = "Successfully registered";
					
/*					$mySession->LoggedUser = $dataForm*/
					$userArr = $db->runQuery("select * from ".USERS." where user_id = '".$Result."' ");
					$mySession->bookingUser = $userArr[0];
					$this->_redirect("booking/index/ppty/".$this->ppty."/step/3");
				}
				else
				{
					$mySession->errorMsg = "This username or email address already exist";	
					$this->render("index");	
				}
									
			}
			else
			{
				$mySession->errorMsg = "Oops! Some error occurred while registering your account";	
                                $this->_redirect("booking/index/ppty/".$this->ppty."/step/1");
                                
                        }
			
		}
		
		$this->view->myform=$myform;
		
	}
	
	
	public function processbookAction()
	{
		global $mySession;
		$db=new Db();
		$dataForm=array();
		$dataextraForm=array();
		
		$this->core();
									  
		$myform = new Form_Booking($this->ppty);
		
		
		
		$this->view->myform = $myform;
		
		$request = $this->getRequest();
		
		if($this->getRequest()->isPost() && isset($_REQUEST['agree']))
		{
			
			if ($myform->isValid($request->getPost()))
			{
				$book = new Booking();
				
				$dataForm = $myform->getValues();
				$result = $book->checkAvailablity($dataForm, $this->ppty);
				
				
				if($result === 1)
				{
					$this->_redirect("booking/index/step/2/ppty/".$this->ppty);
				}
				else
				{
					$mySession->errorMsg = $result;
					$this->render("index");
				}
			}
			else
			{
				$this->_redirect("booking/index/step/2/ppty/".$this->ppty);
			}
		}
		else
		$this->_redirect("booking/index/ppty/".$this->ppty);
		
	}
		
		
		/*if($dataForm['booking_type']==0)
		{
			$db->save(CAL_AVAIL,$databookingForm);
		}
		$db->save(BOOKING,$dataForm);
		
		$dataextraForm['booking_id']=$db->lastInsertId();
		
		$option_complusory=$this->getRequest()->getParam("option_complusory");
		$option_extra=$this->getRequest()->getParam("option_extra");
		if($option_complusory>0)
		{
			for($i=1;$i<=$option_complusory;$i++)
			{
				$dataextraForm['option_name']=$this->getRequest()->getParam("option_complusory_ename_".$i);
				$dataextraForm['option_price']=$this->getRequest()->getParam("option_complusory_eprice_".$i);
				$dataextraForm['option_status']='1';
				//prd($dataextraForm);
				$db->save(BOOKING_EXTRA,$dataextraForm);
			}
		}
		if($option_extra>0)
		{
			for($i=1;$i<=$option_extra;$i++)
			{
				if($this->getRequest()->getParam("option_extra_eprice_".$i)>0)
				{
					$dataextraForm['option_name']=$this->getRequest()->getParam("option_extra_ename_".$i);
					$dataextraForm['option_price']=$this->getRequest()->getParam("option_extra_eprice_".$i);
					$dataextraForm['option_status']='0';
					$db->save(BOOKING_EXTRA,$dataextraForm);
				}
			}
		}
		
		$this->view->booking_type=$dataForm['booking_type'];		*/
		
		
	public function checkvaliduserAction()
	{
		global $mySession;
		$db = new Db();
		$email = $_REQUEST['email'];
		$chkuser = $db->runQuery("select * from ".USERS." where email_address = '".trim($email)."' and uType = '1' ")	;
		if($chkuser != "" && count($chkuser)>0)
		{
			/*$mySession->LoggedUser = $chkuser;*/
			echo 1;
			exit(1);
		}
		else
		{
			echo 0;
			exit(0);		
		}

	}

	
	public function getvoucherAction()
	{
		global $mySession;
		$db=new Db();
			
		$this->_helper->layout()->disableLayout();
			
		$ppty = $_REQUEST['ppty'];
		$code = $_REQUEST['code'];
		
		$dateTo = $mySession->noOfNights-1;
		
		//echo $mySession->arrivalDate;
		
		$dateFrom = date('Y-m-d',strtotime($mySession->arrivalDate));
		$dateTo = date('Y-m-d', strtotime($dateFrom." + $dateTo day"));
		
/*		
	echo "select * from ".SPCL_OFFERS." inner join ".SPCL_OFFER_TYPES." on ".SPCL_OFFERS.".offer_id=".SPCL_OFFER_TYPES.".id 
								where ".SPCL_OFFER_TYPES.".promo_code='".trim($code)."' and ".SPCL_OFFERS.".activate='1' 
								and  ".SPCL_OFFERS.".property_id='".$ppty."' 
								and ".SPCL_OFFERS.".valid_from <='".$dateFrom."' 
								and  ".SPCL_OFFERS.".valid_to >='".$dateTo."'
								and  case 
											when ".SPCL_OFFER_TYPES.".min_nights_type = '1' 
											then ".SPCL_OFFER_TYPES.".min_nights <= '".$mySession->noOfNights."'
											else ".SPCL_OFFERS.".min_night <= '".$mySession->noOfNights."' end
								and  ".SPCL_OFFERS.".book_by >= NOW()
								order by ".SPCL_OFFERS.".spcl_offer_id desc"; exit;	*/
		
		
		if(count($mySession->spclOfferId) > 0)
		{
				exit("already");
		}
		
		
		$special = $db->runQuery("select * from ".SPCL_OFFERS." inner join ".SPCL_OFFER_TYPES." on ".SPCL_OFFERS.".offer_id=".SPCL_OFFER_TYPES.".id 
								where ".SPCL_OFFER_TYPES.".promo_code='".trim($code)."' and ".SPCL_OFFERS.".activate='1' 
								and  ".SPCL_OFFERS.".property_id='".$ppty."' 
								and ".SPCL_OFFERS.".valid_from <='".$dateFrom."' 
								and  ".SPCL_OFFERS.".valid_to >='".$dateTo."'
								and  case 
											when ".SPCL_OFFER_TYPES.".min_nights_type = '1' 
											then ".SPCL_OFFER_TYPES.".min_nights <= '".$mySession->noOfNights."'
											else ".SPCL_OFFERS.".min_night <= '".$mySession->noOfNights."' end
								and  ".SPCL_OFFERS.".book_by >= curdate()
								order by ".SPCL_OFFERS.".spcl_offer_id desc");
			
								
		if(count($special)>0)
		{
			//echo "discount_type|||".$special[0]['discount_type']."***discount_offer|||".$special[0]['discount_offer']."***type_name|||".$special[0]['type_name'];
			if($mySession->spclOfferId[count($mySession->spclOfferId)-1] != $special[0]['spcl_offer_id'])
			$mySession->spclOfferId[count($mySession->spclOfferId)] = $special[0]['spcl_offer_id'];
			
			$spclOfferArr = $db->runQuery("select *, ".SPCL_OFFER_TYPES.".min_nights as MIN_NIGHTS from ".SPCL_OFFERS." 
										   inner join ".SPCL_OFFER_TYPES." on ".SPCL_OFFERS.".offer_id=".SPCL_OFFER_TYPES.".id 
  										   where ".SPCL_OFFERS.".spcl_offer_id = '".$special[0]['spcl_offer_id']."' order by ".SPCL_OFFERS.".spcl_offer_id desc");
			
			?>
			 <div class="bodies" style="display:inline-block;width:100%;">
                                       <div class ="name" align="center" style="width:373px;">
                                			<?=$spclOfferArr[0]['type_name']?>
		                               </div>
                                       <div class="option" align="center">
                                           <? 
										   	switch($spclOfferArr[0]['discount_type'])
											  {
												case '0':   echo "(".$spclOfferArr[0]['promo_code'].") ".$spclOfferArr[0]['discount_offer']."%" ; break;
												case '1':	echo "(".$spclOfferArr[0]['promo_code'].") ".$this->minrate." x ".$spclOfferArr[0]['discount_offer']; break;
												case '2':	echo "(".$spclOfferArr[0]['promo_code'].") "."Free Pool Heating"; break;
												case '3':	echo "(".$spclOfferArr[0]['promo_code'].") ".$spclOfferArr[0]['id']=='6'?"33.3%":"25%"; break;
											}
											?>
                                       </div>
               </div>
			<?
		}
		else
		{
			echo "none";
		}
		exit();
	}
	
	
	public function afterpayAction()
	{
		global $mySession;
		$db = new Db();
		
		$return = $this->getRequest()->getParam("Return");
		
		
	/*	prd($_GET);*/
		if($return == '1')
		{
		
		$spclOffer = implode(",",$mySession->spclOfferId);
		//fetching the compulsory extras
		/*$extrasArr = $db->runQuery("select eid from ".EXTRAS." where property_id = '".$mySession->pptyId."' and etype = '1' ");
	
		
		$extras = implode(",",$mySession->extrasId);*/
		
		foreach($extrasArr as $val)
		$extras[] = $val['edi'];
		
		// saving in Booking table
		//$dataForm['property_id'] = $mySession->pptyId;
                //prd($mySession->bookingUser);
                
		$dataForm['property_id'] = $mySession->bookingUser['property_id'];
		$dataForm['user_id'] = $mySession->bookingUser['user_id'];
		$dataForm['date_from'] = date('Y-m-d',strtotime($mySession->arrivalDate));
		$dataForm['date_to'] = date('Y-m-d',strtotime($mySession->arrivalDate." + $mySession->noOfNights day"));
		$dataForm['offer_id'] = $spclOffer;
		$dataForm['children'] = $mySession->Children;
		$dataForm['adult'] = $mySession->Adults;
		$dataForm['infants'] = $mySession->Infants;
		$dataForm['total'] =  $mySession->finalAmt;
		$dataForm['min_rate'] = $mySession->minrate;
//		$dataForm['booking_date'] = $mySession->Infants;
		$dataForm['rental_amt'] = round($mySession->totalCost);
		$dataForm['booking_date'] = date('Y-m-d');
		$dataForm['booking_type'] = '0';  //available booked status
		if($mySession->paymentType == '1')
		$dataForm['paid_status'] = '2'; //full payment
		else
		$dataForm['paid_status'] = '1';	// advance payment			
		
		$db->save(BOOKING,$dataForm);
		
                
                
		$bookingId = $db->lastInsertId();
		
		
		//code to save data in calendar table
		
		
		
		//saving in payment list
		
                
                //pr($mySession->pptyId);
                //pr($mySession->bookingUser);
		$dataForm = array();
		$dataForm['user_id'] = $mySession->bookingUser['user_id'];
		//$dataForm['property_id'] = $mySession->pptyId;
		$dataForm['property_id'] = $mySession->bookingUser['property_id'];
		//$dataForm['trans_id'] = $_REQUEST['txn_id'];
		$dataForm['amount_paid'] = $mySession->finalAmt;
		$dataForm['booking_id'] = $bookingId;
		$dataForm['payment_date'] = date('Y-m-d');
		$db->save(PAYMENT,$dataForm);
		
                //prd("lets see");
		
		//saving in the booking extra table
		foreach($mySession->extrasId as $values)
		{
			$extrasArr = $db->runQuery("select ename,eprice*exchange_rate as eprice,etype,stay_type from  ".EXTRAS." 
			                            inner join ".PROPERTY." on ".PROPERTY.".id = ".EXTRAS.".property_id
										inner join ".CURRENCY." on ".CURRENCY.".currency_code = ".PROPERTY.".currency_code
										where eid = '".$values."'	 ");
			$dataForm = array();
			$dataForm['booking_id'] = $bookingId;
			$dataForm['option_name'] = $extrasArr[0]['ename'];
			$dataForm['option_price'] = $extrasArr[0]['eprice'];
			$dataForm['option_status'] = $extrasArr[0]['etype'];
			$dataForm['stay_type'] = $extrasArr[0]['stay_type'];
			$db->save(BOOKING_EXTRA,$dataForm);
		}
		
//		$extrasArr = $db->runQuery("select ename,eprice*exchange_rate as eprice,etype,stay_type from  ".EXTRAS." 
//									inner join ".PROPERTY." on ".PROPERTY.".id = ".EXTRAS.".property_id
//   								    inner join ".CURRENCY." on ".CURRENCY.".currency_code = ".PROPERTY.".currency_code
//									where property_id = '".$mySession->pptyId."' and etype = '1' ");
//		
		$extrasArr = $db->runQuery("select ename,eprice*exchange_rate as eprice,etype,stay_type from  ".EXTRAS." 
									inner join ".PROPERTY." on ".PROPERTY.".id = ".EXTRAS.".property_id
   								    inner join ".CURRENCY." on ".CURRENCY.".currency_code = ".PROPERTY.".currency_code
									where property_id = '".$mySession->bookingUser['property_id']."' and etype = '1' ");
		
		//saving for the compulosry extras table
		foreach($extrasArr as $values)
		{
			$dataForm = array();
			$dataForm['booking_id'] = $bookingId;
			$dataForm['option_name'] = $values['ename'];
			$dataForm['option_price'] = $values['eprice'];
			$dataForm['option_status'] = $values['etype'];
			$dataForm['stay_type'] = $values['stay_type'];
			$db->save(BOOKING_EXTRA,$dataForm);
		}
		
		$mySession->sucessMsg = "Thank you.. you have sucessfully booked the property.";
		
		
		//$pptyno = $db->runQuery("select propertycode from  ".PROPERTY." where id = '".$mySession->pptyId."' ");
		$pptyno = $db->runQuery("select propertycode from  ".PROPERTY." where id = '".$mySession->bookingUser['property_id']."' ");
		
		
		$fullName= $mySession->LoggedUserName;

		$Url='<a href="'.APPLICATION_URL.'">'.APPLICATION_URL.'</a>';
		$templateData = $db->runQuery("select * from ".EMAIL_TEMPLATES." where template_id='7'");
		$messageText=$templateData[0]['email_body'];
		$subject=$templateData[0]['email_subject'];
		
		$messageText=str_replace("[NAME]",$fullName,$messageText);
		$messageText=str_replace("[SITENAME]",SITE_NAME,$messageText);
		$messageText=str_replace("[SITEURL]",APPLICATION_URL,$messageText);
		$messageText=str_replace("[PROPERTYNO]",$pptyno[0]['propertycode'],$messageText);
		SendEmail($dataForm['email_address'],$subject,$messageText);
		
		__bookSessionClear();
		
		
		$this->_redirect("contents/pages/slug/bookingsucess");
		}
		else
		{
			$mySession->errorMsg = "We are sorry for the misconviniece <br />
									 Error while booking property"	;
			 __bookSessionClear();
			$this->_redirect(APPLICATION_URL);
		}
		
		
	}
	
	public function sucessAction()
	{	
		global $mySession;
		
	
	}
	
	
	public function beforepayAction()
	{
		global $mySession;
		$db = new Db();
		
		$request = $this->getRequest();
		//prd($_REQUEST);
                $ppty_id = $this->_getParam('ppty');
                
                if(!empty($ppty_id))
                $mySession->bookingUser['property_id'] = $ppty_id;
                
		if($this->getRequest()->isPost())
		{
			$dataForm = $_REQUEST;
			
			if($_REQUEST['payment_type'] == '1') //full payment
			{
				$dataForm['amount'] = $_REQUEST['full_pay_amount']	;
				$mySession->finalAmt = $dataForm['amount'];
				$mySession->paymentType = '1';
			}
			else
			{
				$dataForm['amount'] = $_REQUEST['adv_pay_amount'];
				$mySession->finalAmt = $dataForm['amount'];				
				$mySession->paymentType = '2';				
			}
                        $dataForm['ppty_id'] = $ppty_id;
                        
			$this->view->paypalDetails = $dataForm;
		}
		
	}
	
	public function onrequestpayAction()
	{
		global $mySession;
		$db = new Db();	
		
		$spclOffer = implode(",",$mySession->spclOfferId);
		$extras = implode(",",$mySession->extrasId);
	
		//$dataForm['property_id'] = $mySession->pptyId;
		$dataForm['property_id'] = $mySession->bookingUser['property_id'];
		$dataForm['user_id'] = $mySession->bookingUser['user_id'];
		$dataForm['date_from'] = date('Y-m-d',strtotime($mySession->arrivalDate));
		$dataForm['date_to'] = date('Y-m-d',strtotime($mySession->arrivalDate." + ".$mySession->noOfNights." day"));
		$dataForm['offer_id'] = $spclOffer;
		$dataForm['min_rate'] = $mySession->minrate;
		//$dataForm['extras_id'] = $extras;
		$dataForm['children'] = $mySession->Children;
		$dataForm['adult'] = $mySession->Adults;
		$dataForm['infants'] = $mySession->Infants;
		$dataForm['total'] = $mySession->Infants;
		$dataForm['rental_amt'] = $mySession->totalCost;
		$dataForm['booking_date'] = date('Y-m-d');
		$dataForm['booking_type'] = '1';
		$dataForm['paid_status'] = '0';
	
		$db->save(BOOKING,$dataForm);
		
		$bookingId = $db->lastInsertId();

		//code to save data in calendar table
		
		/*$dataForm = array();
		
		$dataForm['property_id'] = $mySession->pptyId;
		$dataForm['date_from'] = date('Y-m-d',strtotime($mySession->arrivalDate));
		$dataForm['date_to'] = date('Y-m-d',strtotime($mySession->arrivalDate." + ".$mySession->noOfNights." day"));
		$dataForm['cal_status'] = '0';
		
		save_calendar_stat($mySession->pptyId,$dataForm['date_from'],$dataForm['date_to']);*/
		
		//saving in the booking extra table
		foreach($mySession->extrasId as $values)
		{
			$extrasArr = $db->runQuery("select ename,eprice*exchange_rate as eprice,etype,stay_type from  ".EXTRAS." 
			                            inner join ".PROPERTY." on ".PROPERTY.".id = ".EXTRAS.".property_id
										inner join ".CURRENCY." on ".CURRENCY.".currency_code = ".PROPERTY.".currency_code
										where eid = '".$values."'	 ");
										
			$dataForm = array();
			$dataForm['booking_id'] = $bookingId;
			$dataForm['option_name'] = $extrasArr[0]['ename'];
			$dataForm['option_price'] = $extrasArr[0]['eprice'];
			$dataForm['option_status'] = $extrasArr[0]['etype'];
			$dataForm['stay_type'] = $extrasArr[0]['stay_type'];
			$db->save(BOOKING_EXTRA,$dataForm);
		}
		
//		$extrasArr = $db->runQuery("select ename,eprice*exchange_rate as eprice,etype,stay_type from  ".EXTRAS." 
//									inner join ".PROPERTY." on ".PROPERTY.".id = ".EXTRAS.".property_id
//   								    inner join ".CURRENCY." on ".CURRENCY.".currency_code = ".PROPERTY.".currency_code
//									where property_id = '".$mySession->pptyId."' and etype = '1' ");
		$extrasArr = $db->runQuery("select ename,eprice*exchange_rate as eprice,etype,stay_type from  ".EXTRAS." 
									inner join ".PROPERTY." on ".PROPERTY.".id = ".EXTRAS.".property_id
   								    inner join ".CURRENCY." on ".CURRENCY.".currency_code = ".PROPERTY.".currency_code
									where property_id = '".$mySession->bookingUser['property_id']."' and etype = '1' ");
		
		//saving for the compulosry extras table
		foreach($extrasArr as $values)
		{
			$dataForm = array();
			$dataForm['booking_id'] = $bookingId;
			$dataForm['option_name'] = $values['ename'];
			$dataForm['option_price'] = $values['eprice'];
			$dataForm['option_status'] = $values['etype'];
			$dataForm['stay_type'] = $values['stay_type'];
			$db->save(BOOKING_EXTRA,$dataForm);
		}
		
		
		
		$mySession->sucessMsg = "Thank you.. Property has been Booked Successfully";
		
		$pptyno = $db->runQuery("select propertycode from  ".PROPERTY." where id = '".$mySession->pptyId."' ");
		
		$fullName= $mySession->LoggedUserName;

		$Url='<a href="'.APPLICATION_URL.'">'.APPLICATION_URL.'</a>';
		$templateData = $db->runQuery("select * from ".EMAIL_TEMPLATES." where template_id='7'");
		$messageText=$templateData[0]['email_body'];
		$subject=$templateData[0]['email_subject'];
		
		$messageText=str_replace("[NAME]",$fullName,$messageText);
		$messageText=str_replace("[SITENAME]",SITE_NAME,$messageText);
		$messageText=str_replace("[SITEURL]",APPLICATION_URL,$messageText);
		$messageText=str_replace("[PROPERTYNO]",$pptyno[0]['propertycode'],$messageText);
		
		SendEmail($dataForm['email_address'],$subject,$messageText);
		

		
		
		__bookSessionClear();
		
		$this->_redirect("contents/pages/slug/bookingsucess");
		
		
	}
	
}
?>

