<?php

namespace App\Http\Controllers;

use App\Actions\Dashboard\GetDashboardDataAction;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request, GetDashboardDataAction $action)
    {
        $data = $action->execute($request->user());

        return $this->success($data, 'Dashboard data retrieved');
    }
}
