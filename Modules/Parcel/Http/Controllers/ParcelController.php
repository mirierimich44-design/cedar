<?php

namespace Modules\Parcel\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ParcelController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('parcel::index');
    }
}
