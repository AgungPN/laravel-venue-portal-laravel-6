<?php

namespace App\Repositories;

use App\Location;
use App\Venue;

class VenueRepository
{
    public function getOneBySlugAndId(string $slug, int $id)
    {
        return Venue::with('event_types', 'location')->where('slug', $slug)->where('id', $id)->firstOrFail();
    }

    function takeByVenue($venue, int $take)
    {
        Venue::with('event_types')->where('location_id', $venue->location_id)
            ->where('id', '!=', $venue->id)->take($take)->get();
    }

    public function getListByLocation(Location $location)
    {
        return Venue::with('event_types')
            ->where('location_id', $location->id)
            ->latest()
            ->paginate(9);
    }

    public function take(int $take)
    {
        return Venue::with('event_types')->latest()->take($take)->get();
    }
}
