<?php

namespace App\Services;
use ImageIntervention;

class ImageService
{
    public function updateImage($image, $path)
    {
        $image = ImageIntervention::make($request->file('image'));

        if (!empty($model->image)) {
            $currentImage = public_path() . $path . $model->image;

            if (file_exists($currentImage)) {
                unlink($currentImage);
            }
        }

        $file = $request->file('image');
        $extension = $file->getClientOriginalExtension();

        $image->crop(300, 300);

        $name = time() . '.' . $extension;
        $image->save(public_path() . $path . $name);

        if ($methodType === 'store') {
            $model->user_id = $request->get('user_id');
        }

        $model->image = $name;

        $model->save();



        $name = time().'.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
        ImageIntervention::make($image)->save(public_path($path).$name);

        return $name;
    }
}
