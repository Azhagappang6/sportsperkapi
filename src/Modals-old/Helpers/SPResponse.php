<?php

namespace App\Modals\Helpers;

class SPResponse
{
    public $status;
    public $message;
    public $content;

    function __construct($status, $message = NULL, $content = NULL)
    {
        $this->status = $status;
        $this->message = $message;
        $this->content = $content;
    }
}
