<?php
class Zend_View_Helper_FckEditor
{
        /**
         * Creates a form element with FCKEditor.
         *
         * @param string $name The form element name to be used with FCKeditor
         * @param string $value The form element initial value (like: Enter content here)
         * @param array $options (FCKeditor options)
        */

    protected $_view;

    // setView() function is automatically called by the view before calling the main helper function.
    function setView($view)
    {
        $this->_view = $view;
    }

    public function fckEditor($name = '', $value = '', $options = array())
    {
           // $baseUrl = $this->_view->BaseUrl();

            // can be auto-loaded from a non-public library dir
            // FCKeditor.php = renamed fckeditor_php(4|5).php
            include_once 'js/fckeditor/fckeditor.php'; //die;

            $oFCKeditor = new FCKeditor($name);

            // Custom configuration settings
            // Place in it's own Dir to allow updates of FCKeditor w/o changes
            //$oFCKeditor->Config['CustomConfigurationsPath'] = $baseUrl . '/js/configs/myFCKconfig.js';

            // Default configuration

            $oFCKeditor->BasePath            = JS_URL.'fckeditor/';  // must direct to the site's public area
            //$oFCKeditor->ToolbarSet          = ((isset($options['ToolbarSet'])) ? $options['ToolbarSet'] : 'Tsc_mighty');
			$oFCKeditor->ToolbarSet          = ((isset($options['ToolbarSet'])) ? $options['ToolbarSet'] : 'Basic');
            $oFCKeditor->Width               = empty($options['Width']) ? '100%' : $options['Width'];
            $oFCKeditor->Height              = empty($options['Height']) ? 500 : $options['Height'];
			$oFCKeditor->Config['SkinPath'] = 'skins/silver/';
            $oFCKeditor->Value               = $value;

            return $oFCKeditor->Create();
    }
} 