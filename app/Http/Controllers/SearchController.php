<?php

namespace App\Http\Controllers;

use App\Services\VenueService;
use App\Venue;

class SearchController extends Controller
{

    public function index()
    {
        $venues = (new VenueService)->search();

        return view('search', compact('venues'));
    }
}
