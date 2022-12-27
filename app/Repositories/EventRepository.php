<?php

namespace App\Repositories;

use App\EventType;

class EventRepository
{

    public function getOneBySlug(string $slug)
    {
        return EventType::where('slug', $slug)->firstOrFail();
    }
}
