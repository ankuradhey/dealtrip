<?php

    __autoloadDB('Db');

    class Subscription extends Db
    {

        public function saveSubscription($dataForm, $subscriptionId = "")
        {
            global $mySession;
            $db = new Db();
            $dataForm = SetupMagicQuotesTrim($dataForm);
            
            

            if (empty($subscriptionId))
            {
                $data = array();
                //code for inserting order
                $db->save("subscriber", $dataForm);
                $latestId = $db->lastInsertId();

                return 1;
            }else{
                $condition = "subscriber_id = ".$subscriptionId;
                $db->modify("subscriber",$dataForm, $condition);
            
                return 2;
            }
        
            
        }

        #-----------------------------------------------------------#
        # Delete Property Type Function
        // Here delete Property Type record from PROPERTYTYPE table.
        #-----------------------------------------------------------#

        public function deleteSpecification($subscriptionId)
        {
            echo $subscriptionId;

            global $mySession;
            $db = new Db();
            $condition1 = "spec_id='" . $subscriptionId . "'";
            $db->delete(SPECIFICATION, $condition1);
            $db->delete(SPEC_CHILD, $condition1);
        }

        #-----------------------------------------------------------#
        # Status Property Type Function
        // Here Property Type status changed from PROPERTYTYPE table.
        #-----------------------------------------------------------#

        public function statusSpecification($status, $subscriptionId)
        {
            global $mySession;
            $db = new Db();
            $data_update['status'] = $status;
            $condition = "spec_id='" . $subscriptionId . "'";
            $db->modify(SPECIFICATION, $data_update, $condition);
        }

    }

?>