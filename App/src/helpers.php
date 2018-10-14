<?php
function view($viewName, $vars = [], $layout = true)
{
    $path = BASE_PATH.'/App/Views';
    include_once $path.'/layout.phtml';
}

function content($viewName, $vars)
{
    foreach ($vars as $key => $value) {
        $$key = $value;
    }
    $path = BASE_PATH.'/App/Views';
    include_once $path.'/'.$viewName.'.phtml';
}

function clearMask($number)
{
    return preg_replace("/[^A-Za-z0-9\-]/", "", str_replace('-', '', $number));
}