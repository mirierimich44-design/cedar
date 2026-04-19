<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;

class LabTestCompleted
{
    use SerializesModels;

    public $request;

    public function __construct($request)
    {
        $this->request = $request;
    }
}
