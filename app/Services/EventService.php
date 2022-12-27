<?php

namespace App\Services;

use App\EventType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class EventService
{
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $eventType = EventType::create($request->validated());

            if ($request->input('photo', false)) {
                $eventType->addMedia(storage_path('tmp/uploads/' . $request->input('photo')))->toMediaCollection('photo');
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();

            throw new \Exception($th->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function update(Request $request, EventType $eventType)
    {
        try {
            DB::beginTransaction();

            $eventType->update($request->validated());

            if ($request->input('photo', false)) {
                if (!$eventType->photo || $request->input('photo') !== $eventType->photo->file_name) {
                    $eventType->addMedia(storage_path('tmp/uploads/' . $request->input('photo')))->toMediaCollection('photo');
                }
            } elseif ($eventType->photo) {
                $eventType->photo->delete();
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();

            throw new \Exception($th->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function removeMany(...$ids)
    {
        return EventType::whereIn('id', $ids)->delete();
    }
}
