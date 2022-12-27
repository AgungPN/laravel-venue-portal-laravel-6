<?php

namespace App\Http\Controllers;

use App\Repositories\VenueRepository;
use App\Venue;

class VenueController extends Controller
{

    private VenueRepository $venueRepository;

    public function __construct(VenueRepository $venueRepository)
    {
        $this->venueRepository = $venueRepository;
    }

    public function show($slug, $id)
    {
        $venue = $this->venueRepository->getOneBySlugAndId($slug, $id);

        $relatedVenues = $this->venueRepository->takeByVenue($venue, 3);

        return view('venue', compact('venue', 'relatedVenues'));
    }
}
