<?php

class MyApp_Language
{

    protected static $availableLanguages;

    public static function getAvailableLanguages()
    {
        if (!self::$availableLanguages) {
            $locale = Zmz_Culture::getLocale();
            $filename = APPLICATION_PATH . '/configs/languages.ini';
            $languagesConfig = new Zend_Config_Ini($filename);
            $languages = $languagesConfig->language;

            $languagesArray = array();
            foreach ($languages as $k => $v) {
                $tmpLocale = new Zend_Locale($v);
                $string = ucfirst(Zend_Locale::getTranslation($tmpLocale->getLanguage(), 'language', $locale));
                if (!$tmpLocale->equals($locale)) {
                    $string .= ' (' . strtolower(Zend_Locale::getTranslation($tmpLocale->getLanguage(), 'language', $tmpLocale)) . ')';
                }
                $languagesArray[$k] = $string;
            }
            self::$availableLanguages = $languagesArray;
        }
        return self::$availableLanguages;
    }

    public static function getLanguage()
    {
        $locale = Zmz_Culture::getLocale();
        $language = $locale->getLanguage();

        $availableLanguages = self::getAvailableLanguages();

        if (!isset($availableLanguages[$language])) {
            $language = Zmz_Culture::getDefaultLocale();
            Zmz_Culture::setLocale(Zmz_Culture::getDefaultLocale());
        }

        return $language;
    }

}

