<?php

class Form_Locale extends Zmz_Form
{

    public function init()
    {
        parent::init();

        $this->setName('formLocale');
        $this->setAction('/locale');

        $locale = new Zend_Form_Element_Select('locale');
        $locale->setLabel(Zmz_Translate::_('Language'))
                ->addMultiOptions(MyApp_Language::getAvailableLanguages())
                ->setRequired(true)
                ->setAttrib('class', 'default-field')
                ->addValidator('NotEmpty', true)
                ->setValue(MyApp_Language::getLanguage());

        $timezone = new Zend_Form_Element_Select('timezone');
        $timezone->setLabel(Zmz_Translate::_('Timezone'))
                ->addMultiOptions(Zmz_Culture::getTimezoneListForSelect())
                ->setRequired(true)
                ->setAttrib('class', 'default-field')
                ->addValidator('NotEmpty', true)
                ->setValue(Zmz_Culture::getTimezone());

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel(Zmz_Translate::_('Save'));

        $this->addElements(
                array(
                    $locale,
                    $timezone,
                    $submit
        ));
    }

}

