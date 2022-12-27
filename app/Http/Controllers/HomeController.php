<?php

namespace App\Http\Controllers;

use App\EventType;
use App\Location;
use App\Repositories\VenueRepository;
use App\Services\VenueService;
use App\Venue;

class HomeController extends Controller
{

    public function index()
    {
        $featuredVenues = Venue::where('is_featured', 1)->get();

        $eventTypes = EventType::all();
        $locations = Location::all();

        $newestVenues = (new VenueRepository())->take(3);

        return view('home', compact('featuredVenues', 'eventTypes', 'locations', 'newestVenues'));
    }
}
