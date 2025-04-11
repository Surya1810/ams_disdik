<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Models\Sekolah;
use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ApiController extends Controller
{
    /**
     * Success response
     */
    protected function success(array $data = [], int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $data
        ], $statusCode);
    }

    /**
     * Error response
     */
    protected function error(string $message = 'Terjadi kesalahan', int $statusCode = 400): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message
        ], $statusCode);
    }

    public function getsekolah()
    {
        $sekolahs = Sekolah::all(); // atau paginate kalau besar

        $result = $sekolahs->map(function ($sekolah) {
            return [
                'id' => $sekolah->id,
                'SchoolName' => $sekolah->name,
                'lastStockOpname' => optional($sekolah->last_stock_opname)->format('d/m/Y') ?? '-',
                'totalAset' => $sekolah->assets()->count() // pastikan ada relasi assets
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'listSchool' => $result
            ]
        ]);
    }

    public function getAssetSekolah($idSchool)
    {
        $assets = Asset::where('sekolah_id', $idSchool)->get();

        $result = $assets->map(function ($asset) {
            return [
                'id' => $asset->id,
                'itemName' => $asset->kode,
                'rfidNumber' => $asset->rfid_number,
                'room' => $asset->ruangan ?? '-',
                'isThere' => (bool) $asset->is_there,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'listAssets' => $result
            ]
        ]);
    }

    public function postStockOpname(Request $request, $idSchool)
    {
        $validated = $request->validate([
            'stockOpname' => 'required|array',
            'stockOpname.*.id' => 'required|integer|exists:assets,id',
            'stockOpname.*.isThere' => 'required|boolean',
            'stockOpname.*.condition' => 'required|string',
        ]);

        foreach ($validated['stockOpname'] as $item) {
            $asset = Asset::where('id', $item['id'])
                ->where('sekolah_id', $idSchool)
                ->first();

            if ($asset) {
                $asset->is_there = $item['isThere'];
                $asset->kondisi = $item['condition'];
                $asset->save();
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Stock opname berhasil diperbarui.'
        ]);
    }

    public function getSearchFilter()
    {
        // Ambil semua sekolah
        $schools = Sekolah::select('id', 'name as schoolName')->get();

        // Ambil semua tahun pembelian unik dari tabel assets
        $years = Asset::select('tahun_pembelian')
            ->distinct()
            ->orderBy('tahun_pembelian', 'asc')
            ->pluck('tahun_pembelian');

        return response()->json([
            'status' => 'success',
            'data' => [
                'school' => $schools,
                'purcaseYear' => $years
            ]
        ]);
    }


    public function getSearch(Request $request)
    {
        $query = Asset::query();

        // Filter opsional
        if ($request->filled('school')) {
            $query->where('sekolah_id', $request->school);
        }

        if ($request->filled('isThere')) {
            $query->where('is_there', $request->isThere);
        }

        if ($request->filled('condition')) {
            $query->where('kondisi', $request->condition);
        }

        if ($request->filled('purcaseYear')) {
            $query->where('tahun_pembelian', $request->purcaseYear);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('kode', 'like', "%$search%")
                    ->orWhere('rfid_number', 'like', "%$search%");
            });
        }

        // Pagination
        $limit = $request->get('limit', 10);
        $page = $request->get('page', 1);

        $totalData = $query->count();
        $data = $query->offset(($page - 1) * $limit)->limit($limit)->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'listAssets' => $data->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'itemName' => $item->name,
                        'rfid' => $item->rfid_number,
                        'room' => $item->ruangan,
                        'isThere' => (bool) $item->is_there,
                        'condition' => $item->kondisi,
                    ];
                }),
            ],
            'paging' => [
                'currentPage' => (int) $page,
                'limit' => (int) $limit,
                'totalData' => $totalData,
                'totalPage' => ceil($totalData / $limit),
            ]
        ]);
    }


    public function getItemDetail($id)
    {
        $asset = Asset::with('sekolah')->find($id);

        if (!$asset) {
            return response()->json([
                'status' => 'error',
                'message' => 'Item tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'informationItem' => [
                    'ItemName' => $asset->name,
                    'rfidNumber' => $asset->rfid_number,
                    'itemCode' => $asset->kode,
                    'purcaseYear' => $asset->tahun_pembelian,
                    'lastMaintenance' => optional($asset->tanggal_perawatan)->format('d/m/Y'),
                    'merk' => $asset->merk,
                    'condition' => $asset->kondisi,
                ],
                'personInCharge' => [
                    'nip' => $asset->nip_pic,
                    'name' => $asset->nama_pic,
                    'position' => $asset->jabatan_pic,
                    'numberTelp' => $asset->telp_pic,
                ],
                'location' => [
                    'building' => $asset->gedung,
                    'floor' => (int) $asset->lantai,
                    'room' => $asset->ruangan,
                    'information' => $asset->detail,
                ]
            ]
        ]);
    }

    public function mutation(Request $request, $idItem)
    {
        $asset = Asset::findOrFail($idItem);

        $asset->nip_pic = $request->personIncharge['nip'];
        $asset->nama_pic = $request->personIncharge['name'];
        $asset->jabatan_pic = $request->personIncharge['position'];
        $asset->telp_pic = (string) $request->personIncharge['numberTelp'];

        $asset->gedung = $request->location['building'];
        $asset->lantai = $request->location['floor'];
        $asset->ruangan = $request->location['room'];
        $asset->detail = $request->location['information'];

        $asset->status = 'Mutated';

        $asset->save();

        return response()->json([
            'message' => 'Asset berhasil dimutasi'
        ]);
    }

    public function inspection(Request $request, $idItem)
    {
        $asset = Asset::findOrFail($idItem);

        $asset->kondisi = $request->condition;

        $asset->save();

        return response()->json([
            'message' => 'Kondisi asset berhasil diperbarui'
        ]);
    }

    public function updateSearch(Request $request, $idItem)
    {
        $asset = Asset::findOrFail($idItem);

        $asset->nip_pic = $request->personIncharge['nip'];
        $asset->nama_pic = $request->personIncharge['name'];
        $asset->jabatan_pic = $request->personIncharge['position'];
        $asset->telp_pic = (string) $request->personIncharge['numberTelp'];

        $asset->gedung = $request->location['building'];
        $asset->lantai = $request->location['floor'];
        $asset->ruangan = $request->location['room'];
        $asset->detail = $request->location['information'];

        $asset->status = 'Found';

        $asset->save();

        return response()->json([
            'message' => 'Data asset hasil pencarian berhasil diperbarui'
        ]);
    }
}
