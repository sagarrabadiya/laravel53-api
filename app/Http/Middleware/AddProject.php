<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

namespace App\Http\Middleware;

use App\Models\ResourceLimit;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\PreconditionFailedHttpException;

class AddProject
{
    public function handle(Request $request, \Closure $next)
    {
        $company = $request->user()->company;
        // company is not subscribed then it behaves as free account
        if (! $company->subscribed('main')) {
            $myPlan = ResourceLimit::plan('free');
        } else {
            // company is subscribed so behave according to subscribed plan
            $myPlan = ResourceLimit::plan($company->subscription('main')->stripe_plan);
        }
        if ($company->projects()->count() >= $myPlan->projects_allowed) {
            throw new PreconditionFailedHttpException('project creation limit exceeds');
        }

        return $next($request);
    }
}
