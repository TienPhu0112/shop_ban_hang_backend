<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function addImage(Request $request)
    {
        return uploadFileHelper('user_avatar', $request->file('image'));
    }
}
