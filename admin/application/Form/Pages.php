<?PHP

    class Form_Pages extends Zend_Form
    {

        public function __construct($pageId = "")
        {
            $this->init($pageId);
        }

        public function init($pageId)
        {
            global $mySession;
            $db = new Db();

            $PageTitle = "";
            $PageContent = "";
            $PageMetaKeyword = "";
            $PageMetaDescription = "";

            if ($pageId != "")
            {
                $PageData = $db->runQuery("select * from " . PAGES1 . " where page_id='" . $pageId . "'");
//                prd($PageData);

                $PageTitle = $PageData[0]['page_title'];
                $PageContent = $PageData[0]['page_content'];
                $PageMetaKeyword = $PageData[0]['meta_keywords'];
                $PageMetaDescription = $PageData[0]['meta_description'];
                $PageSynonyms= $PageData[0]['synonyms'];
            }



            $page_title = new Zend_Form_Element_Text('page_title');
            $page_title->setRequired(true)
                    ->addValidator('NotEmpty', true, array('messages' => 'Page title is required.'))
                    ->addDecorator('Errors', array('class' => 'error'))
                    ->setAttrib("class", "mws-textinput required")
                    ->setValue($PageTitle);

            $page_content = new Zend_Form_Element_Textarea('page_content');
            $page_content->setRequired(true)
                    ->addValidator('NotEmpty', true, array('messages' => 'Page content is required.'))
                    ->addDecorator('Errors', array('class' => 'error'))
                    ->setAttrib("id", "elrte")->setAttrib("cols", "auto")
                    ->setAttrib("rows", "auto")
                    ->setValue($PageContent);


            $meta_keywords = new Zend_Form_Element_Text('meta_keywords');
            $meta_keywords->setRequired(true)
                    ->addValidator('NotEmpty', true, array('messages' => 'Keywords are required.'))
                    ->addDecorator('Errors', array('class' => 'error'))
                    ->setAttrib("class", "mws-textinput required")
                    ->setValue($PageMetaKeyword);

            $meta_description = new Zend_Form_Element_Text('meta_description');
            $meta_description->setRequired(true)
                    ->addValidator('NotEmpty', true, array('messages' => 'Description is required.'))
                    ->addDecorator('Errors', array('class' => 'error'))
                    ->setAttrib("class", "mws-textinput required")
                    ->setValue($PageMetaDescription);

            $synonyms = new Zend_Form_Element_Text('synonyms');
            $synonyms->setRequired(true)
                    ->addValidator('NotEmpty', true, array('messages' => 'URL is required.'))
                    ->addDecorator('Errors', array('class' => 'error'))
                    ->setAttrib("class", "mws-textinput required")
                    ->setValue($PageSynonyms);

            $this->addElements(array($page_title, $page_content, $meta_keywords, $meta_description,$synonyms));
        }

    }

?>