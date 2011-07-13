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

    public static function printDateFromDb($date, $format = null, Zend_Date $defaultDate = null)
    {
        try {
            $defaultDateString = self::getSqlDateTime($defaultDate, false, true);
            $date = self::getDateFromDb($date, $defaultDateString);
        } catch (Zmz_Date_Exception $e) {
            return '';
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

    public static function getHowLongAgo($date, $ago = 'ago')
    {
        $locale = Zmz_Culture::getLocale();
        $unit = Zend_Locale::getTranslationList('Unit');
        $display = array(
            0 => $unit['year'],
            1 => $unit['month'],
            2 => $unit['day'],
            3 => $unit['hour'],
            4 => $unit['minute'],
            5 => $unit['second'],
        );

        $givenDate = clone $date;
        $givenDate = self::toUTC($givenDate);
        $now = self::toUTC();

        if ($givenDate->isLater($now)) {
            return '';
        }

        $dateCompare = getdate($givenDate->getTimestamp());
        $current = getdate($now->getTimestamp());
        $p = array('year', 'mon', 'mday', 'hours', 'minutes', 'seconds');
        $factor = array(0, 12, 30, 24, 60, 60);

        for ($i = 0; $i < 6; $i++) {
            if ($i > 0) {
                $current[$p[$i]] += $current[$p[$i - 1]] * $factor[$i];
                $dateCompare[$p[$i]] += $dateCompare[$p[$i - 1]] * $factor[$i];
            }
            if ($current[$p[$i]] - $dateCompare[$p[$i]] >= 1) {
                $value = $current[$p[$i]] - $dateCompare[$p[$i]];
                $string = str_replace('{0}', $value, ($value != 1) ? $display[$i]['other'] : $display[$i]['one']) . ' ' . $ago;

                return $string;
            }
        }
        $diffDates = $now->sub($givenDate);
        $value = (int) $diffDates->get(Zend_Date::SECOND_SHORT);
        return str_replace('{0}', $value, ($value != 1) ? $display[5]['other'] : $display[5]['one']) . ' ' . $ago;
    }

    public static function convertDateFormatToJquery($format = null)
    {
        if (!$format) {
            $format = self::getLocaleDateFormat();
        }
        $newFormat = ZendX_JQuery_View_Helper_DatePicker::resolveZendLocaleToDatePickerFormat($format);
        return $newFormat;
    }

}

