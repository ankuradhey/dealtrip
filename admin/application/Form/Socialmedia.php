<?PHP

    class Form_Socialmedia extends Zend_Form
    {

        public function init()
        {
            global $mySession;

            $facebook_app_id = new Zend_Form_Element_Text('facebook_app_id');
            $facebook_app_id->setRequired(true)
                    ->addValidator('NotEmpty', true, array('messages' => 'Facebook App ID is required.'))
                    ->addDecorator('Errors', array('class' => 'error'))
                    ->setAttrib("class", "mws-textinput required checkvaliduser");

            $facebook_secret_id = new Zend_Form_Element_Text('facebook_secret_id');
            $facebook_secret_id->setRequired(true)
                    ->addValidator('NotEmpty', true, array('messages' => 'Facebook App Secret ID is required.'))
                    ->addDecorator('Errors', array('class' => 'error'))
                    ->setAttrib("class", "mws-textinput required checkvaliduser");

            $twitter_consumer_key = new Zend_Form_Element_Text('twitter_consumer_key');
            $twitter_consumer_key->setRequired(true)
                    ->addValidator('NotEmpty', true, array('messages' => 'Twitter Consumer Key is required.'))
                    ->addDecorator('Errors', array('class' => 'error'))
                    ->setAttrib("class", "mws-textinput required checkvaliduser");

            $twitter_consumer_secret = new Zend_Form_Element_Text('twitter_consumer_secret');
            $twitter_consumer_secret->setRequired(true)
                    ->addValidator('NotEmpty', true, array('messages' => 'Twitter Consumer Secret Key is required.'))
                    ->addDecorator('Errors', array('class' => 'error'))
                    ->setAttrib("class", "mws-textinput required checkvaliduser");

            $twitter_access_token = new Zend_Form_Element_Text('twitter_access_token');
            $twitter_access_token->setRequired(true)
                    ->addValidator('NotEmpty', true, array('messages' => 'Twitter Access Token Key is required.'))
                    ->addDecorator('Errors', array('class' => 'error'))
                    ->setAttrib("class", "mws-textinput required checkvaliduser");

            $twitter_access_secret = new Zend_Form_Element_Text('twitter_access_secret');
            $twitter_access_secret->setRequired(true)
                    ->addValidator('NotEmpty', true, array('messages' => 'Twitter Access Secret Key is required.'))
                    ->addDecorator('Errors', array('class' => 'error'))
                    ->setAttrib("class", "mws-textinput required checkvaliduser");

            $this->addElements(array($facebook_app_id, $facebook_secret_id, $twitter_consumer_key, $twitter_consumer_secret, $twitter_access_token, $twitter_access_secret));
        }

    }

?>