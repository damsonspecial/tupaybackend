<?php

namespace App\Http\Controllers;

use App\Models\ExchangeRate;
use Illuminate\Http\Request;

class ExchangeRateController extends Controller
{
    public function index()
    {
        $rates = ExchangeRate::where('is_active', true)->get();

        return $this->success($rates, 'Exchange rates retrieved successfully');
    }
}
