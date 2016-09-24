<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

namespace App\Policies;

use App\Models\Company;
use App\Models\User;

class CompanyPolicy
{
    /**
     * @param User $user
     * @param Company $company
     * @return bool
     */
    public function show(User $user, Company $company)
    {
        return $user->company_id === $company->id;
    }

    /**
     * @param User $user
     * @param Company $company
     * @return bool
     */
    public function update(User $user, Company $company)
    {
        return $user->admin() && $user->company_id === $company->id;
    }
}
