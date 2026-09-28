<?php

namespace App\Repositories;
use Illuminate\Http\Request;
use Illuminate\Contracts\Filesystem\Filesystem;

class S3ClientRepo
{
    public static function Upload($folder, $image, $filename)
    {   
        try
        {
            $s3 = \Storage::disk('s3');
            $filePath = '/'.$folder.'/' . $filename;
            $s3->put($filePath, file_get_contents($image));
            return true;
        }
        catch(Exception $e)
        {
            return false;   
        }
    }
    
    public static function IsExisted($folder, $filename)
    {
        try
        {
            $s3 = \Storage::disk('s3');
            $filePath = '/'.$folder.'/' . $filename;
            return $s3->exists($filePath);
        }
        catch(Exception $e)
        {
            return false;   
        }
    }
    
    public static function Delete($folder, $filename)
    {
        try
        {
            $s3 = \Storage::disk('s3');
            $filePath = '/' . $folder . '/' . $filename;
            $s3->delete($filePath);
            return true;
        }
        catch(Exception $e)
        {
            return false;   
        }
    }
}