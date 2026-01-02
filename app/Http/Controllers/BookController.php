<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * @OA\Tag(
 *     name="Buku",
 *     description="Operasi List dan Detail Buku menggunakan Google Books API"
 * )
 *
 * @OA\Schema(
 *     schema="Book",
 *     title="Book",
 *     description="Model data Buku dari Google Books API",
 *     @OA\Property(property="id", type="string", example="zyTCAlFPjgYC"),
 *     @OA\Property(property="title", type="string", example="Programming 101"),
 *     @OA\Property(property="authors", type="array", @OA\Items(type="string"), example={"John Doe"}),
 *     @OA\Property(property="publisher", type="string", example="Tech Books"),
 *     @OA\Property(property="publishedDate", type="string", example="2020"),
 *     @OA\Property(property="description", type="string", example="Buku pengantar programming lengkap..."),
 *     @OA\Property(property="pageCount", type="integer", example=250),
 *     @OA\Property(property="categories", type="array", @OA\Items(type="string"), example={"Computers"}),
 *     @OA\Property(property="averageRating", type="number", format="float", example=4),
 *     @OA\Property(property="thumbnail", type="string", example="http://books.google.com/books/content?id=zyTCAlFPjgYC&printsec=frontcover&img=1&zoom=1"),
 *     @OA\Property(property="previewLink", type="string", example="http://books.google.com/books?id=zyTCAlFPjgYC"),
 *     @OA\Property(property="infoLink", type="string", example="http://books.google.com/books?id=zyTCAlFPjgYC")
 * )
 */
class BookController extends Controller
{
    private $apiKey;
    private $baseUrl;

    public function __construct()
    {
        $this->apiKey = env('GOOGLE_BOOKS_API_KEY');
        $this->baseUrl = env('GOOGLE_BOOKS_BASE_URL');
    }

    /**
     * @OA\Get(
     *     path="/api/books",
     *     tags={"Buku"},
     *     summary="List Buku",
     *     description="Mengambil daftar buku berdasarkan kata kunci pencarian.",
     *     @OA\Parameter(
     *         name="q",
     *         in="query",
     *         description="Kata kunci pencarian buku",
     *         required=false,
     *         @OA\Schema(type="string", example="programming")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Daftar buku berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="List buku berhasil diambil"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Book")
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $q = $request->query('q', 'programming'); 
        $response = Http::get($this->baseUrl, [
            'q' => $q,
            'key' => $this->apiKey,
            'maxResults' => 20
        ]);

        if ($response->successful()) {
            $data = collect($response->json('items'))->map(function($item) {
                return [
                    'id' => $item['id'] ?? null,
                    'title' => $item['volumeInfo']['title'] ?? null,
                    'authors' => $item['volumeInfo']['authors'] ?? [],
                    'publisher' => $item['volumeInfo']['publisher'] ?? null,
                    'publishedDate' => $item['volumeInfo']['publishedDate'] ?? null,
                    'description' => $item['volumeInfo']['description'] ?? null,
                    'pageCount' => $item['volumeInfo']['pageCount'] ?? null,
                    'categories' => $item['volumeInfo']['categories'] ?? [],
                    'averageRating' => $item['volumeInfo']['averageRating'] ?? null,
                    'thumbnail' => $item['volumeInfo']['imageLinks']['thumbnail'] ?? null,
                    'previewLink' => $item['volumeInfo']['previewLink'] ?? null,
                    'infoLink' => $item['volumeInfo']['infoLink'] ?? null,
                ];
            });

            return response()->json([
                'status' => 200,
                'message' => 'List buku berhasil diambil',
                'data' => $data
            ], 200);
        }

        return response()->json([
            'status' => $response->status(),
            'message' => 'Gagal mengambil daftar buku'
        ], $response->status());
    }

    /**
     * @OA\Get(
     *     path="/api/books/{id}",
     *     tags={"Buku"},
     *     summary="Detail Buku",
     *     description="Mengambil detail buku berdasarkan ID Google Books.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID buku dari Google Books",
     *         required=true,
     *         @OA\Schema(type="string", example="zyTCAlFPjgYC")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detail buku berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Detail buku berhasil diambil"),
     *             @OA\Property(property="data", ref="#/components/schemas/Book")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Buku tidak ditemukan",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=404),
     *             @OA\Property(property="message", type="string", example="Buku tidak ditemukan")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $response = Http::get("{$this->baseUrl}/{$id}", [
            'key' => $this->apiKey,
        ]);

        if ($response->successful()) {
            $item = $response->json();
            $data = [
                'id' => $item['id'] ?? null,
                'title' => $item['volumeInfo']['title'] ?? null,
                'authors' => $item['volumeInfo']['authors'] ?? [],
                'publisher' => $item['volumeInfo']['publisher'] ?? null,
                'publishedDate' => $item['volumeInfo']['publishedDate'] ?? null,
                'description' => $item['volumeInfo']['description'] ?? null,
                'pageCount' => $item['volumeInfo']['pageCount'] ?? null,
                'categories' => $item['volumeInfo']['categories'] ?? [],
                'averageRating' => $item['volumeInfo']['averageRating'] ?? null,
                'thumbnail' => $item['volumeInfo']['imageLinks']['thumbnail'] ?? null,
                'previewLink' => $item['volumeInfo']['previewLink'] ?? null,
                'infoLink' => $item['volumeInfo']['infoLink'] ?? null,
            ];

            return response()->json([
                'status' => 200,
                'message' => 'Detail buku berhasil diambil',
                'data' => $data
            ], 200);
        }

        return response()->json([
            'status' => 404,
            'message' => 'Buku tidak ditemukan'
        ], 404);
    }
}
