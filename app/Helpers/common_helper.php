<?php
use App\User;
use App\Brand;

if (!function_exists('get_unique_id')) {
    /**
     * Returns a human readable file size
     *
     * @param integer $bytes
     * Bytes contains the size of the bytes to convert
     *
     * @param integer $decimals
     * Number of decimal places to be returned
     *
     * @return string a string in human readable format
     *
     * */
    function get_unique_id()
    {
        $user = User::orderby('id', 'desc')->first();
        $uniqueId = $user->unique_id + 1;
        return $uniqueId;
    }
    function get_brand_unique_id()
    {
        $user = Brand::orderby('id', 'desc')->first();
        $uniqueId = $user->unique_id + 1;
        return $uniqueId;
    }
}

?>