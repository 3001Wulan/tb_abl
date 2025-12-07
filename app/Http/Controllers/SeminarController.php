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
use Illuminate\Support\Facades\Log;

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
                if (User::find($ex['id'])) {
                    $seminar->examiners()->attach($ex['id'], ['role' => $ex['role'] ?? 'primary']);
                }
            }
        }

        // 3. Load relationships sebelum notifikasi
        $seminar->load(['student', 'examiners']);

        // 4. Send notification
        try {
            $targets = collect();
            
            if ($seminar->student) {
                $targets->push($seminar->student);
            }
            
            if ($seminar->examiners->isNotEmpty()) {
                $targets = $targets->merge($seminar->examiners);
            }
            
            if ($targets->isNotEmpty()) {
                Notification::send($targets->unique('id'), new SeminarScheduled($seminar, 'Seminar telah dijadwalkan.'));
            }
        } catch (\Exception $e) {
            // notification gagal tidak menggagalkan insert
            Log::error('Failed to send seminar notification: ' . $e->getMessage());
        }

        return (new SeminarResource($seminar))->response()->setStatusCode(201);
    }

    public function update(UpdateSeminarRequest $request, Seminar $seminar)
    {
        return DB::transaction(function () use ($request, $seminar) {
            // Update seminar (tambahkan student_id jika student bisa diubah)
            $seminar->update($request->only([
                'title',
                'student_id',
                'scheduled_at',
                'notes',
                'status'
            ]));

            // Sync examiners jika ada di request
            if ($request->has('examiners')) {
                $syncData = [];
                foreach ($request->examiners as $ex) {
                    $syncData[$ex['id']] = ['role' => $ex['role'] ?? 'primary'];
                }
                $seminar->examiners()->sync($syncData);
            }

            // Load relationships sebelum notifikasi
            $seminar->load(['student', 'examiners']);

            // Kirim notifikasi dengan error handling
            try {
                $targets = collect();
                
                if ($seminar->student) {
                    $targets->push($seminar->student);
                }
                
                if ($seminar->examiners->isNotEmpty()) {
                    $targets = $targets->merge($seminar->examiners);
                }

                if ($targets->isNotEmpty()) {
                    Notification::send(
                        $targets->unique('id'), 
                        new SeminarScheduled($seminar, 'Ada perubahan pada jadwal seminar.')
                    );
                }
            } catch (\Exception $e) {
                // Log error tapi tidak gagalkan update
                Log::error('Failed to send seminar update notification: ' . $e->getMessage());
            }

            return new SeminarResource($seminar);
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

        // Load relationships
        $seminar->load(['student', 'examiners']);

        // notify
        try {
            $targets = collect();
            
            if ($seminar->student) {
                $targets->push($seminar->student);
            }
            
            if ($seminar->examiners->isNotEmpty()) {
                $targets = $targets->merge($seminar->examiners);
            }

            if ($targets->isNotEmpty()) {
                Notification::send($targets->unique('id'), new SeminarScheduled($seminar, 'Anda ditunjuk sebagai penguji seminar.'));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send examiner assignment notification: ' . $e->getMessage());
        }

        return new SeminarResource($seminar);
    }

    // Endpoint khusus: notify manually with custom message
    public function notify(Request $request, Seminar $seminar)
    {
        $data = $request->validate(['message' => 'required|string|max:1000']);

        // Load relationships
        $seminar->load(['student', 'examiners']);

        $targets = collect();
        
        if ($seminar->student) {
            $targets->push($seminar->student);
        }
        
        if ($seminar->examiners->isNotEmpty()) {
            $targets = $targets->merge($seminar->examiners);
        }

        if ($targets->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada target notifikasi (student atau examiner tidak ditemukan)'
            ], 400);
        }

        try {
            Notification::send($targets->unique('id'), new SeminarScheduled($seminar, $data['message']));
            return response()->json(['message' => 'Notifikasi dikirim'], 200);
        } catch (\Exception $e) {
            Log::error('Failed to send manual notification: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengirim notifikasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}