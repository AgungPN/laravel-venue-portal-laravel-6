<?php

namespace App\Http\Controllers;

use App\Location;
use App\Repositories\VenueRepository;

class LocationController extends Controller
{

    public function index($slug)
    {
        $location = Location::where('slug', $slug)->firstOrFail();

        $venues = (new VenueRepository)->getListByLocation($location);

        return view('location', compact('venues', 'location'));
    }
}
