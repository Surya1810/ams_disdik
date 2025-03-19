<?php

namespace App\Http\Controllers;

use App\Exports\TagsExport;
use App\Models\Tag;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rfids = Tag::all();

        return view('tag.index', compact('rfids'));
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
            'from' => 'required|string|unique:tags,rfid_number',
            'until' => 'required|string|unique:tags,rfid_number',
        ]);

        if (!ctype_digit($request->from) || !ctype_digit($request->until)) {
            return redirect()->route('tag.index')->with(['pesan' => 'Must be number', 'level-alert' => 'alert-danger']);
        }

        $tags = [];
        for ($i = (int) $request->from; $i <= (int) $request->until; $i++) {
            $formattedNumber = str_pad($i, strlen($request->until), '0', STR_PAD_LEFT);
            $tags[] = [
                'rfid_number' => $formattedNumber,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        Tag::insert($tags); // Mass insert untuk efisiensi

        return redirect()->route('tag.index')->with(['pesan' => 'Tag created successfully', 'level-alert' => 'alert-success']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Tag $tag)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tag $tag)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tag $tag)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($rfid_number)
    {
        $tag = Tag::where('rfid_number', $rfid_number)->first();

        if ($tag->document == null) {
            $tag->delete();
            return redirect()->back()->with(['pesan' => 'Tag deleted successfully', 'level-alert' => 'alert-danger']);
        } else {
            return redirect()->back()->with(['pesan' => 'Tag used on document', 'level-alert' => 'alert-danger']);
        }
    }

    public function export()
    {
        $date = date('Y-m-d');
        $fileName = "exportTag-$date.xlsx";

        return Excel::download(new TagsExport, $fileName);
    }
}
