<?PHP

    class Form_Country extends Zend_Form
    {

        public function __construct($countryId = "")
        {
            $this->init($countryId);
        }

        public function init($countryId)
        {
            global $mySession;
            $db = new Db();

            $CountryName = "";
            if ($countryId != "")
            {
                $PageData = $db->runQuery("select * from " . COUNTRIES . " where country_id='" . $countryId . "'");
                $CountryName = $PageData[0]['country_name'];
            }

            $country_name = new Zend_Form_Element_Text('country_name');
            $country_name->setRequired(true)
                    ->addValidator('NotEmpty', true, array('messages' => 'Country Name is required.'))
                    ->addValidator('regex', true, array(
                        'pattern' => '/^[a-zA-Z\-]+$/',
                        'messages' => array(
                            'regexNotMatch' => 'Please enter proper name and without space'
                        )
                            )
                    )
                    ->addDecorator('Errors', array('class' => 'error'))
                    ->setAttrib("class", "mws-textinput required")
                    ->setValue($CountryName);

            $this->addElements(array($country_name));
        }

    }

?>