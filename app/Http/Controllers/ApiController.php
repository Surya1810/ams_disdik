<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Models\Sekolah;
use App\Models\Asset;
use App\Models\Approval;
use App\Models\Scan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

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


    public function login(Request $request)
    {
        // dd($request);
        $request->validate([
            'name' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('name', $request->name)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'accessToken' => explode('|', $token)[1],
            'tokenType' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'roleId' => $user->role_id,
                'kecamatanId' => $user->kecamatan_id,
                'name' => $user->name,
                'lastLogin' => $user->last_login,
                'createdAt' => $user->created_at,
                'updatedAt' => $user->updated_at
            ]
        ]);
    }


    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out']);
    }

    private function filterAssetByRole($query)
    {
        $user = Auth::user();

        if ($user->role_id == 1) {
            return $query;
        }

        return $query->whereHas('sekolah', function ($q) use ($user) {
            $q->where('kecamatan_id', $user->kecamatan_id);
        });
    }

    private function filterSekolahByRole($query)
    {
        $user = Auth::user();

        if ($user->role_id == 1) {
            return $query;
        }

        return $query->where('kecamatan_id', $user->kecamatan_id);
    }


    public function getsekolah()
    {
        $sekolahs = $this->filterSekolahByRole(Sekolah::query())->get();

        $result = $sekolahs->map(function ($sekolah) {
            return [
                'id' => $sekolah->id,
                'SchoolName' => $sekolah->name,
                'lastStockOpname' => optional($sekolah->last_stock_opname)->format('d/m/Y') ?? '-',
                'totalAset' => $sekolah->assets()->count(),
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'listSchool' => $result,
            ]
        ]);
    }

    public function getAssetSekolah($idSchool)
    {
        $query = Asset::with('sekolah')->where('sekolah_id', $idSchool);
        $assets = $this->filterAssetByRole($query)->get();

        $result = $assets->map(function ($asset) {
            return [
                'id' => $asset->id,
                'itemName' => $asset->name,
                'school' => $asset->sekolah->name ?? '-',
                'rfid' => $asset->rfid_number,
                'room' => $asset->ruangan ?? '-',
                'isThere' => false,
                'purcaseYear' => $asset->tahun_pembelian,
                'condition' => $asset->kondisi,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'listAssets' => $result,
            ]
        ]);
    }

    public function PostStockOpname(Request $request, $idSchool)
    {
        $validated = $request->validate([
            'stockOpname' => 'required|array',
            'stockOpname.*.id' => 'required|integer|exists:assets,id',
            'stockOpname.*.isThere' => 'required|boolean',
        ]);
        $query = Asset::with('sekolah')->where('sekolah_id', $idSchool);
        $assets = $this->filterAssetByRole($query)->get();

        foreach ($validated['stockOpname'] as $item) {
            $asset = Asset::where('id', $item['id'])
                ->where('sekolah_id', $idSchool)
                ->first();

            if ($asset) {
                $asset->is_there = $item['isThere'];
                // $asset->kondisi = $item['condition'];
                $asset->save();
            }
        }

        Scan::create([
            'total' => count($request->stockOpname),
            'user_id' => Auth::user()->id
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Stock opname berhasil diperbarui.'
        ]);
    }

    public function getSearchFilter(Request $request)
    {
        $sekolahs = $this->filterSekolahByRole(Sekolah::query())->get();

        $query = Asset::with('sekolah');
        $assets = $this->filterAssetByRole($query)->get();

        // << Ini diperbaiki: filter juga sekolah sesuai role
        $schools = $this->filterSekolahByRole(Sekolah::select('id', 'name as schoolName'))->get();

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
        $query = Asset::with('sekolah');
        $query = $this->filterAssetByRole($query);

        if ($request->filled('school')) {
            $sekolah = Sekolah::where('name', $request->school)->first();
            $query->where('sekolah_id', $sekolah->id);
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
                        'school' => $item->sekolah->name ?? '-',
                        'rfid' => $item->rfid_number,
                        'room' => $item->ruangan,
                        'isThere' => (bool) $item->is_there,
                        'purcaseYear' => $item->tahun_pembelian,
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
        $query = Asset::with('sekolah')->where('id', $id);
        $asset = $this->filterAssetByRole($query)->firstOrFail();

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
                    'imageUrl' => (!$asset->foto_awal || $asset->foto_awal === 'dummy.jpg')
                        ? asset('assets/Image/no_image.png')
                        : asset(Storage::url('/public/assets/' . $asset->foto_awal)),
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
                ],
                'school' => [
                    'nameSchool' => $asset->sekolah->name ?? null,
                    'idSchool' => $asset->sekolah->id ?? null,
                ]
            ]
        ]);
    }

    public function mutationPerson(Request $request, $idItem)
    {
        $asset = Asset::findOrFail($idItem);

        $personInCharge = $request->personInCharge;
        if (
            empty($personInCharge['nip']) ||
            empty($personInCharge['name']) ||
            empty($personInCharge['position']) ||
            empty($personInCharge['phoneNumber'])
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Data personInCharge tidak lengkap'
            ], 400);
        }

        $payload = [
            'from' => [
                'nip_pic' => $asset->nip_pic,
                'nama_pic' => $asset->nama_pic,
                'jabatan_pic' => $asset->jabatan_pic,
                'telp_pic' => $asset->telp_pic,
            ],
            'to' => [
                'nip_pic' => $personInCharge['nip'],
                'nama_pic' => $personInCharge['name'],
                'jabatan_pic' => $personInCharge['position'],
                'telp_pic' => $personInCharge['phoneNumber'],
            ],
            'keterangan' => $request->input('reason', null),
        ];

        $approval = Approval::create([
            'type' => 'mutation',
            'asset_id' => $asset->id,
            'status' => 'pending',
            'payload' => json_encode($payload),
            'requested_by' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mutasi aset berhasil diajukan untuk approval.',
            'approval_id' => $approval->id
        ], 200);
    }


    public function mutationLocation(Request $request, $idItem)
    {
        $asset = Asset::findOrFail($idItem);

        $location = $request->location;
        if (empty($location['building']) || empty($location['floor']) || empty($location['room']) || empty($location['information'])) {
            return response()->json([
                'success' => false,
                'message' => 'Data location tidak lengkap'
            ], 400);
        }

        $payload = [
            'old_values' => [
                'building' => $asset->gedung,
                'floor' => $asset->lantai,
                'room' => $asset->ruangan,
                'detail' => $asset->detail,
            ],
            'new_values' => [
                'building' => $location['building'],
                'floor' => $location['floor'],
                'room' => $location['room'],
                'detail' => $location['information'],
            ],
            'keterangan' => $request->input('reason', null),
            'sekolah_id' => $request->input('schoolId')
        ];

        $approval = Approval::create([
            'type' => 'loan',
            'asset_id' => $asset->id,
            'status' => 'pending',
            'payload' => json_encode($payload),
            'requested_by' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Perubahan lokasi berhasil diajukan untuk approval.',
            'approval_id' => $approval->id
        ], 200);
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
