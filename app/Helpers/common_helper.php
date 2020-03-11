<?php

/* Function used to get unique id*/
if (!function_exists('get_unique_id')) {
 
    function get_unique_id($table)
    {
        $tabledata = DB::table($table)->select('id','unique_id')->orderby('id', 'desc')->first();
        if(empty($tabledata))
        {
            $uniqueId = 10000;
        }
        else
        {
            $uniqueId = $tabledata->unique_id + 1;
        }
        return $uniqueId;
    }
}

?>