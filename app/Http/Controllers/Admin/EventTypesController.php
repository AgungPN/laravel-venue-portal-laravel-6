<?php

namespace App\Http\Controllers\Admin;

use App\EventType;
use App\Services\EventService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Repositories\EventRepository;
use App\Http\Requests\StoreEventTypeRequest;
use App\Http\Requests\UpdateEventTypeRequest;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\MassDestroyEventTypeRequest;
use App\Http\Controllers\Traits\MediaUploadingTrait;

class EventTypesController extends Controller
{
    use MediaUploadingTrait;

    private EventService $eventService;
    private EventRepository $eventRepository;

    public function __construct(EventService $eventService, EventRepository $eventRepository)
    {
        $this->eventService = $eventService;
        $this->eventRepository = $eventRepository;
    }

    public function index()
    {
        abort_if(Gate::denies('event_type_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $eventTypes = EventType::all();

        return view('admin.eventTypes.index', compact('eventTypes'));
    }

    public function create()
    {
        abort_if(Gate::denies('event_type_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.eventTypes.create');
    }

    public function store(StoreEventTypeRequest $request)
    {
        try {
            $this->eventService->store($request);
        } catch (\Throwable $th) {
            abort($th->getCode(), $th->getMessage());
        }
        return redirect()->route('admin.event-types.index');
    }

    public function edit(EventType $eventType)
    {
        abort_if(Gate::denies('event_type_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.eventTypes.edit', compact('eventType'));
    }

    public function update(UpdateEventTypeRequest $request, EventType $eventType)
    {
        try {
            $this->eventService->update($request, $eventType);
        } catch (\Throwable $th) {
            abort($th->getCode(), $th->getMessage());
        }

        return redirect()->route('admin.event-types.index');
    }

    public function show(EventType $eventType)
    {
        abort_if(Gate::denies('event_type_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.eventTypes.show', compact('eventType'));
    }

    public function destroy(EventType $eventType)
    {
        abort_if(Gate::denies('event_type_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $eventType->delete();

        return back();
    }

    public function massDestroy(MassDestroyEventTypeRequest $request)
    {
        $this->eventService->removeMany($request->ids);

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
