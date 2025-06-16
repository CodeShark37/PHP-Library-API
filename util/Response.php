<?php


class Response
{
    public static function send($message = "")
    {
        http_response_code(200);
        return json_encode(array(
            'message' => $message
        ));
        exit();
    }
    public static function sendWithCode($code,$message = "")
    {
        http_response_code($code);
        return json_encode(array(
            'message' => $message
        ));
        exit();
    }
    public static function sendEmpty($code)
    {
        http_response_code($code);
        exit();
    }
}
