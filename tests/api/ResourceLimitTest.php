<?php

/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */
class ResourceLimitTest extends TestCase
{

    public function test_with_free_user()
    {
        $plan = App\Models\ResourceLimit::plan('free');
        $this->it_should_not_allow_to_create_project_if_limit_exceeds($plan);
        $this->it_should_not_allow_to_create_user_if_limit_exceeds($plan);
        $this->it_should_not_allow_to_create_file_if_limit_exceeds($plan);
    }

    public function test_with_basic_subscription()
    {
        $this->company->newSubscription('main', 'basic')->trialDays(10)->create();
        $plan = App\Models\ResourceLimit::plan('basic');
        $this->it_should_not_allow_to_create_project_if_limit_exceeds($plan);
        $this->it_should_not_allow_to_create_user_if_limit_exceeds($plan);
        $this->it_should_not_allow_to_create_file_if_limit_exceeds($plan);
    }

    public function it_should_not_allow_to_create_project_if_limit_exceeds($plan)
    {
        factory(\App\Models\Project::class, $plan->projects_allowed)->create(['company_id' => $this->company->id]);
        $params = factory(\App\Models\Project::class)->make()->toArray();
        $this->authUserPost('/projects', $params)
            ->assertResponseStatus(412);
    }

    public function it_should_not_allow_to_create_user_if_limit_exceeds($plan)
    {
        factory(\App\Models\User::class, $plan->users_allowed)->create(['company_id' => $this->company->id]);
        $params = factory(\App\Models\User::class)->make()->toArray();
        $this->authUserPost('/users', $params)
            ->assertResponseStatus(412);
    }

    public function it_should_not_allow_to_create_file_if_limit_exceeds($plan)
    {
        $this->company->files()->create([
            'name'  =>  'test.png',
            'salt'  =>  str_random(10),
            'ext'   =>  'png',
            'size'  =>  $plan->storage_allowed * 1024 * 1024
        ]);
        $file = new \Symfony\Component\HttpFoundation\File\UploadedFile(base_path('tests/test_files/sample.png'), 'sample.png');
        $server = ['HTTP_Authorization' => 'Bearer '.$this->authUserToken];
        $this->call("POST", '/api/companies/' . $this->company->id . "/files", [], [], ['file' => $file], $server);
        $this->assertResponseStatus(412);
    }
}
