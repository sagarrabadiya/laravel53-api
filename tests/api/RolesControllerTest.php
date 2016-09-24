<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

class ProjectRolesControllerTest extends TestCase
{
    /**
     * @var
     */
    protected $route;

    /**
     * @var \App\Models\Role
     */
    protected $role;


    /**
     *
     */
    public function setUp()
    {
        parent::setUp();
        $this->project = factory(App\Models\Project::class)->create(['company_id'=>$this->company->id]);
        $this->project->team()->attach($this->user->id);
        $this->role = $this->project->roles()->save(factory(App\Models\Role::class)->make());
        $this->route = "/projects/".$this->project->id."/roles";
    }

    public function tearDown()
    {
        $this->project->team()->detach($this->user->id);
        $this->role->forceDelete();
        $this->project->forceDelete();
        parent::tearDown();
    }


    public function test_it_should_list_all_the_roles_belongs_to_project()
    {
        $this->authUserGet($this->route)
            ->seeApiSuccess()
            ->seeJson(['id'=>$this->role->id])
            ->assertResponseOk();
    }

    public function test_it_should_allow_admin_user_to_create_role()
    {
        $parameters = $this->params();
        $this->authUserPost($this->route, $parameters)
            ->seeApiSuccess()
            ->seeJson(['name'=>$parameters['name']])
            ->assertResponseStatus(201);
    }

    public function test_it_should_not_allow_non_admin_user_to_create_role()
    {
        $this->makeUserNonAdmin();
        $params = $this->params();
        $this->authUserPost($this->route, $params)
            ->assertResponseStatus(403);
    }

    public function test_it_should_allow_user_get_single_role_by_id()
    {
        $this->authUserGet($this->route."/".$this->role->id)
            ->seeApiSuccess()
            ->seeJson(['id'=>$this->role->id])
            ->assertResponseOk();
    }

    public function test_it_should_allow_non_admin_user_to_get_single_role()
    {
        $this->makeUserNonAdmin();
        $this->authUserGet($this->route."/".$this->role->id)
            ->seeApiSuccess()
            ->seeJson(['id' => $this->role->id])
            ->assertResponseOk();
    }

    public function test_it_should_give_not_found_if_role_not_belongs_to_project()
    {
        $this->authUserGet($this->route."/9999999")
            ->assertResponseStatus(404);
    }

    public function test_it_should_allow_admin_user_to_update_role()
    {
        $this->authUserPut($this->route."/".$this->role->id, ['name' => 'updated'])
            ->seeApiSuccess()
            ->seeJson(['name'=>'updated'])
            ->assertResponseOk();
    }

    public function test_it_should_not_allow_non_admin_user_to_update()
    {
        $this->makeUserNonAdmin();
        $this->authUserPut($this->route."/".$this->role->id, ['name' => 'updated'])
            ->assertResponseStatus(403);
    }

    public function test_it_should_give_error_if_parameter_is_invalid()
    {
        $this->authUserPost($this->route)
            ->assertResponseStatus(422);
    }

    public function test_it_should_allow_admin_user_to_delete_role()
    {
        $this->authUserDelete($this->route."/".$this->role->id)
            ->assertResponseStatus(204);
    }

    public function test_it_should_not_allow_non_admin_to_delete_role()
    {
        $this->makeUserNonAdmin();
        $this->authUserDelete($this->route."/".$this->role->id)
            ->assertResponseStatus(403);
    }

    /**
     * @return mixed
     */
    private function params()
    {
        return factory(App\Models\Role::class)->make()->toArray();
    }
}
