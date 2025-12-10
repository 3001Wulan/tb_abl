<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\JadwalBimbingan;
use App\Models\Forum;
use App\Models\KomentarForum;
use App\Models\CatatanBimbingan;

class KonsultasiController extends Controller {

    // ==== Jadwal Bimbingan ====
    public function indexJadwal() {
        return response()->json(JadwalBimbingan::all());
    }

    public function storeJadwal(Request $request) {
        $request->validate([
            'student_id'=>'required',
            'lecturer_id'=>'required',
            'tanggal'=>'required',
            'waktu'=>'required'
        ]);
        $jadwal = JadwalBimbingan::create($request->all());
        return response()->json($jadwal,201);
    }

    // ==== Forum Konsultasi ====
    public function indexForum() {
        return response()->json(Forum::with('komentar')->get());
    }

    public function storeForum(Request $request) {
        $request->validate(['student_id'=>'required','judul'=>'required','konten'=>'required']);
        $forum = Forum::create($request->all());
        return response()->json($forum,201);
    }

    public function storeKomentar(Request $request,$forumId){
        $request->validate(['student_id'=>'required','konten'=>'required']);
        $komentar = KomentarForum::create([
            'forum_id'=>$forumId,
            'student_id'=>$request->student_id,
            'konten'=>$request->konten
        ]);
        return response()->json($komentar,201);
    }

    // ==== Catatan Bimbingan ====
    public function indexCatatan($studentId) {
        return response()->json(CatatanBimbingan::where('student_id',$studentId)->get());
    }

    public function storeCatatan(Request $request) {
        $request->validate(['student_id'=>'required','lecturer_id'=>'required','tanggal'=>'required','isi_catatan'=>'required']);
        $catatan = CatatanBimbingan::create($request->all());
        return response()->json($catatan,201);
    }
}
