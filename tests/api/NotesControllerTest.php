<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */


class NotesControllerTest extends TestCase
{

    /**
     * @var \App\Models\Note
     */
    private $notebook;

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
        $this->notebook = factory(App\Models\Note::class)->create(['project_id'=>$this->project->id]);
        $this->route = "/projects/".$this->project->id."/notes";
    }

    public function tearDown()
    {
        $this->project->team()->detach($this->user->id);
        $this->notebook->forceDelete();
        $this->project->forceDelete();
        parent::tearDown();
    }

    /**
     * @return array
     */
    private function params()
    {
        return [
            'title'  =>  $this->faker->sentence(10),
            'description'    =>  $this->faker->paragraph(5)
        ];
    }

    public function test_it_should_list_all_note_books()
    {
        $this->authUserGet($this->route)
            ->seeApiSuccess()
            ->seeJson(['id'=>$this->notebook->id])
            ->assertResponseOk();
    }

    public function test_it_should_not_allow_unauthorized_user_to_get_note()
    {
        $this->makeNotAllowedUser();
        $this->authUserGet($this->route)
            ->assertResponseStatus(403);
    }

    public function test_it_should_allow_authorized_user_to_create_note()
    {
        $this->expectsEvents(\App\Events\Note\NoteCreated::class);
        $params = $this->params();
        $this->authUserPost($this->route, $params)
            ->seeApiSuccess()
            ->seeJson($params)
            ->seeJsonContains(['id' => $this->user->id, 'username' => $this->user->username, 'email' => $this->user->email])
            ->assertResponseStatus(201);
    }

    public function test_it_should_give_error_if_invalid_input_is_give()
    {
        $this->authUserPost($this->route)
            ->assertResponseStatus(422);
    }

    public function test_it_should_not_allow_unauthorized_user_to_create()
    {
        $this->makeNotAllowedUser();
        $params = $this->params();
        $this->authUserPost($this->route, $params)
            ->assertResponseStatus(403);
    }

    public function test_it_should_allow_user_with_write_permission_to_create()
    {
        $this->expectsEvents(\App\Events\Note\NoteCreated::class);
        $this->makeNotAllowedUser();
        $this->user->pivot->update(['note_write' => true]);
        $params = $this->params();
        $this->authUserPost($this->route, $params)
            ->seeApiSuccess()
            ->seeJson($params)
            ->seeJsonContains(['id' => $this->user->id, 'username' => $this->user->username, 'email' => $this->user->email])
            ->assertResponseStatus(201);
    }

    public function test_it_should_allow_authorized_user_to_update_note()
    {
        $this->expectsEvents(\App\Events\Note\NoteUpdated::class);
        $this->authUserPut($this->route."/".$this->notebook->id, ['title' => 'updated'])
            ->seeApiSuccess()
            ->seeJson(['title'=>'updated'])
            ->seeJsonContains(['id' => $this->user->id, 'username' => $this->user->username, 'email' => $this->user->email])
            ->assertResponseOk();
    }

    public function test_it_should_allow_user_with_write_permission_to_update()
    {
        $this->expectsEvents(\App\Events\Note\NoteUpdated::class);
        $this->makeNotAllowedUser();
        $this->user->pivot->update(['note_write' => true]);
        $this->authUserPut($this->route."/".$this->notebook->id, ['title' => 'updated'])
            ->seeApiSuccess()
            ->seeJson(['title'=>'updated'])
            ->seeJsonContains(['id' => $this->user->id, 'username' => $this->user->username, 'email' => $this->user->email])
            ->assertResponseOk();
    }

    public function test_it_should_not_allow_unauthorized_user_to_update()
    {
        $this->makeNotAllowedUser();
        $this->authUserPut($this->route."/".$this->notebook->id, ['title' => 'updated'])
            ->assertResponseStatus(403);
    }

    public function test_it_should_allow_authorized_user_to_delete()
    {
        $this->authUserDelete($this->route."/".$this->notebook->id)
            ->assertResponseStatus(204);
    }

    public function test_it_should_allow_user_with_write_permission_to_delete()
    {
        $this->makeNotAllowedUser();
        $this->user->pivot->update(['note_write' => true]);
        $this->authUserDelete($this->route."/".$this->notebook->id)
            ->assertResponseStatus(204);
    }

    public function test_it_should_not_allow_unauthorized_user_to_delete()
    {
        $this->makeNotAllowedUser();
        $this->authUserDelete($this->route."/".$this->notebook->id)
            ->assertResponseStatus(403);
    }

    /**
     * @return mixed
     */
    private function makeNotAllowedUser()
    {
        $this->user->pivot->update(['note_read' => false, 'note_write' => false]);
        $this->user->designation = "designer";
        $this->user->save();
    }
}
