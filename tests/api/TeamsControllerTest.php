<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

class TeamsControllerTest extends TestCase
{
    /**
     * @var
     */
    protected $route;

    /**
     * @var \App\Models\User
     */
    private $tempUser;

    /**
     * @function to set initial user to project
     */
    public function setUp()
    {
        parent::setUp();
        $this->project = factory(App\Models\Project::class)->create(['company_id'=>$this->company->id]);
        $this->project->team()->attach($this->user->id);
        $this->user = $this->project->team()->find($this->user->id);
        $this->route = "/projects/".$this->project->id."/team";
    }

    public function tearDown()
    {
        if ($this->tempUser) {
            $this->tempUser->forceDelete();
        }
        parent::tearDown();
    }

    /**
     * @param bool $isAdmin
     * @return array
     */
    private function params($isAdmin = true)
    {
        $this->tempUser = factory(App\Models\User::class)->create(['company_id'=>$this->company->id, 'designation'=>$isAdmin ? 'admin' : 'designer']);
        return array_merge(['user_id'    =>    $this->tempUser->id], $this->getPermissions());
    }

    public function test_it_should_list_all_users_belongs_to_project()
    {
        $this->authUserGet($this->route)
            ->seeApiSuccess()
            ->seeJson(['id'=>$this->user->id])
            ->assertResponseOk();
    }

    public function test_it_should_not_include_user_that_is_not_in_project()
    {
        $this->params();
        $this->authUserGet($this->route)
            ->seeApiSuccess()
            ->dontSeeJson(['id'=>$this->tempUser->id])
            ->assertResponseOk();
    }

    public function test_it_should_create_user_in_the_project()
    {
        $this->expectsEvents(\App\Events\Team\UserInvited::class);
        $parameters = $this->params();
        $expected = $parameters;
        unset($expected['user_id']);
        $this->authUserPost($this->route, $parameters)
            ->seeApiSuccess()
            ->seeJson($expected)
            ->assertResponseStatus(201);
    }

    public function test_it_should_not_allow_unauthorized_user_to_add_member()
    {
        $this->makeNotAllowedUser();
        $params = $this->params();
        $this->authUserPost($this->route, $params)
            ->assertResponseStatus(403);
    }

    public function test_it_should_allow_non_admin_authorized_user_to_add_member()
    {
        $this->expectsEvents(\App\Events\Team\UserInvited::class);
        $this->makeNotAllowedUser();
        $this->user->pivot->update(['team_write' => true]);
        $params = $this->params();
        $expected = $params;
        unset($expected['user_id']);
        $this->authUserPost($this->route, $params)
            ->seeApiSuccess()
            ->seeJson($expected)
            ->assertResponseStatus(201);
    }

    public function test_it_should_give_error_if_user_doesnt_belong_to_company()
    {
        $this->authUserPost($this->route, ['user_id' => 999])
            ->assertResponseStatus(422);
    }

    public function test_it_should_give_team_member_by_id()
    {
        $this->authUserGet($this->route."/".$this->user->id)
            ->seeApiSuccess()
            ->seeJson(['id'=>$this->user->id])
            ->assertResponseOk();
    }

    public function test_it_should_give_not_found_if_user_not_available()
    {
        $this->authUserGet($this->route."/9999")
            ->assertResponseStatus(404);
    }

    public function test_it_should_not_allow_unauthorized_user_to_get_user()
    {
        $this->makeNotAllowedUser();
        $attr = $this->params();
        $this->project->team()->attach($this->tempUser->id, $attr);
        $this->authUserGet($this->route."/".$this->tempUser->id)
            ->assertResponseStatus(403);
    }

    public function test_it_should_allow_authorized_user_to_update_permissions()
    {
        $params = $this->getPermissions();
        $params['team_write'] = true;
        $this->authUserPut($this->route."/".$this->user->id, $params)
            ->seeApiSuccess()
            ->seeJson($params)
            ->assertResponseOk();
    }

    public function test_it_should_allow_user_with_write_permission_to_update_permissions()
    {
        $this->makeNotAllowedUser();
        $this->user->pivot->update(['team_write' => true]);
        $params = $this->getPermissions();
        $params['team_write'] = true;
        $this->authUserPut($this->route."/".$this->user->id, $params)
            ->seeApiSuccess()
            ->seeJson($params)
            ->assertResponseOk();
    }

    public function test_it_should_not_allow_unauthorized_user_to_update()
    {
        $params = $this->getPermissions();
        $params["team_write"] = true;
        $this->makeNotAllowedUser();
        $this->authUserPut($this->route."/".$this->user->id, $params)
            ->assertResponseStatus(403);
    }

    public function test_it_should_allow_user_to_delete_member()
    {
        $this->authUserDelete($this->route."/".$this->user->id)
            ->assertResponseStatus(204);
    }

    public function test_it_should_allow_user_with_write_permission_to_delete_permissions()
    {
        $this->makeNotAllowedUser();
        $this->user->pivot->update(['team_write' => true]);
        $this->authUserDelete($this->route."/".$this->user->id)
            ->assertResponseStatus(204);
    }

    public function test_it_should_not_allowed_unauthorized_user_to_delete()
    {
        $this->makeNotAllowedUser();
        $this->authUserDelete($this->route."/".$this->user->id)
            ->assertResponseStatus(403);
    }

    /**
     * @return mixed
     */
    private function makeNotAllowedUser()
    {
        $this->user->pivot->update(['team_read' => false, 'team_write' => false]);
        $this->user->designation = "designer";
        $this->user->save();
    }

    /**
     * @return array
     */
    private function getPermissions()
    {
        return collect(factory(App\Models\Role::class)->make())->except(['name'])->toArray();
    }
}
