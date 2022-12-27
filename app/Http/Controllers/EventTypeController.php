<?php

namespace App\Http\Controllers;

use App\Services\VenueService;
use App\Repositories\EventRepository;

class EventTypeController extends Controller
{

    private VenueService $venueService;
    private EventRepository $eventRepository;

    public function __construct(VenueService $venueService, EventRepository $eventRepository)
    {
        $this->venueService = $venueService;
        $this->eventRepository = $eventRepository;
    }

    public function index($slug)
    {
        $eventType = $this->eventRepository->getOneBySlug($slug);

        $venues = $this->venueService->getBySlug($slug);

        return view('event_type', compact('venues', 'eventType'));
    }
}
