<?php

    __autoloadDB('Db');

    class Photo extends Db
    {

        public function Savehpi($dataForm, $photoId = '')
        {
            global $mySession;
            $db = new Db();

            if ($dataForm['photo_path2'] != "")
            {
                $photoname = explode('.', $dataForm['photo_path2']);
                $len = count($photoname);
                $extension = $photoname[$len - 1];
                $datetime = date(d . m . y_h . i . s);
                $new_name = $datetime . '.' . $extension;
                rename(SITE_ROOT . 'uploads/' . $dataForm['photo_path2'], SITE_ROOT . 'uploads/' . $new_name);
                $data_update['img_name'] = $new_name;
                $data_update['image_text'] = addslashes(trim($dataForm["image_text"]));

                if ($photoId > 0)
                {
                    $condition = 'id=' . $photoId;
                    $result = $db->modify('homepageimg', $data_update, $condition);
                }
                else
                {
                    $updateData = array();
                    $updateData['order_number'] = new Zend_Db_Expr('order_number + 1');
                    
                    $db->modify(HOMEPAGEIMG,$updateData);
                    
                    $data_update['order_number'] = '1';
                    $result = $db->save('homepageimg', $data_update);
                }

                return 1;
            }
            else
                return 0;
        }

        public function Addphoto($dataForm, $photoId = '')
        {
            global $mySession;
            $db = new Db();


            if ($dataForm['photo_path2'] != "")
            {
                $photoname = explode('.', $dataForm['photo_path2']);
                $len = count($photoname);
                $extension = $photoname[$len - 1];
                $datetime = date(d . m . y_h . i . s);
                $new_name = $datetime . '.' . $extension;
                rename(SITE_ROOT . 'photo/' . $dataForm['photo_path2'], SITE_ROOT . 'photo/' . $new_name);
                $data_update['photo_path'] = $new_name;
            }


            $data_update['photo_title'] = $dataForm['photo_title'];
            $data_update['photo_description'] = $dataForm['photo_description'];
            $data_update['photo_status'] = $dataForm['photo_status'];
            $data_update['website_id'] = $mySession->WebsiteId;

            if ($photoId != "")
            {
                $condition = 'photo_id=' . $photoId;
                $result = $db->modify(PHOTO, $data_update, $condition);
            }
            else
            {

                $result = $db->save(PHOTO, $data_update);
            }
            return 1;
        }

    }

?>