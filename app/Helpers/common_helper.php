<?php

use App\Mail\DemoEmail;
use Illuminate\Support\Facades\Mail;

/* Function used to get unique id*/
if (!function_exists('get_unique_id')) {
    function get_unique_id($table)
    {
        $tabledata = DB::table($table)->select('id', 'unique_id')->orderby('id', 'desc')->first();
        if (empty($tabledata)) {
            $uniqueId = 10000;
        } else {
            $uniqueId = $tabledata->unique_id + 1;
        }
        return $uniqueId;
    }
}


    function send($data) {

        $objDemo = new \stdClass();
        $to_name = $data['TO'];
       // $from = $data['FROM'];
        //$from_name = $data['SITE_NAME'];
        $objDemo->view = $data['VIEW'];
        $subject = $data['SUBJECT'];
        $to_email = strtolower( trim( $data['TO'] ) );
        Mail::send($objDemo->view, $data, function($message) use($to_email,$subject)
        {      
            $message->from('hiral.devtsree@gmail.com','Fingertips');
            $message->to($to_email , 'HTML Email' )
            ->subject( $subject);
            
        });

        return true;
    }

?>