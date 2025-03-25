<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    public function index(): View
    {
        Log::info('情報メッセージをここに書きます');
        return view('attendance');
    }
}
