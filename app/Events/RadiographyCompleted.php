<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;

class RadiographyCompleted
{
    use SerializesModels;

    public $request;

    public function __construct($request)
    {
        $this->request = $request;
    }
}
