<?php

class Zmz_View_Helper_UrlQuery extends Zend_View_Helper_Url
{

    public function urlQuery($urlOptions = array(), $name = null, $reset = false, $encode = true)
    {
        $queryArray = $this->getQueryArray();
        if (count($queryArray)) {
            $request = Zend_Controller_Front::getInstance()->getRequest();
            $params = $request->getParams();
            $valuesToRemove = array();

            foreach ($queryArray as $k => $v) {
                // select query string elements with no values
                if ($v == '') {
                    array_push($valuesToRemove, $k);
                }
            }
            
            $queryString = $this->clean_query($valuesToRemove);
            empty($queryString) ? $queryString = '' : $queryString = '?' . $queryString;
        } else {
            $queryString = '';
        }
        $url = parent::url($urlOptions, $name, $reset, $encode);
        return $url . $queryString;
    }

    /**
     * Remove key/values from query string
     *
     * @param array $values keys to remove
     * @return string $query
     */
    protected function clean_query($values = null)
    {
        if ($values === null)
            $values = array();

        $values = array_flip($values);
        $query = $this->getQueryArray();

        foreach ($query as $k => $v) {
            if (isset($values[$k])) {
                unset($query[$k]);
            }
        }
        $query = $this->composeQueryString($query);

        return $query;
    }

    /**
     * Get query string in associative array format
     *
     * @return array $query
     */
    protected function getQueryArray()
    {
        $query = array();

        if (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] != '') {
            $array = explode("&", $_SERVER['QUERY_STRING']);
            foreach ($array as $k => $v) {
                $tmp = explode("=", $v, 2);
                $query[$tmp[0]] = $tmp[1];
            }
        }
        return $query;
    }

    /**
     * Compose a query string from an associative array.
     * Example: key1=val1&key2=val2
     * <code>
     * $query = array('key1' => 'val1', 'key2' => 'val2');
     * </code>
     *
     * @return string $newQuery
     */
    protected function composeQueryString($query)
    {
        if (!is_array($query)) {
            throw new Exception('"$query" must be an associative array');
        }

        if (!count($query)) {
            return "";
        }

        $newQuery = array();
        foreach ($query as $k => $v) {
            $tmp = $k . "=" . $v;
            array_push($newQuery, $tmp);
        }
        $newQuery = implode("&", $newQuery);

        return $newQuery;
    }

}