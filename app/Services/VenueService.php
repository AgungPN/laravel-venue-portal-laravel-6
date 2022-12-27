<?php

namespace App\Services;

use App\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class VenueService
{
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $venue = Venue::create($request->validated());
            $venue->event_types()->sync($request->input('event_types', []));

            if ($request->input('main_photo', false)) {
                $venue->addMedia(storage_path('tmp/uploads/' . $request->input('main_photo')))->toMediaCollection('main_photo');
            }

            foreach ($request->input('gallery', []) as $file) {
                $venue->addMedia(storage_path('tmp/uploads/' . $file))->toMediaCollection('gallery');
            }

            DB::commit();
        } catch (\Exception $th) {
            DB::rollBack();
            throw new \Exception($th, Response::HTTP_BAD_REQUEST);
        }
    }

    public function update(Request $request, Venue $venue)
    {
        try {
            DB::beginTransaction();

            $venue->update($request->all());
            $venue->event_types()->sync($request->input('event_types', []));

            if ($request->input('main_photo', false)) {
                if (!$venue->main_photo || $request->input('main_photo') !== $venue->main_photo->file_name) {
                    $venue->addMedia(storage_path('tmp/uploads/' . $request->input('main_photo')))->toMediaCollection('main_photo');
                }
            } elseif ($venue->main_photo) {
                $venue->main_photo->delete();
            }

            if (count($venue->gallery) > 0) {
                foreach ($venue->gallery as $media) {
                    if (!in_array($media->file_name, $request->input('gallery', []))) {
                        $media->delete();
                    }
                }
            }

            $media = $venue->gallery->pluck('file_name')->toArray();

            foreach ($request->input('gallery', []) as $file) {
                if (count($media) === 0 || !in_array($file, $media)) {
                    $venue->addMedia(storage_path('tmp/uploads/' . $file))->toMediaCollection('gallery');
                }
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw new \Exception($th, Response::HTTP_BAD_REQUEST);
        }
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
}
