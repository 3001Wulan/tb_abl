<?php

namespace App\Http\Controllers;

use App\Models\JadwalSeminarKp;
use App\Http\Requests\StoreJadwalSeminarKPRequest;
use App\Http\Requests\UpdateJadwalSeminarKPRequest;
use App\Http\Resources\JadwalSeminarKPResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "Jadwal Seminar KP",
    description: "Operasi CRUD untuk Mengelola Jadwal Seminar Kerja Praktek Mahasiswa"
)]
class JadwalSeminarKPController extends Controller
{
    #[OA\Get(
        path: "/api/jadwal-seminar-kp",
        summary: "Menampilkan Semua Jadwal Seminar KP",
        tags: ["Jadwal Seminar KP"],
        parameters: [
            new OA\Parameter(
                name: "page",
                in: "query",
                description: "Halaman",
                required: false,
                schema: new OA\Schema(type: "integer", default: 1)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Data Semua Jadwal Seminar KP",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(ref: "#/components/schemas/JadwalSeminarKP")
                        ),
                        new OA\Property(
                            property: "links",
                            type: "object"
                        ),
                        new OA\Property(
                            property: "meta",
                            type: "object"
                        )
                    ]
                )
            )
        ]
    )]
   public function index(Request $request)
{
    $query = JadwalSeminarKp::with('student');
    
    // ✅ Tambahkan filter by student_id
    if ($request->has('student_id')) {
        $query->where('student_id', $request->student_id);
    }
    
    $jadwal = $query->paginate(15);

    // Custom response untuk index (cuma beberapa field)
    return response()->json([
        'data' => $jadwal->map(function($item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'student' => [ // ✅ Tambahkan student info
                    'id' => $item->student->id,
                    'name' => $item->student->name,
                    'email' => $item->student->email,
                ],
                'scheduled_at' => $item->scheduled_at,
                'status' => $item->status, // ✅ Tambahkan status
                'notes' => $item->notes,
            ];
        }),
        'links' => [
            'first' => $jadwal->url(1),
            'last' => $jadwal->url($jadwal->lastPage()),
            'prev' => $jadwal->previousPageUrl(),
            'next' => $jadwal->nextPageUrl(),
        ],
        'meta' => [
            'current_page' => $jadwal->currentPage(),
            'last_page' => $jadwal->lastPage(),
            'per_page' => $jadwal->perPage(),
            'total' => $jadwal->total(),
        ]
    ]);
}

    #[OA\Get(
        path: "/api/jadwal-seminar-kp/{id}",
        summary: "Menampilkan Detail Satu Jadwal Seminar KP",
        tags: ["Jadwal Seminar KP"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID jadwal seminar KP",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Detail Jadwal Seminar KP",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "data",
                            ref: "#/components/schemas/JadwalSeminarKP"
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Jadwal Tidak Ditemukan"
            )
        ]
    )]
    public function show(JadwalSeminarKp $jadwalSeminarKp)
    {
        $jadwalSeminarKp->load('student');

        return new JadwalSeminarKPResource($jadwalSeminarKp);
    }

    #[OA\Post(
        path: "/api/jadwal-seminar-kp",
        summary: "Membuat Jadwal Seminar KP yang Baru",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/StoreJadwalSeminarKPRequest")
        ),
        tags: ["Jadwal Seminar KP"],
        responses: [
            new OA\Response(
                response: 201,
                description: "Jadwal Berhasil dibuat",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "data",
                            ref: "#/components/schemas/JadwalSeminarKP"
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Jadwal Gagal dibuat"
            )
        ]
    )]
    public function store(StoreJadwalSeminarKPRequest $request)
    {
        $jadwal = JadwalSeminarKp::create($request->only([
            'title',
            'student_id',
            'scheduled_at',
            'notes',
            'status'
        ]));

        $jadwal->load('student');

        return (new JadwalSeminarKPResource($jadwal))
            ->response()
            ->setStatusCode(201);
    }

    #[OA\Put(
        path: "/api/jadwal-seminar-kp/{id}",
        summary: "Update/Edit Jadwal Seminar KP",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/UpdateJadwalSeminarKPRequest")
        ),
        tags: ["Jadwal Seminar KP"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID jadwal seminar KP",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Jadwal Berhasil Diperbarui",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "data",
                            ref: "#/components/schemas/JadwalSeminarKP"
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Jadwal Tidak Ditemukan"
            ),
            new OA\Response(
                response: 422,
                description: "Jadwal Gagal Diperbarui"
            )
        ]
    )]
    public function update(UpdateJadwalSeminarKPRequest $request, JadwalSeminarKp $jadwalSeminarKp)
    {
        DB::transaction(function () use ($request, $jadwalSeminarKp) {
            $jadwalSeminarKp->update($request->only([
                'title',
                'student_id',
                'scheduled_at',
                'notes',
                'status'
            ]));
        });

        $jadwalSeminarKp->load('student');

        return new JadwalSeminarKPResource($jadwalSeminarKp);
    }

    #[OA\Delete(
        path: "/api/jadwal-seminar-kp/{id}",
        summary: "Menghapus Jadwal Seminar KP",
        tags: ["Jadwal Seminar KP"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID jadwal seminar KP",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Jadwal Berhasil Dihapus",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "message",
                            type: "string",
                            example: "Jadwal seminar KP berhasil dihapus"
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Jadwal Tidak Ditemukan"
            )
        ]
    )]
    public function destroy(JadwalSeminarKp $jadwalSeminarKp)
    {
        $jadwalSeminarKp->delete();

        return response()->json([
            'message' => 'Jadwal seminar KP berhasil dihapus'
        ]);
    }

    public function showForMahasiswa()
{
    return view('mahasiswa.jadwal-seminar');
}

   public function mySchedule(Request $request)
{
    $jadwal = JadwalSeminarKp::with('student')
        ->where('student_id', $request->user()->id)
        ->first();
    
    if (!$jadwal) {
        return response()->json(['data' => null]);
    }
    
    return response()->json(['data' => [
        'id' => $jadwal->id,
        'title' => $jadwal->title,
        'student' => [
            'id' => $jadwal->student->id,
            'name' => $jadwal->student->name,
            'email' => $jadwal->student->email,
        ],
        'scheduled_at' => $jadwal->scheduled_at,
        'status' => $jadwal->status,
        'notes' => $jadwal->notes,
    ]]);
}
}