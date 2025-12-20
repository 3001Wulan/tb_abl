<?php

namespace App\Http\Controllers;

use App\Models\PenilaianKP;
use App\Http\Requests\StorePenilaianKPRequest;
use App\Http\Requests\UpdatePenilaianKPRequest;
use App\Http\Resources\PenilaianKPResource;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "Penilaian KP",
    description: "Operasi CRUD untuk Mengelola Penilaian Kerja Praktek Mahasiswa"
)]
class PenilaianKPController extends Controller
{
    private function convertToNilaiMutu($nilaiAkhir)
    {
        if ($nilaiAkhir >= 85) return 'A';
        if ($nilaiAkhir >= 80) return 'A-';
        if ($nilaiAkhir >= 75) return 'B+';
        if ($nilaiAkhir >= 70) return 'B';
        if ($nilaiAkhir >= 65) return 'B-';
        if ($nilaiAkhir >= 60) return 'C+';
        if ($nilaiAkhir >= 55) return 'C';
        if ($nilaiAkhir >= 40) return 'D';
        return 'E';
    }

   private function hitungNilaiAkhir($laporan, $presentasi, $aktivitas, $opsiPerhitungan)
{
    return 
        ($laporan * $opsiPerhitungan['laporan']) +
        ($presentasi * $opsiPerhitungan['presentasi']) +
        ($aktivitas * $opsiPerhitungan['aktivitas']);
}

