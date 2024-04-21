<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class BackUpDownload extends Controller
{
    public function download()
    {
        if (auth()->user()->hasRole('Super Admin') && Storage::disk('local')->exists(env("APP_NAME"))) {

            $file_path   = storage_path(). DIRECTORY_SEPARATOR. 'app'. DIRECTORY_SEPARATOR. env("APP_NAME"). DIRECTORY_SEPARATOR;
            $latest_file = scandir($file_path, SCANDIR_SORT_DESCENDING);

            return response()->download(storage_path('app'. DIRECTORY_SEPARATOR. env("APP_NAME"). DIRECTORY_SEPARATOR. $latest_file[0]));
        }

        return redirect('/404');
    }
}
