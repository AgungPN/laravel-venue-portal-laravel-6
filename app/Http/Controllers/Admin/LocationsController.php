<?php

namespace App\Http\Controllers\Admin;

use App\Location;
use App\Services\LocationService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Repositories\LocationRepository;
use App\Http\Requests\StoreLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\MassDestroyLocationRequest;
use App\Http\Controllers\Traits\MediaUploadingTrait;

class LocationsController extends Controller
{
    use MediaUploadingTrait;

    private LocationService $locationService;
    private LocationRepository $locationRepository;

    public function __construct(LocationService $locationService, LocationRepository $locationRepository)
    {
        $this->locationService = $locationService;
        $this->locationRepository = $locationRepository;
    }

    public function index()
    {
        abort_if(Gate::denies('location_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.locations.index', ['locations' => Location::all()]);
    }

    public function create()
    {
        abort_if(Gate::denies('location_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.locations.create');
    }

    public function store(StoreLocationRequest $request)
    {
        try {
            $this->locationService->store($request);
        } catch (\Throwable $th) {
            abort($th->getCode(), $th->getMessage());
        }
        return redirect()->route('admin.locations.index');
    }

    public function edit(Location $location)
    {
        abort_if(Gate::denies('location_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.locations.edit', compact('location'));
    }

    public function update(UpdateLocationRequest $request, Location $location)
    {
        try {
            $this->locationService->update($request, $location);
        } catch (\Throwable $th) {
            abort($th->getCode(), $th->getMessage());
        }
        return redirect()->route('admin.locations.index');
    }

    public function show(Location $location)
    {
        abort_if(Gate::denies('location_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.locations.show', compact('location'));
    }

    public function destroy(Location $location)
    {
        abort_if(Gate::denies('location_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $location->delete();

        return back();
    }

    public function massDestroy(MassDestroyLocationRequest $request)
    {
        $this->locationService->removeMany(request('ids'));

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
