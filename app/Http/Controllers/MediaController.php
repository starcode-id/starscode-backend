<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MediaController extends Controller

{
    public function index(Request $request)
    {
        $mediaList = Media::all();
        $host = $request->getSchemeAndHttpHost();
        $media_path  = $mediaList->map(function ($media) use ($host) {
            return [
                "id" => $media->id,
                'image' => $host . '/' . $media->image,
            ];
        });
        return response()->json([
            'status' => 'success',
            'data' => $media_path,
        ]);
    }
    public function store(Request $request)
    {
        // Validate the incoming request with the necessary rules
        $rules = [
            'image' => 'required|base64image',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
            ]);
        }
        $host = $request->getSchemeAndHttpHost();
        $imageData = $request->input('image');
        $extension = explode('/', explode(':', substr($imageData, 0, strpos($imageData, ';')))[1])[1];
        $replace = substr($imageData, 0, strpos($imageData, ';') + 1);
        $image = str_replace($replace, ' ', $imageData);
        $image = str_replace(' ', '+', $image);

        $imageName = Str::random(10) . '.' . $extension;
        if (!file_exists(public_path('images'))) {
            mkdir(public_path('images'), 0777, true);
        }
        file_put_contents(public_path('images/' . $imageName), base64_decode($image));

        // Storage::disk('public')->put('images/' . $imageName, base64_decode($image));

        $media = new Media();
        $media->image = 'images/' . $imageName;
        $media->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Image successfully uploaded.',
            'data' => $host . '/' . 'images/' . $imageName,
        ], 200);
    }
    public function destroy(Request $request, $id)
    {
        try {
            $media = Media::findOrFail($id);

            $media->delete();
            if (file_exists(public_path($media->image))) {
                unlink(public_path($media->image));
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Media berhasil dihapus'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'media not found'
            ], 404);
        }
    }
}
