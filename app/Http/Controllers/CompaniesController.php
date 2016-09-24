<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Core\Transformers\CompanyTransformer;
use Illuminate\Http\Request;

/**
 * Class CompaniesController.
 */
class CompaniesController extends Controller
{
    /**
     * @param \App\Models\Company $company
     *
     * @return mixed
     */
    public function show(Company $company)
    {
        $this->authorize('show', $company);

        return response()->item($company, new CompanyTransformer(), ['key' => 'company']);
    }

    /**
     * @param Company $company
     * @param Request $request
     *
     * @return mixed
     */
    public function update(Request $request, Company $company)
    {
        $this->authorize('update', $company);
        $company->update($request->all());

        return response()->item($company, new CompanyTransformer(), ['key' => 'company']);
    }
}
