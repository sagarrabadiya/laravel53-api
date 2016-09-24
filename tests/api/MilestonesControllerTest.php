<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

class MilestonesControllerTest extends TestCase
{
    /**
     * @var App\Models\Milestone
     */
    private $milestone;

    /**
     * @var App\Models\Milestone
     */
    private $tempMilestone;

    /**
     * @var
     */
    private $route;

    /**
     *
     */
    public function setUp()
    {
        parent::setUp();
        $this->project = factory(App\Models\Project::class)->create(['company_id'=>$this->company->id]);
        $this->project->team()->attach($this->user->id);
        $this->user = $this->project->team()->find($this->user->id);
        $this->milestone = factory(App\Models\Milestone::class)->create(['project_id'=>$this->project->id]);
        $this->route = "/projects/".$this->project->id."/milestones";
    }

    public function tearDown()
    {
        $this->project->team()->detach($this->user->id);
        $this->milestone->forceDelete();
        $this->project->forceDelete();
        if ($this->tempMilestone) {
            $this->tempMilestone->forceDelete();
        }
        parent::tearDown();
    }

    /**
     * @return array
     */
    private function params()
    {
        return factory(App\Models\Milestone::class)->make()->toArray();
    }

    public function test_it_should_list_all_milestone()
    {
        $this->authUserGet($this->route)
            ->seeApiSuccess()
            ->seeJson(['id'=>$this->milestone->id])
            ->assertResponseOk();
    }

    public function test_it_should_not_list_milestones_with_unauthorized()
    {
        $this->makeNotAllowedUser();
        $this->authUserGet($this->route)
            ->assertResponseStatus(403);
    }

    public function test_it_should_give_error_if_invalid_parameters()
    {
        $this->authUserPost($this->route, [])
            ->assertResponseStatus(422);
    }

    public function test_it_should_allow_authorized_user_to_create_milestone()
    {
        $this->expectsEvents(\App\Events\Milestone\MilestoneCreated::class);
        $params = $this->params();
        $this->authUserPost($this->route, $params)
            ->seeApiSuccess()
            ->seeJson(['title'=>$params['title']])
            ->seeJsonContains(['id' => $this->user->id, 'username' => $this->user->username, 'email' => $this->user->email])
            ->assertResponseStatus(201);
    }

    public function test_it_should_not_allow_unauthorized_user_to_create_milestone()
    {
        $this->makeNotAllowedUser();
        $params = $this->params();
        $this->authUserPost($this->route, $params)
            ->assertResponseStatus(403);
    }

    public function test_it_should_allow_user_with_write_permission_to_create_milestone()
    {
        $this->expectsEvents(\App\Events\Milestone\MilestoneCreated::class);
        $this->makeNotAllowedUser();
        $this->user->pivot->update(['milestone_write' => true]);
        $params = $this->params();
        $this->authUserPost($this->route, $params)
            ->seeApiSuccess()
            ->seeJson(['title' => $params['title']])
            ->seeJsonContains(['id' => $this->user->id, 'username' => $this->user->username, 'email' => $this->user->email])
            ->assertResponseStatus(201);
    }

    public function test_it_should_allow_authorized_user_to_update()
    {
        $this->expectsEvents(\App\Events\Milestone\MilestoneUpdated::class);
        $this->authUserPut($this->route."/".$this->milestone->id, ['title' => 'updated'])
            ->seeApiSuccess()
            ->seeJson(['title'=>'updated'])
            ->seeJsonContains(['id' => $this->user->id, 'username' => $this->user->username, 'email' => $this->user->email])
            ->assertResponseOk();
    }

    public function test_it_should_allow_user_with_write_permission_to_update()
    {
        $this->expectsEvents(\App\Events\Milestone\MilestoneUpdated::class);
        $this->makeNotAllowedUser();
        $this->user->pivot->update(['milestone_write' => true]);
        $this->authUserPut($this->route."/".$this->milestone->id, ['title' => 'updated'])
            ->seeApiSuccess()
            ->seeJson(['title'=>'updated'])
            ->seeJsonContains(['id' => $this->user->id, 'username' => $this->user->username, 'email' => $this->user->email])
            ->assertResponseOk();
    }

    public function test_it_should_not_allow_unauthorized_user_to_update()
    {
        $this->makeNotAllowedUser();
        $this->authUserPut($this->route."/".$this->milestone->id, ['title' => 'updated'])
            ->assertResponseStatus(403);
    }

    public function test_it_should_allow_authorized_user_to_delete_milestone()
    {
        $this->authUserDelete($this->route."/".$this->milestone->id)
            ->assertResponseStatus(204);
    }

    public function test_it_should_allow_user_with_write_permission_to_delete()
    {
        $this->makeNotAllowedUser();
        $this->user->pivot->update(['milestone_write' => true]);
        $this->authUserDelete($this->route."/".$this->milestone->id)
            ->assertResponseStatus(204);
    }

    public function test_it_should_not_allow_unauthorized_user_to_delete()
    {
        $this->makeNotAllowedUser();
        $this->authUserDelete($this->route."/".$this->milestone->id)
            ->assertResponseStatus(403);
    }

    /**
     * @return mixed
     */
    private function makeNotAllowedUser()
    {
        $this->user->pivot->update(['milestone_read' => false, 'milestone_write' => false]);
        $this->user->designation = "designer";
        $this->user->save();
    }
}
