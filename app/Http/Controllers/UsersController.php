<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

namespace App\Http\Controllers;

use App\Core\Transformers\UserTransformer;
use App\Events\Team\UserCreated;
use App\Models\User;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Request;

/**
 * Class UsersController
 * @package App\Http\Controllers
 */
class UsersController extends Controller
{
    /**
     * @var UserTransformer
     */
    private $transformer;

    /**
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * UsersController constructor.
     * @param UserTransformer $transformer
     * @param Dispatcher $dispatcher
     */
    public function __construct(UserTransformer $transformer, Dispatcher $dispatcher)
    {
        $this->middleware('user.create', ['only'   =>  'store']);
        $this->transformer = $transformer;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return mixed
     */
    public function index()
    {
        $users = User::where('company_id', $this->user()->company_id)->paginate(self::PAGER);
        return response()->paginator($users, $this->transformer, ['key' => 'users']);
    }

    /**
     * @param $user
     * @return mixed
     */
    public function show(User $user)
    {
        return response()->item($user, $this->transformer, ['key' => 'user']);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        $this->authorize('create', User::class);
        $user = $this->user()->company->users()->create($request->all());
        $this->dispatcher->fire(new UserCreated($user));
        return response()->created($user, $this->transformer, ['key' => 'user']);
    }

    /**
     * @param Request $request
     * @param $user
     * @return mixed
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);
        $user->update($request->all());
        return response()->item($user, $this->transformer, ['key' => 'user']);
    }

    /**
     * @param $user
     * @return mixed
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        $user->delete();
        return response()->deleted();
    }

    /**
     * @return mixed
     */
    public function me()
    {
        $user = $this->user();
        return response()->item($user, $this->transformer);
    }
}
