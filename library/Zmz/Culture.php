<?php

/**
 * Zmz
 *
 * LICENSE
 *
 * This source file is subject to the GNU GPLv3 license that is bundled
 * with this package in the file COPYNG.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @copyright  Copyright (c) 2010-2011 Massimo Zappino (http://www.zappino.it)
 * @license    http://www.gnu.org/licenses/gpl-3.0.html     GNU GPLv3 License
 */
class Zmz_Culture
{

    protected static $locale;
    protected static $timezone;
    protected static $defaultLocale = 'en';
    protected static $defaultTimezone = 'UTC';
    protected static $adapter;
    protected static $localeList;
    protected static $countryLanguages;

    /**
     * Set locale and timezone only if locale or timezone are not set previously
     *
     * @param Zend_Locale|string $locale
     * @param string $timezone
     */
    public static function setCulture($locale = null, $timezone = null, $force = false)
    {
        if (!$force) {
            if (self::isSetLocale()) {
                $locale = self::getLocale(true);
            }
            if (self::isSetTimezone()) {
                $timezone = self::getTimezone(true);
            }
        }
        self::setLocale($locale);
        self::setTimezone($timezone);
    }

    /**
     * Set adapter for Culture to store data
     *
     * @param Zmz_Culture_Adapter_Abstract $adapter
     */
    public static function setAdapter(Zmz_Culture_Adapter_Abstract $adapter)
    {
        self::$adapter = $adapter;
    }

    /**
     * Get adapter
     *
     * @return Zmz_Culture_Adapter_Abstract
     */
    public static function getAdapter()
    {
        if (!self::$adapter) {
            throw new Zmz_Culture_Exception('No adapter set');
        }

        return self::$adapter;
    }

    /**
     * Get timezone searching in this class and in the adapter
     *
     * @return string
     * @throws Zmz_Culture_Exception if timezone is not set
     */
    public static function getTimezone($checkAdapter = false)
    {
        if ($checkAdapter) {
            $adapter = self::getAdapter();
            try {
                $timezone = $adapter->timezone;
                if (!$timezone) {
                    throw new Zmz_Culture_Exception('timezone is not set into adapter');
                }
            } catch (Exception $e) {
                $locale = self::findLocale();
            }
        } else {
            if (self::$timezone) {
                $timezone = self::$timezone;
            } else {
//            $timezone = self::findTimezone();
                $timezone = self::setTimezone();
//                throw new Zmz_Culture_Exception("'timezone' is not set in Zmz_Culture object, use setTimezone()");
            }
        }
        return $timezone;
    }

    /**
     * Set timezone into adapter and current class
     * If no timezone was set defaultTimezone will be used
     *
     * @param string
     */
    public static function setTimezone($timezone = null)
    {
        if ($timezone) {
            try {
                $tz = new DateTimeZone($timezone);
            } catch (Exception $e) {
                $timezone = null;
            }
        }

        if (!$timezone) {
            $timezone = self::findTimezone();
        }
        $adapter = self::getAdapter();
        $adapter->timezone = (string) $timezone;

        date_default_timezone_set($timezone);
        self::$timezone = $timezone;

        return $timezone;
    }

    /**
     *
     * @return boolean
     */
    public static function isSetTimezone()
    {
        $adapter = self::getAdapter();

        return (bool) $adapter->timezone;
    }

    /**
     * Get the default timezone
     *
     * @return string
     */
    public static function getDefaultTimezone()
    {
        $timezone = self::$defaultTimezone;

        return $timezone;
    }

    /**
     * Get default locale
     *
     * @return Zend_Locale
     */
    public static function getDefaultLocale()
    {
        $locale = new Zend_Locale(self::$defaultLocale);

        return $locale;
    }

