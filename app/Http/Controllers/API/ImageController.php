<?php

namespace App\Http\Controllers\API;

use App\Models\Image;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $this->validate($request, [
            'path' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'imageable_type' => 'required|string',
            'imageable_id' => 'required|integer'
        ]);
        $file = $request->file('path');
        // dd($file);
        $filename = Str::random(40) . '.' . $file->extension();
        $root_path = Storage::disk('public')->putFileAs('images', $file, $filename);
        if (DB::table('images')
                    ->where('imageable_type', $request->input('imageable_type'))
                    ->where('imageable_id', $request->input('imageable_id'))
                    ->exists()) {
            return response()->json(['message' => 'This type has already taken image']);
        }
            $image = new Image();
            $image->path = Storage::url($root_path);
            $image->imageable_type = $request->input('imageable_type');
            $image->imageable_id = $request->input('imageable_id');
            $image->save();
    
        return response()->json(['message' => 'Image uploaded successfully'], 200);
        
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Image $image)
    { 
        Storage::disk('public')->delete('images/' . $image->path);
        $image->delete();

        return response()->json(['message' => 'Image deleted successfully']);
    }
}
