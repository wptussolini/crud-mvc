<?php

namespace App\src;

class Request
{
    /**
     * Request constructor.
     */
    function __construct()
    {
        $this->bootstrapSelf();
    }

    /**
     *
     */
    private function bootstrapSelf()
    {
        foreach($_SERVER as $key => $value)
        {
            $this->{$this->toCamelCase($key)} = $value;
        }
        if (strpos($this->requestUri, 'show')) {
            $this->id = explode('/', $this->requestUri)[2];
        }
    }

    /**
     * @param  $string
     * @return mixed|string
     */
    private function toCamelCase($string)
    {
        $result = strtolower($string);

        preg_match_all('/_[a-z]/', $result, $matches);
        foreach($matches[0] as $match)
        {
            $c = str_replace('_', '', strtoupper($match));
            $result = str_replace($match, $c, $result);
        }
        return $result;
    }

    /**
     * @return array|void
     */
    public function getBody()
    {
        if($this->requestMethod === "GET") {
            return;
        }
        if ($this->requestMethod == "POST") {
            $result = array();
            foreach($_POST as $key => $value)
            {
                $result[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
            return $result;
        }

        return $body;
    }

    /**
     * @return mixed
     */
    public function all()
    {
        return $_POST;
    }

    /**
     * @param  $name
     * @return null
     */
    public function __get($name)
    {
        return isset($_POST[$name]) ? $_POST[$name] : null;
    }
}