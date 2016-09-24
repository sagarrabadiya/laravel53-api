<?php

namespace App\Providers;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\ServiceProvider;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\TransformerAbstract;
use Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class ApiServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerResponseMacros();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * register all available response macros
     */
    public function registerResponseMacros()
    {
        Response::macro('collection', function ($data, TransformerAbstract $transformer, $settings = []) {
            return fractal()->collection($data, $transformer, $settings);
        });

        Response::macro('item', function ($data, TransformerAbstract $transformer, $settings = []) {
            return fractal()->item($data, $transformer, $settings);
        });

        Response::macro('created', function ($data, TransformerAbstract $transformer, $settings = []) {
            return response()->json(fractal()->item($data, $transformer, $settings), 201);
        });

        Response::macro('paginator', function (LengthAwarePaginator $data, TransformerAbstract $transformer, $settings = []) {
            return fractal()
                    ->collection($data->getCollection(), $transformer, $settings)
                    ->paginateWith(new IlluminatePaginatorAdapter($data));
        });

        Response::macro('deleted', function () {
            return response()->json('', 204);
        });

        Response::macro('modelNotFound', function ($message = "Not found") {
            return response()->error($message, 404);
        });

        Response::macro('array', function ($data = []) {
            return response()->json($data);
        });

        Response::macro('unauthorized', function ($message = "You are not authorized to perform that!") {
            throw new UnauthorizedHttpException($message);
        });

        Response::macro('error', function ($message, $status = 400) {
            return response()->json([
                'errors'          => [$message],
                'status_code'     => $status,
            ], $status);
        });
    }
}
