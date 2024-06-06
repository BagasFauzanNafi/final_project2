<?php

namespace App\Http\Controllers;

use App\Models\Fipro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FiproController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        {
            $fipro =Fipro::all();
            return view('fipro.index' ,compact('fipro'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('fipro.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {  
        $this->validate($request,[
               'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
               'title' => 'required|string|max:255',
               'description' => 'required|string|max:500',
               'content' => 'required|string',
           ]);
        
          // Upload gambar
       if ($request->hasFile('image')) {
           $image = $request->file('image');
           $imageName = $image->hashName();
           $image->storeAs('public/fipro', $imageName);
       } else {
           $imageName = null; // Atau Anda bisa memberikan nilai default
       }
   
   
       // Simpan data ke database
       Fipro::create([
           'image' => 'fipro/' . $imageName,  // Menyimpan path relatif
           'title' => $request->title,
           'description' => $request->description,
           'content' => $request->content,
       ]);
           //         //redirect to index
           return redirect()->route('fipro.index')->with(['success' => 'Data Berhasil Disimpan!']);
       }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $fipro = Fipro::findorFail($id);
        return view('fipro.show',compact('fipro'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $fipro = Fipro::findorFail($id);
        return view('fipro.edit',compact('fipro'));
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validasi data
        $this->validate($request, [
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:500',
            'content' => 'required|string',
        ]);
   
        // Temukan post berdasarkan ID
        $fipro = Fipro::findOrFail($id);
   
        // Periksa jika ada file gambar yang diunggah
        if ($request->hasFile('image')) {
            // Upload gambar baru
            $image = $request->file('image');
            $imageName = $image->hashName();
            $image->storeAs('public/fipro', $imageName);
   
            // Hapus gambar lama
            Storage::delete('public/fipro/' . $fipro->image);
   
            // Perbarui data post dengan gambar baru
            $fipro->update([
                'image' => 'fipro/' . $imageName,
                'title' => $request->title,
                'description' => $request->description,
                'content' => $request->content
            ]);
        } else {
            // Perbarui data post tanpa mengubah gambar
            $fipro->update([
                'title' => $request->title,
                'description' => $request->description,
                'content' => $request->content
            ]);
        }
   
        // Redirect ke halaman index
        return redirect()->route('fipro.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //get post by ID
        $fipro = Fipro::findOrFail($id);


        //delete image
        Storage::delete('public/fipro/'. $fipro->image);


        //delete post
        $fipro->delete();


        //redirect to index
        return redirect()->route('fipro.index');
    }
}


