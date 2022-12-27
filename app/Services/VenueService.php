<?php

namespace App\Services;

use App\Venue;

class VenueService
{
    public function getBySlug(string $slug)
    {
        // in service because it more uses business logic
        return Venue::with('event_types')
            ->whereHas('event_types', function ($q) use ($slug) {
                $q->where('event_types.slug', $slug);
            })
            ->latest()
            ->paginate(9);
    }

    public function search()
    {
        $venues = Venue::with('event_types')
            ->when(request('event_type'), function ($query) {
                return $query->whereHas('event_types', function ($q) {
                    $q->where('event_types.id', request('event_type'));
                });
            })
            ->when(request('people_amount'), function ($query) {
                return $query->where('venues.people_minimum', '<=', request('people_amount'))
                    ->where('venues.people_maximum', '>=', request('people_amount'));
            })
            ->when(request('location'), function ($query) {
                return $query->where('venues.location_id', request('location'));
            })
            ->latest()
            ->paginate(3);

        return $venues;
    }
}
