<?php

namespace App\Http\Controllers;

use App\Models\PenilaianKP;
use Illuminate\Http\Request;

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

    private function hitungNilaiAkhir($laporan, $presentasi, $aktivitas, $bobot)
    {
        return 
            ($laporan * $bobot['laporan']) +
            ($presentasi * $bobot['presentasi']) +
            ($aktivitas * $bobot['aktivitas']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'mahasiswa_id'        => 'required',
            'nilai_laporan'       => 'required|integer|min:0|max:100',
            'nilai_presentasi'    => 'required|integer|min:0|max:100',
            'nilai_aktivitas_kp'  => 'required|integer|min:0|max:100',
            'bobot'               => 'nullable|string|in:A,B'
        ]);

        $bobot = $request->bobot === 'B'
            ? ['laporan' => 0.4, 'presentasi' => 0.3, 'aktivitas' => 0.3]
            : ['laporan' => 0.5, 'presentasi' => 0.2, 'aktivitas' => 0.3];

        $nilaiAkhir = $this->hitungNilaiAkhir(
            $request->nilai_laporan,
            $request->nilai_presentasi,
            $request->nilai_aktivitas_kp,
            $bobot
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

public function index(Request $request)
{
    $data = PenilaianKP::all()->map(function ($item) {
        return [
            'id'            => $item->id,
            'mahasiswa_id'  => $item->mahasiswa_id,
            'nilai_akhir'   => $item->nilai_akhir,
            'nilai_mutu'    => $item->nilai_mutu,
            'created_at'    => $item->created_at,
            'updated_at'    => $item->updated_at,
        ];
    });

    if ($request->has('mutu')) {
        $mutu = strtoupper($request->mutu); 
        $data = $data->filter(function ($item) use ($mutu) {
            return strtoupper($item['nilai_mutu']) === $mutu;
        })->values();
    }

    return response()->json($data);
}

    public function show($id)
    {
         $data = PenilaianKP::find($id);

        if (!$data) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json(PenilaianKP::find($id));
    }

    public function update(Request $request, $id)
    {
        $data = PenilaianKP::find($id);

        if (!$data) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $request->validate([
            'nilai_laporan'       => 'required|integer|min:0|max:100',
            'nilai_presentasi'    => 'required|integer|min:0|max:100',
            'nilai_aktivitas_kp'  => 'required|integer|min:0|max:100',
            'bobot'               => 'nullable|string|in:A,B'
        ]);

        $bobot = $request->bobot === 'B'
            ? ['laporan' => 0.4, 'presentasi' => 0.3, 'aktivitas' => 0.3]
            : ['laporan' => 0.5, 'presentasi' => 0.2, 'aktivitas' => 0.3];

        $nilaiAkhir = $this->hitungNilaiAkhir(
            $request->nilai_laporan,
            $request->nilai_presentasi,
            $request->nilai_aktivitas_kp,
            $bobot
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

    public function destroy($id)
    {
        $data = PenilaianKP::find($id);

        if (!$data) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $data->delete();
        return response()->json(['message' => 'Berhasil dihapus']);
    }
}
