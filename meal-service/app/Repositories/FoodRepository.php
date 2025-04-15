<?php

namespace App\Repositories;

use App\Models\Food;
use App\Repositories\Interfaces\FoodRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class FoodRepository implements FoodRepositoryInterface
{
    public function getAll()
    {
        return Food::all();
    }

    public function getById($id)
    {
        return Food::findOrFail($id);
    }

    public function create(array $data)
    {
        if (isset($data['image'])) {
            $data['image'] = $this->storeImage($data['image'], $data['name']);
        }
        return Food::create($data);
    }

    public function update(Food $food, array $data)
    {
        $food->update($data);
        return $food;
    }

    public function delete(Food $food)
    {
        if ($food->image) {
            Storage::disk('public')->delete($food->image);
        }
        $food->delete();
    }

    public function updatefoodimage(Food $food, $image)
    {
        if ($image && $image->isValid() && in_array($image->getMimeType(), ['image/jpeg', 'image/png'])) {
            if ($food->image) {
                Storage::disk('s3')->delete($food->image);
            }
            try{
                $imagePath = $this->storeImage($image, $food->name);
    
                $food->update(['image' => $imagePath]);

            }catch(\Exception $e){
                throw new Exception($e->getMessage());
            }

            return $food;
        } else {
            throw new Exception('Invalid image file');
        }
    }

    private function storeImage($image, $foodName)
    {
        try {
            $filename = Str::slug($foodName) . '-' . time() . '.' . $image->getClientOriginalExtension();
            $path = 'foods/' . $filename;
            
            $fileContent = file_get_contents($image);
            
            Log::info('Attempting to upload file to S3', [
                'path' => $path,
                'filesize' => strlen($fileContent),
                'mimetype' => $image->getMimeType()
            ]);
            
            $result = Storage::disk('s3')->put($path, $fileContent, [
                'ContentType' => $image->getMimeType()  
            ]);
            
            Log::info('S3 upload result', ['success' => $result]);
            
            if (!$result) {
                throw new \Exception('Failed to upload image to S3');
            }
            
            return $path;
        } catch (\Exception $e) {
            Log::error('General upload error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
