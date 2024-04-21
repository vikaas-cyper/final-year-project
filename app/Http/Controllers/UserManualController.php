<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserManualController extends Controller
{
    public function download()
    {
        if (auth()->user()->hasRole('Super Admin')) {
            $folderFilePath = public_path('/import_templates/storage/User_Manual.pdf');
            return response()->download($folderFilePath,"", []);
        }

        return redirect('/404');
    }
}
