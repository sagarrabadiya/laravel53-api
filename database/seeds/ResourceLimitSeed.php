<?php
use Illuminate\Database\Seeder;

/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */
class ResourceLimitSeed extends Seeder
{
    /**
     *
     */
    public function run()
    {

        App\Models\ResourceLimit::create([
            'stripe_plan_identifier'    =>  'free',
            'projects_allowed'  =>  1,
            'archived_projects_allowed' =>  0,
            'users_allowed' =>  5,
            'storage_allowed'   =>  100
        ]);

        App\Models\ResourceLimit::create([
            'stripe_plan_identifier'    =>  'basic',
            'projects_allowed'  =>  5,
            'archived_projects_allowed' =>  0,
            'users_allowed' =>  5,
            'storage_allowed'   =>  500
        ]);

        App\Models\ResourceLimit::create([
            'stripe_plan_identifier'    =>  'medium',
            'projects_allowed'  =>  10,
            'archived_projects_allowed' =>  3,
            'users_allowed' =>  10,
            'storage_allowed'   =>  1024
        ]);

        App\Models\ResourceLimit::create([
            'stripe_plan_identifier'    =>  'large',
            'projects_allowed'  =>  25,
            'archived_projects_allowed' =>  10,
            'users_allowed' =>  20,
            'storage_allowed'   =>  2048
        ]);

        App\Models\ResourceLimit::create([
            'stripe_plan_identifier'    =>  'enterprise',
            'projects_allowed'  =>  999,
            'archived_projects_allowed' =>  999,
            'users_allowed' =>  999,
            'storage_allowed'   =>  -1
        ]);
    }
}