<?php namespace App\Http\Middleware;

use Laravel\Passport\Http\Middleware\CreateFreshApiToken as LaravelCreateFreshApiToken;

class CreateFreshApiToken extends LaravelCreateFreshApiToken
{
    /**
     * override requestShouldReceiveFreshToken to support ajax login
     */
    protected function requestShouldReceiveFreshToken($request)
    {
        return ($request->isMethod('GET') || $request->ajax()) && $request->user();
    }
}
