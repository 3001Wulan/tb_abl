<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSeminarRequest;
use App\Http\Requests\UpdateSeminarRequest;
use App\Models\Seminar;
use App\Models\User;
use App\Notifications\SeminarScheduled;
use App\Http\Resources\SeminarResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;

class SeminarController extends Controller
{
    public function index(Request $request)
    {
        $seminars = Seminar::with(['student','examiners'])->paginate(15);
        return SeminarResource::collection($seminars);
    }

    public function show(Seminar $seminar)
    {
        $seminar->load(['student','examiners']);
        return new SeminarResource($seminar);
    }

   public function store(StoreSeminarRequest $request)
{
    // 1. Insert seminar dulu
    $seminar = Seminar::create($request->only(['title','student_id','scheduled_at','notes']));

    // 2. Attach examiners jika ada
    if ($request->filled('examiners')) {
        foreach ($request->examiners as $ex) {
            if (\App\Models\User::find($ex['id'])) {
                $seminar->examiners()->attach($ex['id'], ['role' => $ex['role'] ?? 'primary']);
            }
        }
    }

    // 3. Send notification
    try {
        $targets = collect();
        $targets->push($seminar->student);
        $targets = $targets->merge($seminar->examiners);
        Notification::send($targets->unique('id'), new SeminarScheduled($seminar, 'Seminar telah dijadwalkan.'));
    } catch (\Exception $e) {
        // notification gagal tidak menggagalkan insert
    }

    return (new SeminarResource($seminar->load(['student','examiners'])))->response()->setStatusCode(201);
}

    public function update(UpdateSeminarRequest $request, Seminar $seminar)
{
    return DB::transaction(function () use ($request, $seminar) {
        $seminar->update($request->only(['title','scheduled_at','notes','status']));

        if ($request->has('examiners')) {
            $syncData = [];
            foreach ($request->examiners as $ex) {
                $syncData[$ex['id']] = ['role' => $ex['role'] ?? 'primary'];
            }
            $seminar->examiners()->sync($syncData);
        }

        // Notifikasi aman untuk semua kasus
        $targets = collect();
if ($seminar->student) $targets->push($seminar->student);
if ($seminar->examiners) $targets = $targets->merge($seminar->examiners);

Notification::send(
    $targets->unique('id'), 
    new SeminarScheduled($seminar, 'Ada perubahan pada jadwal seminar.')
);


        return new SeminarResource($seminar->load(['student','examiners']));
    });
}

    public function destroy(Seminar $seminar)
    {
        $seminar->delete();
        return response()->json(['message'=>'Seminar dihapus']);
    }

    // Endpoint khusus: assign penguji
    public function assignExaminers(Request $request, Seminar $seminar)
    {
        $data = $request->validate([
            'examiners' => 'required|array|min:1',
            'examiners.*.id' => 'required|exists:users,id',
            'examiners.*.role' => 'in:primary,secondary',
        ]);

        $syncData = [];
        foreach ($data['examiners'] as $ex) {
            $syncData[$ex['id']] = ['role' => $ex['role'] ?? 'primary'];
        }
        $seminar->examiners()->sync($syncData);

        // notify
        $targets = $seminar->examiners;
        $targets->push($seminar->student);

        Notification::send($targets->unique('id'), new SeminarScheduled($seminar, 'Anda ditunjuk sebagai penguji seminar.'));

        return new SeminarResource($seminar->load(['student','examiners']));
    }

    // Endpoint khusus: notify manually with custom message
  public function notify(Request $request, Seminar $seminar)
{
    $data = $request->validate(['message' => 'required|string|max:1000']);

    $targets = $seminar->examiners;
    $targets->push($seminar->student);

    try {
        Notification::send($targets->unique('id'), new SeminarScheduled($seminar, $data['message']));
        return response()->json(['message' => 'Notifikasi dikirim'], 200);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Gagal mengirim notifikasi',
            'error' => $e->getMessage()
        ], 500);
    }
}

}
