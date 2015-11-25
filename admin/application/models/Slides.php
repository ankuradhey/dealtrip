<?php

    __autoloadDB('Db');

    class Slides extends Db
    {

        public function saveSlides($dataForm, $lType, $lId = '')
        {
            global $mySession;
            $db = new Db();


            $order = $db->runQuery("select coalesce(max(lppty_order),0)+1 as _order from  " . SLIDES_PROPERTY . " 
							where lppty_type = '" . $lType . "' ");

            $data_update['lppty_property_id'] = $dataForm['propertycode'];
            $data_update['lppty_type'] = $lType;



            if ($lId != "")
            {
                $data_update['lppty_status'] = $dataForm['status'];
                $condition = 'lppty_id=' . $lId;
                $result = $db->modify(SLIDES_PROPERTY, $data_update, $condition);
            }
            else
            {
                //=== increase the order to make space for the new one
                    
                    $updateData = array();
                    $updateData['lppty_order'] = new Zend_Db_Expr('lppty_order+1');
                    $db->modify(SLIDES_PROPERTY,$updateData,"lppty_type='".$lType."'");
                    
                //--- increased sir
                
                //$data_update['lppty_order'] = $order[0]['_order'];
                $data_update['lppty_order'] = '1';
                $result = $db->save(SLIDES_PROPERTY, $data_update);
            }

            return 1;
        }

        public function changeStatus($lId, $status)
        {
            $db = new Db();

            $data_update['lppty_status'] = ($status == '1') ? '0' : '1';
            $condition = "lppty_id = " . $lId;
            $Result = $db->modify(SLIDES_PROPERTY, $data_update, $condition);

            return $Result;
        }

        public function deleteSlides($lId, $lppty_type)
        {
            $db = new Db();
            $lId = explode("|", $lId);

            $result = 0;
            foreach ($lId as $values)
            {
                if ($values > 0)
                {

                    $chekArr = $db->runQuery("select lppty_id, lppty_order from " . SLIDES_PROPERTY . " 
									  where lppty_order >= (select lppty_order from " . SLIDES_PROPERTY . " where lppty_id = '" . $values . "')
									  and lppty_type = '" . $lppty_type . "'
									  order by lppty_order asc
									   ");


                    $tmp = "";
                    foreach ($chekArr as $val)
                    {
                        if ($val['lppty_id'] == $values)
                        {
                            $condition = "lppty_id = " . $values . " and lppty_type = '" . $lppty_type . "'";
                            $result = $db->delete(SLIDES_PROPERTY, $condition);
                            $tmp = $val['lppty_order'];
                        }
                        else
                        {
                            $data_update['lppty_order'] = $tmp;
                            $condition = "lppty_id=" . $val['lppty_id'];
                            $db->modify(SLIDES_PROPERTY, $data_update, $condition);
                            $tmp = $val['lppty_order'];
                        }
                    }
                }
            }
            return $result;
        }

        public function saveLatestReview($dataForm, $lId)
        {
            $db = new Db();
            //$order = $db->runQuery("select coalesce(max(r_order),0)+1 as _order from  " . LATEST_REVIEW . " ");

            //$data_update['r_review_id'] = $dataForm['review'];
            $data_update['r_status'] = $dataForm['status'];

            if ($lId != "")
            {

                $condition = "r_id = " . $lId;
                $data_update['r_property_id'] = $dataForm['propertycode'];
                $result = $db->modify(LATEST_REVIEW, $data_update, $condition);
            }
            else
            {
                
                $updateData = array();
                $updateData['r_order'] = new Zend_Db_Expr('r_order+1');
                
                $db->modify(LATEST_REVIEW,$updateData,'1');
                
                $data_update['r_order'] = '1';
                $data_update['r_property_id'] = $dataForm['propertycode'];
                $result = $db->save(LATEST_REVIEW, $data_update);
            }
            return $result;
        }

        public function changeReviewStatus($lId, $status)
        {
            $db = new Db();
            $data_update['r_status'] = ($status == '1') ? '0' : '1';
            $condition = "r_id = " . $lId;
            $Result = $db->modify(LATEST_REVIEW, $data_update, $condition);
            return $Result;
        }

        /*         * *********************************** */
        /* 	DELETE Latest Reviews   		 */
        /*         * *********************************** */

        public function deleteLatestReview($lId)
        {

            $db = new Db();
            $lId = explode("|", $lId);

            $result = 0;
            foreach ($lId as $values)
            {
                if ($values > 0)
                {

                    $chekArr = $db->runQuery("select r_id, r_order from " . LATEST_REVIEW . " 
									  where r_order >= (select r_order from " . LATEST_REVIEW . " where r_id = '" . $values . "')
									  order by r_order asc
									 ");

                    $tmp = "";
                    foreach ($chekArr as $val)
                    {
                        if ($val['r_id'] == $values)
                        {
                            $condition = "r_id = " . $values;
                            $result = $db->delete(LATEST_REVIEW, $condition);
                            $tmp = $val['r_order'];
                        }
                        else
                        {
                            $data_update['r_order'] = $tmp;
                            $condition = "r_id=" . $val['lppty_id'];
                            $db->modify(LATEST_REVIEW, $data_update, $condition);
                            $tmp = $val['r_order'];
                        }
                    }
                }
            }
            return $result;
        }

    }

?>