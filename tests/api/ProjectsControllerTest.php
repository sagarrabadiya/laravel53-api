<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

class ProjectsControllerTest extends TestCase
{
    /**
     * @var string
     */
    protected $route = "/projects";

    /**
     * @var App\Models\Project
     */
    protected $project;

    /**
     * @var \App\Models\Project
     */
    protected $tempProject;

    /**
     * @function to setup project initial
     */
    public function setUp()
    {
        parent::setUp();
        $this->project = factory(App\Models\Project::class)->create(['company_id'=>$this->company->id]);
        $this->project->team()->attach($this->user->id);
    }

    public function tearDown()
    {
        $this->project->team()->detach($this->user->id);
        $this->project->forceDelete();
        if ($this->tempProject) {
            $this->tempProject->forceDelete();
        }

        parent::tearDown();
    }

    private function createProject()
    {
        $this->tempProject = factory(App\Models\Project::class)->create(['company_id' => $this->company->id]);
    }

    /**
     * @param array $attr
     * @return array
     */
    private function params($attr = array())
    {
        return factory(App\Models\Project::class)->make($attr)->toArray();
    }


    public function test_it_should_list_all_the_projects_in_which_user_belongs()
    {
        $this->authUserGet($this->route)
            ->seeApiSuccess()
            ->seeJson(['id'=>$this->project->id])
            ->assertResponseOk();
    }

    public function test_it_should_not_list_project_in_which_user_is_not_included()
    {
        $this->createProject();
        $this->authUserGet($this->route)
            ->seeApiSuccess()
            ->dontSeeJson(['id'=>$this->tempProject->id])
            ->assertResponseOk();
    }

    public function test_it_should_allow_admin_user_to_create_project()
    {
        // make project limit to 3 so create test won't be failed
        $plan = \App\Models\ResourceLimit::plan('free');
        $plan->projects_allowed = 3;
        $plan->save();

        $this->expectsEvents(\App\Events\Team\UserInvited::class);
        $params = $this->params();

        $this->authUserPost($this->route, $params)
            ->seeApiSuccess()
            ->seeJson(['name'=>$params['name']])
            ->assertResponseStatus(201);
    }

    public function test_it_should_not_allow_non_admin_user_to_create_project()
    {
        // make project limit to 3 so create test won't be failed
        $plan = \App\Models\ResourceLimit::plan('free');
        $plan->projects_allowed = 3;
        $plan->save();

        $this->makeUserNonAdmin();

        $params = $this->params();

        $this->authUserPost($this->route, $params)
            ->assertResponseStatus(403);
    }

    public function test_it_should_give_single_project_by_id()
    {
        $this->authUserGet($this->route."/".$this->project->id)
            ->seeApiSuccess()
            ->seeJson(['id'=>$this->project->id])
            ->assertResponseOk();
    }

    public function test_it_should_not_give_single_project_if_user_not_included()
    {
        $this->createProject();
        $this->authUserGet($this->route."/".$this->tempProject->id)
            ->assertResponseStatus(404);
    }

    public function test_it_should_allow_admin_user_user_to_update_project()
    {
        $this->authUserPut($this->route."/".$this->project->id,['name'=>'updated'])
            ->seeApiSuccess()
            ->seeJson(['name'=>'updated'])
            ->assertResponseOk();
    }

    public function test_it_should_not_allow_non_admin_user_to_update_project()
    {
        $this->makeUserNonAdmin();
        $this->authUserPut($this->route."/".$this->project->id)
            ->assertResponseStatus(403);
    }

    public function test_it_should_allow_admin_user_to_delete_project()
    {
        $this->authUserDelete($this->route."/".$this->project->id)
            ->assertResponseStatus(204);
    }

    public function test_it_should_not_allow_non_admin_user_to_delete_project()
    {
        $this->makeUserNonAdmin();
        $this->authUserDelete($this->route."/".$this->project->id)
            ->assertResponseStatus(403);
    }

    public function test_it_should_give_statistics_for_the_project()
    {
        $this->authUserGet($this->route."/".$this->project->id."/stats")
            ->seeApiSuccess();
        $responseArray = json_decode($this->response->getContent(), true);
        $this->assertArrayHasKey('tickets', $responseArray);
        $this->assertArrayHasKey('team', $responseArray);
        $this->assertArrayHasKey('board_items', $responseArray);
        $this->assertArrayHasKey('milestones', $responseArray);
    }

    public function test_it_should_not_give_project_statistics_if_user_not_included()
    {
        $this->createProject();
        $this->makeUserNonAdmin();
        $this->authUserGet($this->route."/".$this->tempProject->id."/stats")
            ->assertResponseStatus(404);
    }
}
