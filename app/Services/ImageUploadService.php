<?php

namespace App\Services;

use Illuminate\Http\Request;

class ImageUploadService
{
    /**
     * Upload image to public/uploads
     * Save FULL URL to DB
     */
    public function upload(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpg,jpeg,png|max:5120',
            ]);

            $image = $request->file('image');
            $extension = $image->getClientOriginalExtension();
            $imageName = time() . '.' . strtolower($extension);

            $uploadPath = public_path('uploads');

            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $finalPath = $uploadPath . '/' . $imageName;
            $image->move($uploadPath, $imageName);
            chmod($finalPath, 0644);

            $imageURL = asset('uploads/' . $imageName);

            return response()->json([
                'status' => 'success',
                'image_url' => $imageURL,
                'message' => 'Image uploaded successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Image upload failed: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Upload image to public/uploads/images
     */
    public function publicUpload(Request $request)
    {
        return $this->upload($request);
    }
}
