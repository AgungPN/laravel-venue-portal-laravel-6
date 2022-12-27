<?php

namespace App\Repositories;

use App\Venue;
use App\Location;
use App\EventType;

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

    public function deleteIn(...$ids)
    {
        return Venue::whereIn('id', $ids)->delete();
    }

    public function getEventType()
    {
        return EventType::all()->pluck('name', 'id');
    }

    public function getLocations()
    {
        return Location::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
    }
}
