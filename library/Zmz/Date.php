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
class Zmz_Date
{

    const SQL_DATETIME = 'yyyy-MM-dd HH:mm:ss';

    protected static $_localeDateFormat = array();
    protected static $_localeTimeFormat = array();

    public static function getDate($date = null, $part = null)
    {
        return self::_getDateObject($date, $part);
    }

    /**
     *
     * @param string|Zend_Date $date
     * @param string $format
     * @param string|Zend_Locale              $locale  OPTIONAL Locale for parsing input
     * @return string
     */
    public static function printDate($date = null, $format = null, $locale = null)
    {
        if (!$date instanceof Zend_Date) {
            if (!$date) {
                return '';
            }
            $date = new Zend_Date($date, null, $locale);
        }
        if ($format === null) {
            $format = self::getLocaleDateTimeFormat();
        }
        $string = $date->get($format);

        return $string;
    }

    /**
     * Convert given date string with UTC timezone into default locale format
     * and locale timezone
     *
     * @param string $date date string in SQL format with UTC timezone
     * @param string $defaultDate date to use if $date is null
     * @return Zend_Date
     */
    public static function getDateFromDb($date, $defaultDate = null)
    {
        if ($defaultDate == null) {
            $defaultDate = "1970-01-01 00:00:00";
        }
        if (null === $date) {
            $date = $defaultDate;
        }

        if (!is_string($date)) {
            throw new Exception('$date must be a string');
        }

        $tmpDate = new Zend_Date();
        $tmpDate->setTimezone('UTC');
        $tmpDate->set($date, self::SQL_DATETIME);
        $tmpDate->setTimezone(Zmz_Culture::getTimezone());

        return $tmpDate;
    }

    public static function printDateFromDb($date, $format = null, $defaultDateString = null)
    {
        if ($date == null) {
            return $defaultDateString;
        }
        try {
            $date = self::getDateFromDb($date);
        } catch (Zmz_Date_Exception $e) {
            return $defaultDateString;
        }
        if (!$format) {
            $format = self::getLocaleDateTimeFormat();
        }
        $string = $date->get($format);

        return $string;
    }

    /**
     *
     * @param string|Zend_Date $date
     * @param <type> $timeZero
     * @param boolean $toUTC
     * @return string
     */
    public static function getSqlDateTime($date = null, $timeZero = false, $toUTC = true)
    {
        if ($timeZero) {
            // reset time to '00:00:00'
            $date = self::_getDateObject($date, self::getLocaleDateFormat());
        } else {
            $date = self::_getDateObject($date, self::getLocaleDateTimeFormat());
        }

        $dateSQL = clone($date);
        if ($toUTC) {
            $dateSQL = self::toUTC($dateSQL);
        }
        $format = self::SQL_DATETIME;
        $stringDate = self::printDate($dateSQL, $format);

        return $stringDate;
    }

    /**
     * Create a Zend_Date object from a string
     *
     * @param string|Zend_Date $date
     * @return Zend_Date
     */
    protected static function _getDateObject($date = null, $part = null)
    {
        if (!$date instanceof Zend_Date) {
            if ($part === null) {
                $part = self::getLocaleDateTimeFormat();
            }
            $date = new Zend_Date($date, $part, null);
        }

        return $date;
    }

    public static function toUTC($date = null)
    {
        $date = self::_getDateObject($date);
        $date->setTimezone('UTC');

        return $date;
    }

    public static function getLocaleDateFormat($part = 'short', $locale = null)
    {
        if (!$locale) {
            $locale = Zend_Registry::get('Zend_Locale');
        }
        if (!isset(self::$_localeDateFormat[$part][(string) $locale])) {
            $dateFormats = Zend_Locale_Data::getList($locale, 'date');
            $format = $dateFormats[$part];
            self::$_localeDateFormat[$part][(string) $locale] = $format;
        }

        return self::$_localeDateFormat[$part][(string) $locale];
    }

    public static function getLocaleTimeFormat($part = 'medium', $locale = null)
    {
        if (!$locale) {
            $locale = Zend_Registry::get('Zend_Locale');
        }
        $dateFormats = Zend_Locale_Data::getList($locale, 'time');
        $format = $dateFormats[$part];
        return $format;
    }

    public static function getLocaleDateTimeFormat($partDate = 'short', $partTime = 'medium', $locale = null)
    {
        if (!$locale) {
            $locale = Zend_Registry::get('Zend_Locale');
        }
        $format = trim(
                self::getLocaleDateFormat($partDate, $locale)
                . ' '
                . self::getLocaleTimeFormat($partTime, $locale)
        );

        return $format;
    }

    public static function getJqueryDatePickerFormat($format = null)
    {
        if (!$format) {
            $format = self::getLocaleDateFormat();
        }
        $newFormat = ZendX_JQuery_View_Helper_DatePicker::resolveZendLocaleToDatePickerFormat($format);
        return $newFormat;
    }

    public static function getHowLongAgo(Zend_Date $date, $ago = 'ago')
    {
        $howLongAgo = "";
        $locale = Zmz_Culture::getLocale();
        $unitArray = Zend_Locale::getTranslationList('Unit');
        $display = array(
            0 => $unitArray['duration-year'],
            1 => $unitArray['duration-month'],
            2 => $unitArray['duration-day'],
            3 => $unitArray['duration-hour'],
            4 => $unitArray['duration-minute'],
            5 => $unitArray['duration-second'],
        );
        $eventTimestamp = $date->getTimestamp();

        $totaldelay = time() - ($eventTimestamp);

        $value = 0;
        $unit = null;
        if ($totaldelay <= 0) {
            return '';
        }

        $array = array(0 => 31536000, 1 => 2678400, 2 => 86400, 3 => 3600, 4 => 60, 5 => 1);

        foreach ($array as $k => $v) {
            $value = floor($totaldelay / $v);
            if ($value) {
                $totaldelay = $totaldelay % $v;
                $unit = $k;
                break;
            }
        }
        if ($value && $unit !== null) {
            $baseString = $value > 1 ? @$display[$unit]['other'] : @$display[$unit]['one'];

            if ($ago) {
                $ago = ' ' . $ago;
            }
            $howLongAgo = Zmz_String::format($baseString, $value) . $ago;
        }
        return $howLongAgo;
    }

    public static function convertDateFormatToJquery($format = null)
    {
        if (!$format) {
            $format = self::getLocaleDateFormat();
        }
        $newFormat = ZendX_JQuery_View_Helper_DatePicker::resolveZendLocaleToDatePickerFormat($format);
        return $newFormat;
    }

    public static function getExcelDateTime(Zend_Date $date)
    {
        $tmpDate = clone $date;
        $diffDates = $tmpDate->toValue() - $tmpDate->getGmtOffset();
        $diffDays = ($diffDates / 60 / 60 / 24) + 25569;

        return $diffDays;
    }

    public static function getExcelDate(Zend_Date $date)
    {
        $tmpDate = clone $date;
        $tmpDate->setHour(0);
        $tmpDate->setMinute(0);
        $tmpDate->setSecond(0);

        $diffDates = $tmpDate->toValue() - $tmpDate->getGmtOffset();
        $diffDays = ceil($diffDates / 60 / 60 / 24) + 25569;
        return $diffDays;
    }

    public static function getExcelTime(Zend_Date $date)
    {
        // seconds in a day
        $totalSeconds = 3600 * 24;
        $time = ($date->get(Zend_Date::HOUR) * 3600) + ($date->get(Zend_Date::MINUTE) * 60) + ($date->get(Zend_Date::SECOND));

        return $time / $totalSeconds;
    }

}

