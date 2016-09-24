<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    const PAGER = 50;

    /**
     * @return \App\Models\User|null
     */
    public function user()
    {
        return Auth::user();
    }

    /**
     * function to get only inputs those are posted
     * @param Request $request
     * @param array $attributes
     * @return array
     */
    protected function filterInputs(Request $request, array $attributes = array())
    {
        $availableInputs = [];
        foreach ($attributes as $inputKey) {
            if ($request->has($inputKey)) {
                $availableInputs[$inputKey] = $request->input($inputKey);
            }
        }
        return $availableInputs;
    }
}
