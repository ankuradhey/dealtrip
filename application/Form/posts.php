<?php
class Form_Post extends Zend_Form
{
    public function __construct()
    {
        parent::__construct($options);
        $this->setName('Posts');
        $id = new Zend_Form_Element_Hidden('id');
        $title = new Zend_Form_Element_Text('title');
        $title->setLabel('Title')
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty');
        $description = new Zend_Form_Element_Textarea('description');
        $description->setLabel('Description')
                ->setRequired(true)
                ->setAttrib('rows',20)
                ->setAttrib('cols',50)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty');
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('id', 'submitbutton');
        $this->addElements( array( $id, $title, $description, $submit ));
    }
}
?>