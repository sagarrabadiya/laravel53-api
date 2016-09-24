<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

namespace App\Core\Transformers;

use App\Models\Company;

/**
 * Class CompanyTransformer
 * @package App\Transformers
 */
class CompanyTransformer extends Transformer
{

    /**
     * @param $company
     * @return array
     */
    public function transform(Company $company)
    {
        return [
            'id'    =>    $company->id,
            'name'    =>    $company->name,
            'domain' => $company->domain,
            'settings'    =>    $company->settings,
            'created_at'    =>    $company->created_at->toIso8601String(),
            'updated_at'    =>    $company->updated_at->toIso8601String()
        ];
    }
}
