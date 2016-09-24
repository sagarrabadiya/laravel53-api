<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

namespace App\Http\Middleware;

use App\Models\ResourceLimit;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\PreconditionFailedHttpException;

class UpdateProject
{
    public function handle(Request $request, \Closure $next)
    {
        if ($request->has('active')) {
            $company = $request->user()->company;
            // company is not subscribed then it behaves as free account
            if (!$company->subscribed('main')) {
                $myPlan = ResourceLimit::plan('free');
            } else {
                // company is subscribed so behave according to subscribed plan
                $myPlan = ResourceLimit::plan($company->subscription('main')->stripe_plan);
            }
            if ($request->input('active') == false
                && $company->projects()->archive()->count() >= $myPlan->archived_projects_allowed) {
                throw new PreconditionFailedHttpException('archive project limit exceeds');
            }
            if ($request->input('active') == true
                && $company->projects()->active()->count() >= $myPlan->projects_allowed) {
                throw new PreconditionFailedHttpException('active project limit exceeds');
            }
        }

        return $next($request);
    }
}
