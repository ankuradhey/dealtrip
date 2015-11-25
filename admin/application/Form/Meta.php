<?php

    class Form_Meta extends Zend_Form
    {

        public function __construct($adminId = "")
        {
            $this->init($adminId);
        }

        public function init($adminId)
        {
            global $mySession;
            $db = new Db();
            $page_name_value = "";
            $title_value = "";
            $meta_keywords_value = "";
            $meta_description_value = "";

            if ($adminId != "")
            {
                $metaData = $db->runQuery("select * from " . META_INFO . " where meta_id = '" . $adminId . "'");
                $page_name_value = $metaData[0]['meta_id'];
                $title_value = $metaData[0]['meta_title'];
                $meta_keywords_value = $metaData[0]['meta_keyword'];
                $meta_description_value = $metaData[0]['meta_description'];
            }

            $pageArr = $db->runQuery("select * from " . META_INFO . " ");

            $pagenameArr[0]['key'] = '';
            $pagenameArr[0]['value'] = '-- Select --';


            for ($i = 1; $i <= count($pageArr); $i++)
            {
                $pagenameArr[$i]['key'] = $pageArr[$i - 1]['meta_id'];
                $pagenameArr[$i]['value'] = $pageArr[$i - 1]['meta_page'];
            }


            $page_name = new Zend_Form_Element_Select('page_name');
            $page_name->setRequired(true)
                    ->addMultiOptions($pagenameArr)
                    ->setAttrib("class", "textInput required")
                    ->setAttrib("onchange", "getmetadata(value);")
                    ->setValue($page_name_value);



            $title = new Zend_Form_Element_Text('title');
            $title->setRequired(true)
                    ->addValidator('NotEmpty', true, array('messages' => 'Title name is required.'))
                    ->addDecorator('Errors', array('class' => 'error'))
                    ->setAttrib("class", "mws-textinput required")
                    ->setAttrib("minlength", "3")
//                    ->setAttrib("maxlength", "69")
                    ->setAttrib("id", "title")
                    ->setValue($title_value);


            $meta_keyword = new Zend_Form_Element_Text('meta_keyword');
            $meta_keyword->setRequired(true)
                    ->addValidator('NotEmpty', true, array('	messages' => 'Keywords are required.'))
                    ->addDecorator('Errors', array('class' => 'error'))
                    ->setAttrib("class", "mws-textinput required")
                    ->setAttrib("minlength", "5")
                    ->setAttrib("maxlength", "200")
                    ->setAttrib("id", "meta_keyword")
                    ->setValue($meta_keywords_value);


            /* 		$meta_keyword = new Zend_Form_Element_Textarea('meta_keyword');
              $meta_keyword->setAttrib("class","textInput required")
              ->setAttrib("rows","3")
              ->setAttrib("style","height:200px;width:400px;")
              ->setAttrib("id","meta_keyword")
              ->setAttrib("minlength","3")
              ->setAttrib("maxlength","200")
              ->setRequired(true)
              ->addValidator('NotEmpty',true,array('messages' =>'Keywords are required.'))
              ->addDecorator('Errors', array('class'=>'error'))
              ->setValue($meta_keywords_value);

             */

            $meta_description = new Zend_Form_Element_Textarea('meta_description');
            $meta_description->setAttrib("class", "mws-textinput required")
                    ->setAttrib("rows", "3")
                    ->setAttrib("id", "meta_description")
                    ->setAttrib("style", "height:200px;width:400px;")
                    ->setAttrib("minlength", "10")
                    ->setAttrib("maxlength", "400")
                    ->setRequired(true)
                    ->addValidator('NotEmpty', true, array('messages' => 'Description is required.'))
                    ->addDecorator('Errors', array('class' => 'error'))
                    ->setValue($meta_description_value);

            $this->addElements(array($page_name, $title, $meta_keyword, $meta_description));
        }

    }

?>