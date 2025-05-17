<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;

class TempImageController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 403,
                'errors' => $validator->errors(),
            ], 403);
        }

        $image = $request->file('image');

        // Đặt tên file duy nhất tránh trùng (sửa lỗi trùng ảnh)
        $imageName = time() . '_' . uniqid() . '.' . $image->extension();

        // Đảm bảo thư mục tồn tại
        $tempPath = public_path('uploads/temp');
        $thumbPath = public_path('uploads/temp/thumb');
        if (!file_exists($tempPath)) mkdir($tempPath, 0777, true);
        if (!file_exists($thumbPath)) mkdir($thumbPath, 0777, true);

        // Lưu file gốc
        $image->move($tempPath, $imageName);

        // Lưu DB
        $tempImage = new TempImage();
        $tempImage->name = $imageName;
        $tempImage->save();

        // Tạo thumbnail dạng webp
        $manager = new ImageManager(Driver::class);
        $img = $manager->read($tempPath . '/' . $imageName);
        $img->coverDown(600, 650);
        $thumbWebpName = pathinfo($imageName, PATHINFO_FILENAME) . '.webp';
        $img->toWebp()->save($thumbPath . '/' . $thumbWebpName);

        return response()->json([
            'data' => [
                'id' => $tempImage->id,
                'name' => $tempImage->name,
                'image_url' => asset('uploads/temp/' . $imageName),
                'thumb_url' => asset('uploads/temp/thumb/' . $thumbWebpName),
            ],
            'message' => 'Image added successfully',
            'status' => 200,
        ], 200);
    }
}