    /**
     * Get current locale searching in Adapter or by findLocale() function.
     * If locale is not set DefaultLocale will be returned.
     *
     * @return Zend_Locale $locale
     */
    public static function getLocale($checkAdapter = false)
    {
        if ($checkAdapter) {
            $adapter = self::getAdapter();
            try {
                $locale = $adapter->locale;
                if (!$locale) {
                    throw new Zmz_Culture_Exception('locale is not set into adapter');
                }
                $locale = new Zend_Locale($locale);
            } catch (Exception $e) {
                $locale = self::findLocale();
            }
        } else {
            if (self::$locale) {
                $locale = self::$locale;
            } else {
//                $locale = new Zend_Locale(self::$defaultLocale);
                self::setLocale(null);
//                throw new Zmz_Culture_Exception("'locale' is not set in Zmz_Culture object, use setLocale()");
            }
        }
        return $locale;
    }

    /**
     * Set current locale in Zend_Locale::setDefault(), Zend_Registry and
     * Adapter.
     *
     * @param Zend_Locale|string $locale
     */
    public static function setLocale($locale = null)
    {
        if (!$locale) {
            $locale = self::findLocale();
        }

        if (is_string($locale)) {
            try {
                $locale = new Zend_Locale($locale);
                if ($locale->__toString() == 'root') {
                    $locale = self::getDefaultLocale();
                }
            } catch (Exception $e) {
                $locale = new Zend_Locale();
            }
        }

        try {
            Zend_Locale::setDefault($locale);
        } catch (Exception $e) {
            Zend_Locale::setDefault(self::getDefaultLocale());
        }
        Zend_Registry::set('Zend_Locale', $locale);

        $adapter = self::getAdapter();
        $adapter->locale = (string) $locale;

        self::$locale = $locale;
    }

    public static function isSetLocale()
    {
        $adapter = self::getAdapter();

        return (bool) $adapter->locale;
    }

    /**
     *
     * @return Zend_Locale $locale
     */
    public static function findLocale()
    {
        try {
            $locale = Zend_Registry::get('Zend_Locale');
            $locale = new Zend_Locale($locale);
        } catch (Zend_Exception $e) {
            $locale = new Zend_Locale();
        }

//        if (!$locale->getRegion()) {
//            try {
//                $locale = new Zend_Locale($locale . '_' . strtoupper($locale));
//            } catch (Exception $e) {}
//        }

        return $locale;
    }

    public static function getLanguage()
    {
        return self::getLocale()->getLanguage();
    }

    /**
     *
     * @return string $timezone
     */
    public static function findTimezone()
    {
        $locale = self::getLocale();

        if (!$locale->getRegion()) {
            try {
                $locale = new Zend_Locale($locale . '_' . strtoupper($locale));
            } catch (Exception $e) {
                
            }
        }

        $region = $locale->getRegion();

        if ($region) {
            $timezoneList = Zend_Locale::getTranslationList('TimezoneToTerritory');

            if (isset($timezoneList[$region])) {
                $timezone = $timezoneList[$region];
                return $timezone;
            }
        }

        $httpCookieObject = new Zend_Http_Cookie('timezone', null, Zmz_Host::getHostname());
        $cookie = new Zmz_Cookie($httpCookieObject);
        $timezoneCookieValue = $cookie->getValue();
        $timezoneArray = @explode('/', $timezoneCookieValue);

        // check valid cookie
        if (isset($timezoneArray[0]) && isset($timezoneArray[1])) {
            $offset = intval($timezoneArray[0]);
            $dts = intval($timezoneArray[1]);
            $guessedTimezone = timezone_name_from_abbr('', $offset, $dts);


            $tz = new DateTimeZone($guessedTimezone);
            $guessedLocation = $tz->getLocation();
            $guessedLocationCode = $guessedLocation['country_code'];

            $timezone = $guessedTimezone;
        } else {
            $timezone = self::$defaultTimezone;
        }

        return $timezone;
    }