    #[OA\Get(
        path: "/api/penilaian-kp",
        summary: "Menampilkan Semua Penilaian KP yang sudah diinputkan",
        tags: ["Penilaian KP"],
        parameters: [
            new OA\Parameter(
                name: "mutu",
                in: "query",
                description: "Filter berdasarkan nilai mutu (A, A-, B+, B, B-, C+, C, D, E)",
                required: false,
                schema: new OA\Schema(
                    type: "string",
                    enum: ["A", "A-", "B+", "B", "B-", "C+", "C", "D", "E"]
                )
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Data Penilaian KP Berhasil ditampilkan",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(ref: "#/components/schemas/PenilaianKP")
                )
            )
        ]
    )]
   public function index(Request $request)
{
    $query = PenilaianKP::query();
    
    // Filter by mahasiswa_id (untuk mahasiswa lihat nilai sendiri)
    if ($request->has('mahasiswa_id')) {
        $query->where('mahasiswa_id', $request->mahasiswa_id);
    }
    
    // Filter by mutu
    if ($request->has('mutu')) {
        $mutu = strtoupper($request->mutu);
        $query->where('nilai_mutu', $mutu);
    }
    
    $data = $query->get()->map(function ($item) {
        return [
            'id'                 => $item->id,
            'mahasiswa_id'       => $item->mahasiswa_id,
            'nilai_laporan'      => $item->nilai_laporan,      
            'nilai_presentasi'   => $item->nilai_presentasi,   
            'nilai_aktivitas_kp' => $item->nilai_aktivitas_kp, 
            'nilai_akhir'        => $item->nilai_akhir,
            'nilai_mutu'         => $item->nilai_mutu,
            'created_at'         => $item->created_at,
            'updated_at'         => $item->updated_at,
        ];
    });

    return response()->json($data);
}

    #[OA\Get(
        path: "/api/penilaian-kp/{id}",
        summary: "Menampilkan Detail Penilaian KP salah satu mahasiswa",
        tags: ["Penilaian KP"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID penilaian KP",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Data Penilaian Berhasil ditampilkan",
                content: new OA\JsonContent(ref: "#/components/schemas/PenilaianKP")
            ),
            new OA\Response(
                response: 404,
                description: "Data tidak ditemukan",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "message",
                            type: "string",
                            example: "Data tidak ditemukan"
                        )
                    ]
                )
            )
        ]
    )]
    public function show($id)
    {
        $data = PenilaianKP::find($id);

        if (!$data) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json(PenilaianKP::find($id));
    }

    #[OA\Post(
        path: "/api/penilaian-kp",
        summary: "Membuat Penilaian KP yang baru",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/StorePenilaianKPRequest")
        ),
        tags: ["Penilaian KP"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Penilaian Berhasil dibuat",
                content: new OA\JsonContent(ref: "#/components/schemas/PenilaianKP")
            ),
            new OA\Response(
                response: 422,
                description: "Penilaian Gagal dibuat"
            )
        ]
    )]
    public function store(StorePenilaianKPRequest $request)
    {
       $opsiPerhitungan = $request->opsi_perhitungan === '2'
    ? ['laporan' => 0.4, 'presentasi' => 0.3, 'aktivitas' => 0.3]
    : ['laporan' => 0.5, 'presentasi' => 0.2, 'aktivitas' => 0.3];

        $nilaiAkhir = $this->hitungNilaiAkhir(
            $request->nilai_laporan,
            $request->nilai_presentasi,
            $request->nilai_aktivitas_kp,
            $opsiPerhitungan
        );

        $nilaiMutu = $this->convertToNilaiMutu($nilaiAkhir);

        $data = PenilaianKP::create([
            'mahasiswa_id'       => $request->mahasiswa_id,
            'nilai_laporan'      => $request->nilai_laporan,
            'nilai_presentasi'   => $request->nilai_presentasi,
            'nilai_aktivitas_kp' => $request->nilai_aktivitas_kp,
            'nilai_akhir'        => round($nilaiAkhir),
            'nilai_mutu'         => $nilaiMutu
        ]);

        return response()->json($data);
    }

    #[OA\Put(
        path: "/api/penilaian-kp/{id}",
        summary: "Update/Edit Penilaian KP",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/UpdatePenilaianKPRequest")
        ),
        tags: ["Penilaian KP"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID penilaian KP",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Penilaian Berhasil diperbarui",
                content: new OA\JsonContent(ref: "#/components/schemas/PenilaianKP")
            ),
            new OA\Response(
                response: 404,
                description: "Data tidak ditemukan"
            ),
            new OA\Response(
                response: 422,
                description: "Data tidak ditemukan"
            )
        ]
    )]
    public function update(UpdatePenilaianKPRequest $request, $id)
    {
        $data = PenilaianKP::find($id);

        if (!$data) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $opsiPerhitungan = $request->opsi_perhitungan === '2'
            ? ['laporan' => 0.4, 'presentasi' => 0.3, 'aktivitas' => 0.3]
            : ['laporan' => 0.5, 'presentasi' => 0.2, 'aktivitas' => 0.3];

        $nilaiAkhir = $this->hitungNilaiAkhir(
            $request->nilai_laporan,
            $request->nilai_presentasi,
            $request->nilai_aktivitas_kp,
            $opsiPerhitungan
        );

        $nilaiMutu = $this->convertToNilaiMutu($nilaiAkhir);

        $data->update([
            'nilai_laporan'      => $request->nilai_laporan,
            'nilai_presentasi'   => $request->nilai_presentasi,
            'nilai_aktivitas_kp' => $request->nilai_aktivitas_kp,
            'nilai_akhir'        => round($nilaiAkhir),
            'nilai_mutu'         => $nilaiMutu
        ]);

        return response()->json($data);
    }

    #[OA\Delete(
        path: "/api/penilaian-kp/{id}",
        summary: "Menghapus Penilaian KP",
        tags: ["Penilaian KP"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID penilaian KP",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Penilaian Berhasil dihapus",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "message",
                            type: "string",
                            example: "Penilaian Berhasil dihapus"
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Data tidak ditemukan"
            )
        ]
    )]
    public function destroy($id)
    {
        $data = PenilaianKP::find($id);

        if (!$data) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $data->delete();
        return response()->json(['message' => 'Penilaian Berhasil dihapus']);
    }

    public function showForMahasiswa()
{
    return view('mahasiswa.nilai-kp');
}
}