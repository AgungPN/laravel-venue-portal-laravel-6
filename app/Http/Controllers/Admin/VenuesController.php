<?php

namespace App\Http\Controllers\Admin;

use App\Venue;
use App\Location;
use App\EventType;
use App\Services\VenueService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Repositories\VenueRepository;
use App\Http\Requests\StoreVenueRequest;
use App\Http\Requests\UpdateVenueRequest;
use App\Http\Requests\MassDestroyVenueRequest;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Traits\MediaUploadingTrait;

class VenuesController extends Controller
{
    use MediaUploadingTrait;

    private VenueService $venueService;
    private VenueRepository $venueRepository;
    private Location $locations;
    private EventType $event_types;

    public function __construct(VenueService $venueService, VenueRepository $venueRepository)
    {
        $this->venueService = $venueService;
        $this->venueRepository = $venueRepository;

        $this->locations = $this->venueRepository->getLocations();
        $this->event_types = $this->venueRepository->getEventType();
    }

    public function index()
    {
        abort_if(Gate::denies('venue_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $venues = Venue::all();

        return view('admin.venues.index', compact('venues'));
    }

    public function create()
    {
        abort_if(Gate::denies('venue_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.venues.create', [
            'locations' => $this->locations,
            'event_types' => $this->event_types
        ]);
    }

    public function store(StoreVenueRequest $request)
    {
        try {
            $this->venueService->store($request);
        } catch (\Throwable $th) {
            abort($th->getCode(), $th->getMessage());
        }

        return redirect()->route('admin.venues.index');
    }

    public function edit(Venue $venue)
    {
        abort_if(Gate::denies('venue_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $venue->load('location', 'event_types');

        return view('admin.venues.edit', [
            'locations' => $this->locations,
            'event_types' => $this->event_types,
            'venue' => $venue
        ]);
    }

    public function update(UpdateVenueRequest $request, Venue $venue)
    {
        try {
            $this->venueService->update($request, $venue);
        } catch (\Throwable $th) {
            abort($th->getCode(), $th->getMessage());
        }

        return redirect()->route('admin.venues.index');
    }

    public function show(Venue $venue)
    {
        abort_if(Gate::denies('venue_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $venue->load('location', 'event_types');

        return view('admin.venues.show', compact('venue'));
    }

    public function destroy(Venue $venue)
    {
        abort_if(Gate::denies('venue_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $venue->delete();

        return back();
    }

    public function massDestroy(MassDestroyVenueRequest $request)
    {
        $this->venueRepository->deleteIn(request('ids'));
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
