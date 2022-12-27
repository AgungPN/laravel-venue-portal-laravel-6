<?php
namespace App\Services;

use App\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class LocationService
{

    public function store(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $location = Location::create($request->validated());

                if ($request->input('photo', false)) {
                    $location->addMedia(storage_path('tmp/uploads/' . $request->input('photo')))->toMediaCollection('photo');
                }
            });
        } catch (\Throwable $th) {
            throw new \Exception($th, Response::HTTP_BAD_REQUEST);
        }
    }

    public function update(Request $request, Location $location)
    {
        try {
            $location->update($request->validated());

            if ($request->input('photo', false)) {
                if (!$location->photo || $request->input('photo') !== $location->photo->file_name) {
                    $location->addMedia(storage_path('tmp/uploads/' . $request->input('photo')))->toMediaCollection('photo');
                }
            } elseif ($location->photo) {
                $location->photo->delete();
            }
        } catch (\Throwable $th) {
            throw new \Exception($th, Response::HTTP_BAD_REQUEST);
        }
    }

    public function removeMany(...$ids)
    {
        return Location::whereIn('id', $ids)->delete();
    }
}
