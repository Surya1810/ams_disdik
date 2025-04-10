<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Sekolah;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Yajra\DataTables\Facades\DataTables;

class AssetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // $user = Auth::user();

        // if (!Auth::check() || Auth::user()->role_id == 1) {
        //     if ($request->ajax()) {
        //         $assets = Asset::whereHas('sekolah', function ($query) use ($user) {
        //             $query->where('kecamatan_id', $user->kecamatan_id);
        //         })->with(['asset.kecamatan'])
        //             ->get();

        //         return DataTables::of($assets)
        //             ->filter(function ($query) use ($request) {
        //                 if (!empty($request->search['value'])) {
        //                     $search = $request->search['value'];
        //                     $query->where('name', 'like', "%{$search}%");
        //                 }
        //             })
        //             ->addColumn('action', function ($row) {
        //                 return '
        //                     <a role="button" class="text-danger px-3 mb-0 border-radius-lg"
        //                         onclick="deleteUser(' . $row->id . ')"><i class="fa-solid fa-trash"></i></a>
        //                     <form id="delete-form-' . $row->id . '" 
        //                         action="' . route('user.destroy', $row->id) . '" 
        //                         method="POST" style="display: none;">
        //                         ' . csrf_field() . method_field('DELETE') . '
        //                     </form>
        //                 ';
        //             })
        //             ->rawColumns(['action']) // Izinkan HTML dalam kolom action
        //             ->make(true);
        //     }
        //     return view('asset.index');
        // } elseif (!Auth::check() || Auth::user()->role_id == 2) {
        //     return view('asset.index');
        // } elseif (!Auth::check() || Auth::user()->role_id == 3) {
        //     return view('asset.index');
        // } else {
        //     abort(403, 'Unauthorized');
        // }
        $tags = Tag::where('status', 'available')->where('kecamatan_id', Auth::user()->kecamatan_id)->pluck('rfid_number');
        $places = Sekolah::where('kecamatan_id', Auth::user()->kecamatan_id)->get();

        if ($request->ajax()) {
            $assets = Asset::with('sekolah'); // eager loading

            return DataTables::of($assets)
                ->addColumn('kondisi_badge', function ($row) {
                    $badge = match ($row->kondisi) {
                        'Baik' => '<span class="badge bg-success">Baik</span>',
                        'Perlu Perbaikan', 'Rusak Ringan', 'Rusak Sedang' => '<span class="badge bg-warning">' . $row->kondisi . '</span>',
                        'Rusak Berat', 'Hilang' => '<span class="badge bg-danger">' . $row->kondisi . '</span>',
                        default => '<span class="badge bg-secondary">' . $row->kondisi . '</span>',
                    };
                    return $badge;
                })
                ->addColumn('action', function ($row) {
                    return '
                    <a href="javascript:void(0)" class="btn btn-link py-0 px-2" data-bs-toggle="modal" data-bs-target="#showAssetModal" onclick="showAsset({{ $row->id }})">
                        <i class="fa-solid fa-eye" data-bs-toggle="tooltip" data-bs-placement="top" title="Lihat Detail"></i>
                    </a>
                    &nbsp;
                    <a href="javascript:void(0)" class="btn btn-link p-0" data-bs-toggle="modal" data-bs-target="#editAssetModal" onclick="editAsset(' . $row->id . ')">
                        <i class="fa-solid fa-pencil" data-bs-toggle="tooltip" data-bs-placement="top" title="Ubah"></i>
                    </a>
                ';
                })
                ->rawColumns(['kondisi_badge', 'action'])
                ->make(true);
        }

        return view('asset.index', compact('tags', 'places'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'image|max:2048',
            'tag' => 'required|exists:tags,rfid_number',
            'sekolah_id' => 'required',

            'kode' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'register' => 'required|string|max:255',
            'merk' => 'required|string|max:255',
            'ukuran' => 'string|max:255',
            'bahan' => 'required|string|max:255',
            'tahun_pembelian' => 'required|integer',
            'pabrik' => 'string|max:255',

            'rangka' => 'string|max:255',
            'mesin' => 'string|max:255',
            'polisi' => 'string|max:255',
            'bpkb' => 'string|max:255',

            'nip_pic' => 'required|string|max:255',
            'nama_pic' => 'required|string|max:255',
            'jabatan_pic' => 'required|string|max:255',
            'telp_pic' => 'required|min:10',

            'asal_perolehan' => 'required|string|max:255',
            'nilai_perolehan' => 'required|numeric|min:0',
            'kondisi' => 'required',
            'tanggal_perawatan' => 'required|date',
            'harga_perawatan' => 'required|numeric|min:0',
            'waktu_perawatan' => 'required|numeric|min:0',

            'gedung' => 'required',
            'lantai' => 'required',
            'ruangan' => 'required',
            'detail' => 'required',

            // validasi gambar dan fungsi gambar belum
        ]);

        $old = session()->getOldInput();

        $file = $request->file('image');
        $filename = Str::uuid() . '.webp';
        $path = 'assets/' . $filename;

        // Buat instance ImageManager versi 3
        $manager = new ImageManager(new Driver());

        // Baca gambar dari file, resize, dan encode ke webp
        $image = $manager->read($file->getPathname())
            ->scale(width: 800) // otomatis menjaga aspect ratio
            ->toWebp(quality: 75); // encode ke WebP dengan kompresi

        // Simpan ke storage
        Storage::disk('public')->put($path, (string) $image);

        Asset::create([
            'rfid_number' => $request->input('tag'),
            'sekolah_id' => $request->input('sekolah_id'),
            'kode' => $request->input('kode'),
            'name' => $request->input('name'),
            'register' => $request->input('register'),
            'merk' => $request->input('merk'),
            'ukuran' => $request->input('ukuran'),
            'bahan' => $request->input('bahan'),
            'tahun_pembelian' => 2025,
            'pabrik' => $request->input('pabrik'),
            'rangka' => $request->input('rangka'),
            'mesin' => $request->input('mesin'),
            'polisi' => $request->input('polisi'),
            'bpkb' => $request->input('bpkb'),
            'nip_pic' => $request->input('nip_pic'),
            'nama_pic' => $request->input('nama_pic'),
            'jabatan_pic' => $request->input('jabatan_pic'),
            'telp_pic' => $request->input('telp_pic'),
            'asal_perolehan' => $request->input('asal_perolehan'),
            'nilai_perolehan' => $request->input('nilai_perolehan'),
            'kondisi' => $request->input('kondisi'),
            'tanggal_perawatan' => $request->input('tanggal_perawatan'),
            'harga_perawatan' => $request->input('harga_perawatan'),
            'waktu_perawatan' => $request->input('waktu_perawatan'),
            'gedung' => $request->input('gedung'),
            'lantai' => $request->input('lantai'),
            'ruangan' => $request->input('ruangan'),
            'detail' => $request->input('detail'),
            'foto_awal' => $filename
        ]);

        // Update Tag Status
        $tag = Tag::where('rfid_number', $request->input('tag'))->first();
        $tag->status = 'used';
        $tag->save();

        return redirect()->route('asset.index')->with(['pesan' => 'Aset berhasil ditambahkan', 'level-alert' => 'alert-success']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Asset $asset)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Asset $asset)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Asset $asset)
    {
        return redirect()->route('asset.index')->with(['pesan' => 'Aset berhasil diperbarui', 'level-alert' => 'alert-warning']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Asset $asset)
    {
        // 
    }
}
