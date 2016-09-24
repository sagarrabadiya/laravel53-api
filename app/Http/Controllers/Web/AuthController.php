<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

namespace App\Http\Controllers\Web;


use App\Core\Transformers\UserTransformer;
use App\Http\Controllers\Controller;

/**
 * Class AuthController
 * @package App\Http\Controllers\Web
 */
class AuthController extends Controller
{

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request()->only(['email', 'password']);

        if (auth()->attempt($credentials)) {
            return response()->item(auth()->user(), new UserTransformer());
        }

        return response()->error('invalid credentials', 401);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();
        return response()->json('');
    }
}