    public static function getLocaleList()
    {
        if (!self::$localeList) {
            $locale = Zmz_Culture::getLocale();
            $enLocale = new Zend_Locale('en');

            $locales = Zend_Locale::getLocaleList();
            unset($locales['in']);
            unset($locales['iw']);

            $countries = array();
            foreach ($locales as $k => $v) {
                $tmpLocale = new Zend_Locale($k);
                $region = $tmpLocale->getRegion();
                if ($region) {
                    $countries[(string) $tmpLocale] = Zend_Locale::getTranslation($tmpLocale->getLanguage(), 'language', $enLocale)
                            . ' (' . Zend_Locale::getTranslation($region, 'Territory', $enLocale) . ')';
                } else {
                    $countries[(string) $tmpLocale] = Zend_Locale::getTranslation($tmpLocale->getLanguage(), 'language', $enLocale);
                }
                $countries[(string) $tmpLocale] .= ' [' . (string) $tmpLocale . ']';
            }

            asort($countries);

            self::$localeList = $countries;
        }
        return self::$localeList;
    }

    public static function getCountryLanguages()
    {
        if (!self::$countryLanguages) {
            $locales = Zend_Locale::getLocaleList();
            $enLocale = new Zend_Locale('en');

            $countries = array();
            foreach ($locales as $k => $v) {
                $tmpLocale = new Zend_Locale($k);
                $region = $tmpLocale->getRegion();

                try {
                    $countryName = Zend_Locale::getTranslation($region, 'Territory', self::getLocale());
                    if (!trim($countryName)) {
                        throw new Zmz_Culture_Exception('No translation for current locale');
                    }
                } catch (Exception $e) {
                    $countryName = Zend_Locale::getTranslation($region, 'Territory', $enLocale);
                }
                if (!trim($countryName)) {
                    continue;
                }

                try {
                    $languageTranslation = Zend_Locale::getTranslation($tmpLocale->getLanguage(), 'language', self::getLocale());
                    if (!trim($languageTranslation)) {
                        throw new Zmz_Culture_Exception('No translation for current locale');
                    }
                } catch (Exception $e) {
                    $languageTranslation = Zend_Locale::getTranslation($tmpLocale->getLanguage(), 'language', $enLocale);
                }
                if (!trim($languageTranslation)) {
                    continue;
                }

                if (!isset($countries[$region])) {
                    $countries[$region] = array(
                        'name' => $countryName,
                        'locales' => array()
                    );
                }
                $countries[$region]['locales'][$k] = $languageTranslation;
            }
            self::$countryLanguages = $countries;
        }
        return self::$countryLanguages;
    }

    public static function getTimezoneList()
    {
        $timezones = Zend_Locale::getTranslationList('TimezoneToTerritory');

        $timezones = timezone_identifiers_list();

        $timezonesArray = array();
        foreach ($timezones as $v) {
            $timezonesArray[$v] = $v;
        }

        return $timezonesArray;
    }

    public static function getTimezoneListForSelect()
    {
        $list = self::getTimezoneList();
        $timezone = array();

        foreach ($list as $k => $v) {
            $tmp = explode('/', $k, 2);
            if (!isset($tmp[0]) || !isset($tmp[1])) {
                continue;
            } else {
                if (!isset($timezone[$tmp[0]])) {
                    $timezone[$tmp[0]] = array();
                }
                $timezone[$tmp[0]][$k] = $tmp[1];
            }
        }

        return $timezone;
    }

    public static function getTimezoneAbbrByTimezone($timezone)
    {
        $timezoneAbbr = Zend_Locale::getTranslation($timezone, 'TerritoryToTimezone');
        if (!$timezoneAbbr) {
            throw new Zmz_Culture_Exception("Timezone '$timezone' is not valid");
        }

        return $timezoneAbbr;
    }

    public static function getTimezoneByTimezoneAbbr($timezoneAbbr)
    {
        $timezone = Zend_Locale::getTranslation($timezoneAbbr, 'TimezoneToTerritory');
        if (!$timezone) {
            throw new Zmz_Culture_Exception("Timezone abbreviation '$timezoneAbbr' is not valid");
        }

        return $timezone;
    }

    /*
     * @todo
      public static function isValidLocale($locale)
      {
      $valid = true;
      if (!$locale) {
      $valid = false;
      } else {
      try {
      $tmpLocale = new Zend_Locale($locale);
      } catch (Exception $e) {
      $valid = false;
      }
      }

      return $valid;
      }

      public static function isValidTimezone($timezone)
      {

      }
     */
}

