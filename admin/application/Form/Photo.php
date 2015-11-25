<?PHP

    class Form_Photo extends Zend_Form
    {

        public function __construct($hp, $photoId)
        {
            if ($hp == 1)
            {
                $this->inithpimage($photoId);
            }
            else
            {
                $this->init($photoId);
            }
        }

        private function inithpimage($photoId)
        {
            global $mySession;
            $db = new Db();
            $img_name_value = "";
            if ($photoId != "")
            {
                $photoData = $db->runQuery("select * from homepageimg where id='" . $photoId . "'");
                $img_name_value = $photoData[0]['img_name'];
            }
            $photo_path2 = new Zend_Form_Element_File('photo_path2');
            $photo_path2->setAttrib("class", "textInput")
                    ->setDestination(SITE_ROOT . 'uploads/')
                    ->addValidator('Extension', false, 'jpg,jpeg,png,gif');

            $photo_path2->setRequired(true)
                    ->addValidator('NotEmpty', true, array('messages' => 'Photo path required.'))
                    ->addDecorator('Errors', array('class' => 'error'));

            $image_text = new Zend_Form_Element_Textarea('image_text');
            $image_text->setRequired(true)
                    ->addValidator('NotEmpty', true, array('messages' => 'Photo Title is required.'))
                    ->addDecorator('Errors', array('class' => 'error'))
                    ->setAttrib("class", "textInput")
                    ->setAttrib("placeholder", "Photo Caption");

            $this->addElements(array($photo_path2, $image_text));
        }

        public function init($photoId)
        {
            global $mySession;
            $db = new Db();

            $path = "";
            $title = "";
            $date = "";
            $description = "";
            $status = "";
            $featuredstatus = "";
            $phototype = "";
            $featurepath = "";

            if ($photoId != "")
            {
                $photoData = $db->runQuery("select * from " . PHOTO . " where 	photo_id='" . $photoId . "'");
                $path = $photoData[0]['photo_path'];
                $title = $photoData[0]['photo_title'];
                $date = date('F d-Y, h:i:s a', strtotime($photoData[0]['video_date']));
                $description = $photoData[0]['photo_description'];
                $status = $photoData[0]['photo_status'];
                $featuredstatus = $photoData[0]['featured_status'];
            }

            //->setValue($phototype);



            $photo_path2 = new Zend_Form_Element_File('photo_path2');
            $photo_path2->setAttrib("class", "textInput")
                    ->setDestination(SITE_ROOT . 'photo/')
                    ->addValidator('Extension', false, 'jpg,jpeg,png,gif');

            $photo_path2->setRequired(true)
                    ->addValidator('NotEmpty', true, array('messages' => 'Photo path required.'))
                    ->addDecorator('Errors', array('class' => 'error'));




            $photo_title = new Zend_Form_Element_Text('photo_title');
            $photo_title->setRequired(true)
                    ->addValidator('NotEmpty', true, array('messages' => 'Photo Title is required.'))
                    ->addDecorator('Errors', array('class' => 'error'))
                    ->setAttrib("class", "textInput")
                    ->setValue($title);

            $up_date = new Zend_Form_Element_Text('up_date');
            $up_date->setAttrib("class", "textInput")
                    ->setAttrib("disabled", "disabled")
                    ->setValue($date);

            $photo_description = new Zend_Form_Element_Textarea('photo_description');
            $photo_description->setRequired(true)
                    ->addValidator('NotEmpty', true, array('messages' => 'Video description is required.'))
                    ->addDecorator('Errors', array('class' => 'error'))
                    ->setAttrib("class", "textarea_medium")
                    ->setValue($description);


            $Option[0]['key'] = "1";
            $Option[0]['value'] = "Active";
            $Option[1]['key'] = "0";
            $Option[1]['value'] = "Inactive";

            $photo_status = new Zend_Form_Element_Radio('photo_status');
            $photo_status->setRequired(true)
                    ->addMultiOptions($Option)
                    ->addValidator('NotEmpty', true, array('messages' => 'Please select status of photo.'))
                    ->addDecorator('Errors', array('class' => 'error'))
                    ->setValue($status);


            //->setValue($featurepath);

            $this->addElements(array($photo_path2, $photo_title, $up_date, $photo_description, $photo_status));
        }

    }

?>