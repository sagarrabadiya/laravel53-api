<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

namespace App\Http\Controllers\Web;


use App\Core\Transformers\CompanyTransformer;
use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompaniesController extends Controller
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function byDomain(Request $request)
    {
        $this->validate($request, ['domain'    =>    'required']);
        $company = Company::byDomain($request->domain);
        if (! $company) {
            return response()->modelNotFound();
        }
        return response()->item($company, new CompanyTransformer(), ['key' => 'company']);
    }
}