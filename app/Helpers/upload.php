<?php

if (!function_exists('upload_to_public')) {
    function upload_to_public($file, $path)
    {
        // إنشاء اسم فريد للملف
        $filename = time() . mt_rand() . '.' . $file->getClientOriginalExtension();

        // التأكد من وجود المجلد
        $directory = public_path($path);
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        // نقل الملف إلى المجلد المطلوب
        $file->move($directory, $filename);

        // إرجاع مسار الملف النسبي
        return $path . '/' . $filename;
    }
}

if (!function_exists('delete_from_public')) {
    function delete_from_public($path)
    {
        $fullPath = public_path($path);
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }
}