<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */


class UsersControllerTest extends TestCase
{
    private $route = "/users";

    private $tempUser;

    public function setUp()
    {
        parent::setUp();
    }

    public function tearDown()
    {
        if ($this->tempUser) {
            $this->tempUser->forceDelete();
        }
        parent::tearDown();
    }

    /**
     * @param array $attributes
     * @return mixed
     */
    private function createUser($attributes = array())
    {
        return $this->tempUser = factory(App\Models\User::class)->create(['company_id' => $this->company->id]);
    }


    public function test_it_should_list_all_the_users()
    {
        $this->authUserGet($this->route)
            ->seeApiSuccess()
            ->seeJson(['id'=>$this->user->id])->assertResponseOk();
    }

    public function test_it_should_show_single_user_by_id()
    {
        $this->authUserGet($this->route."/".$this->user->id)
            ->seeApiSuccess()
            ->seeJson(['id'=>$this->user->id])
            ->assertResponseOk();
    }

    public function test_it_should_show_other_user_if_authorized()
    {
        $this->createUser();
        $this->authUserGet($this->route."/".$this->tempUser->id)
            ->seeApiSuccess()
            ->seeJson(['id'=>$this->tempUser->id])
            ->assertResponseOk();
    }

    public function test_it_should_allow_to_create_user()
    {
        $this->expectsEvents(\App\Events\Team\UserCreated::class);
        $data = $this->params();
        $this->authUserPost($this->route, $data)
            ->seeApiSuccess()
            ->seeJson(['username'=>$data['username']])
            ->assertResponseStatus(201);
    }

    public function test_it_should_not_allow_non_admin_user_to_create()
    {
        $data = $this->params();
        $this->makeUserNonAdmin();
        $this->authUserPost($this->route, $data)
            ->assertResponseStatus(403);
    }

    public function test_it_should_allow_admin_to_update_any_user_data()
    {
        $this->createUser();
        $this->authUserPut($this->route."/".$this->tempUser->id, ['firstname' => 'updated'])
            ->seeApiSuccess()
            ->seeJson(['firstname'=>'updated'])
            ->assertResponseOk();
    }

    public function test_it_should_allow_user_to_update_himself()
    {
        $this->makeUserNonAdmin();
        $this->authUserPut($this->route."/".$this->user->id, ['firstname' => 'updated'])
            ->seeApiSuccess()
            ->seeJson(['firstname'=>'updated'])
            ->assertResponseStatus(200);
    }

    public function test_it_should_not_allow_user_to_update_other_user()
    {
        $this->createUser();
        $this->makeUserNonAdmin();
        $this->authUserPut($this->route."/".$this->tempUser->id, ['firstname' => 'updated'])
            ->assertResponseStatus(403);
    }

    public function test_it_should_allow_admin_to_delete_user()
    {
        $this->createUser();
        $this->authUserDelete($this->route."/".$this->tempUser->id)
            ->assertResponseStatus(204);
    }

    public function test_it_should_not_allow_non_admin_user_to_delete()
    {
        $this->makeUserNonAdmin();
        $this->createUser();
        $this->authUserDelete($this->route."/".$this->tempUser->id)
            ->assertResponseStatus(403);
    }

    public function test_it_should_give_my_info()
    {
        $this->authUserGet('/me')
            ->seeApiSuccess()
            ->seeJson(['id'=>$this->user->id])
            ->assertResponseOk();
    }

    /**
     * @param array $attributes
     * @return mixed
     */
    private function params()
    {
        return [
            'username'  =>  $this->faker->userName,
            'password'   =>  '123456',
            'designation'  =>  $this->faker->randomKey(['admin'=>'admin', 'manager'=>'manager', 'developer'=>'developer', 'designer'=>'designer']),
            'firstname' =>  $this->faker->firstName,
            'lastname'   =>  $this->faker->lastName,
            'email'  =>  $this->faker->email,
            'avatar'  =>  $this->faker->imageUrl(50, 50),
            'password' => '123456',
        ];
    }
}
