<script type="text/javascript">
    $(document).ready(function(e) {

        $(".mws-datepicker-forward").datepicker({dateFormat: "dd/mm/yy", maxDate: new Date()});
    });
</script>
<?PHP

    class Form_Review extends Zend_Form
    {

        public function __construct($userId)
        {

            global $mySession;
            $this->init($userId);
        }

        public function init($userId)
        {
            global $mySession;
            $db = new Db();


            $full_name_value = "";
            $location_value = "";
            $check_in_value = "";
            $rating_value = "";
            $headline_value = "";
            $comment_value = "";
            $review_value = "";
            $ppty_no_value = "";

            if ($userId != "")
            {
                $userData = $db->runQuery("select * from " . OWNER_REVIEW . " inner join " . PROPERTY . " on " . PROPERTY . ".id = " . OWNER_REVIEW . ".property_id  where property_id = '" . $userId . "' ");

                if ($userData != "" && count($userData) > 0)
                {
                    $full_name_value = $userData[0]['owner_name'];
                    $ppty_no_value = $userData[0]['propertycode'];
                    $location_value = $userData[0]['location'];
                    $check_in_value = $userData[0]['check_in'];
                    $rating_value = $userData[0]['rating'];
                    $headline_value = $userData[0]['headline'];
                    $comment_value = $userData[0]['comment'];
                    $review_value = $userData[0]['review'];
                }
            }
            else
            {
                $userData = $db->runQuery("select * from " . USERS . " where user_id = '" . $mySession->LoggedUserId . "' ");

                $full_name_value = $userData[0]['first_name'];
                //$location_value = $userData[0]['address'];
            }


//            $ppty_no = new Zend_Form_Element_Text('ppty_no');
//            $ppty_no->setRequired(true)
//                    ->addValidator('NotEmpty', true, array('messages' => "Please enter Property number"))
//                    ->addDecorator('Errors', array('class' => 'error'))
//                    ->setAttrib("class", "mws-textinput required")
//                    ->setAttrib("maxlength", "50")
//                    ->setValue($ppty_no_value);

            $new_array = array();
            $final_array = array();
            $final_array = array("No data found");
            $user_id = $mySession->LoggedUserId;
            
            $propert_codes_sql = "SELECT propertycode,(select count(property_id) from booking where user_id = $user_id and property_id = b.property_id) as _bcount, (select count(property_id) from review where user_id = $user_id and property_id = b.property_id ) as _count, review.*,b.property_id FROM " . BOOKING . " as b
                                  inner join ".PROPERTY." on ".PROPERTY.".id = b.property_id
                                  left join review on review.property_id = b.property_id and review.user_id = b.user_id
                                  where b.user_id = ".$user_id."
                                  group by b.property_id
                                  having _bcount > _count or review.property_id is NULL    
                                  ";
            
          //prd($propert_codes_sql);
            $propert_codes_arr = $db->runQuery($propert_codes_sql);
//            prd($propert_codes_arr);

            if (!empty($propert_codes_arr[0]))
            {
                $final_array=array();
                foreach ($propert_codes_arr as $p_key => $p_value)
                {
                    $final_array[$p_value["propertycode"]] = $p_value["propertycode"];
                }
            }
//            prd($final_array);

            $ppty_no = new Zend_Form_Element_Select("ppty_no");
            $ppty_no->setRequired(true)
                    ->addValidator('NotEmpty', true, array('messages' => "Please select property code"))
                    ->addDecorator('Errors', array('class' => 'error'))
                    ->setAttrib("class", "mws-textinput required")
                    ->setMultiOptions($final_array);


            $full_name = new Zend_Form_Element_Text('full_name');
            $full_name->setRequired(true)
                    ->addValidator('NotEmpty', true, array('messages' => "Please enter Full Name"))
                    ->addDecorator('Errors', array('class' => 'error'))
                    ->setAttrib("class", "mws-textinput required")
                    ->setAttrib("readonly", "readonly")
                    ->setAttrib("maxlength", "50")
                    ->setValue($full_name_value);

            $location = new Zend_Form_Element_Text('location');
            $location->setRequired(true)
                    ->addValidator('NotEmpty', true, array('messages' => "enter email address"))
                    ->addDecorator('Errors', array('class' => 'error'))
                    ->setAttrib("class", "mws-textinput required")
                    ->setAttrib("maxlength", "50")
                    ->setValue($location_value);


            $check_in = new Zend_Form_Element_Text('check_in');
            $check_in->setRequired(true)
                    ->addValidator('NotEmpty', true, array('messages' => "Please enter phone number"))
                    ->addDecorator('Errors', array('class' => 'error'))
                    ->setAttrib("class", "mws-textinput mws-datepicker-forward required")
                    ->setValue($check_in_value);

            $ratingArr = array();
            $ratingArr[0]['key'] = "";
            $ratingArr[0]['value'] = "- - Select - -";

            for ($i = 1; $i <= 10; $i++)
            {
                $ratingArr[$i]['key'] = $i;
                $ratingArr[$i]['value'] = $i;
            }

            $rating = new Zend_Form_Element_Select('rating');
            $rating->setRequired(true)
                    ->addMultiOptions($ratingArr)
                    ->addValidator('NotEmpty', true, array('messages' => "enter email address"))
                    ->addDecorator('Errors', array('class' => 'error'))
                    ->setAttrib("class", "mws-textinput required")
                    ->setValue($rating_value);

            $headline = new Zend_Form_Element_Text('headline');
            $headline->setRequired(true)
                    ->addValidator('NotEmpty', true, array('messages' => "enter email address"))
                    ->addDecorator('Errors', array('class' => 'error'))
                    ->setAttrib("class", "mws-textinput required reviewHeadline")
                    ->setAttrib("maxlength", "100")
                    ->setValue($headline_value);



            $comment = new Zend_Form_Element_Textarea('comment');
            $comment
                    ->addDecorator('Errors', array('class' => 'error'))
                    //->setAttrib("class","required")
                    ->setAttrib("rows", "4")
                    ->setAttrib("cols", "30")
                    ->setAttrib("maxlength", "300")
                    ->setValue($comment_value);



            $review = new Zend_Form_Element_Textarea('review');
            $review->setRequired(true)
                    ->addDecorator('Errors', array('class' => 'error'))
                    ->setAttrib("class", "required")
                    ->setAttrib("rows", "4")
                    ->setAttrib("cols", "30")
                    ->setAttrib("maxlength", "1000")
                    ->addValidator('NotEmpty', true, array('messages' => "Enter message"))
                    ->setValue($review_value);



            $this->addElements(array($ppty_no, $full_name, $location, $check_in, $rating, $headline, $comment, $review));
        }

    }
?>