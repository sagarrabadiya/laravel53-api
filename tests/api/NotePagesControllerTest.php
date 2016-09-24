<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

class NotePagesControllerTest extends TestCase
{
    /**
     * @var App\Models\Note
     */
    private $notebook;

    /**
     * @var App\Models\NotePage
     */
    private $page;

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
        $this->notebook = factory(App\Models\Note::class)->create(['project_id'=>$this->project->id, 'created_by'=>$this->user->id]);
        $this->page = factory(App\Models\NotePage::class)->create(['note_id'=>$this->notebook->id]);
        $this->route = "/projects/".$this->project->id."/notes/".$this->notebook->id."/pages";
    }

    public function tearDown()
    {
        $this->project->team()->detach($this->user->id);
        $this->notebook->forceDelete();
        $this->page->forceDelete();
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

    public function test_it_should_list_all_the_pages_belongs_to_note()
    {
        $this->authUserGet($this->route)
            ->seeApiSuccess()
            ->seeJson(['id'=>$this->page->id])
            ->assertResponseOk();
    }

    public function test_it_should_not_allow_unauthorized_user_to_get()
    {
        $this->makeNotAllowedUser();
        $this->authUserGet($this->route)
            ->assertResponseStatus(403);
    }

    public function test_it_should_give_error_if_note_doesnt_exist()
    {
        $this->authUserGet("/projects/".$this->project->id."/notes/999/pages")
            ->assertResponseStatus(404);
    }

    public function test_it_should_allow_authorized_user_to_create_note_page()
    {
        $this->expectsEvents(\App\Events\Note\NotePageCreated::class);
        $params = $this->params();
        $this->authUserPost($this->route, $params)
            ->seeApiSuccess()
            ->seeJson(['title'=>$params['title']])
            ->seeJsonContains(['id' => $this->user->id, 'email' => $this->user->email, 'username' => $this->user->username])
            ->assertResponseStatus(201);
    }

    public function test_it_should_give_error_if_parameters_invalid()
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
        $this->expectsEvents(\App\Events\Note\NotePageCreated::class);
        $params = $this->params();
        $this->makeNotAllowedUser();
        $this->user->pivot->update(['note_write' => true]);
        $this->authUserPost($this->route, $params)
            ->seeApiSuccess()
            ->seeJson($params)
            ->seeJsonContains(['id' => $this->user->id, 'email' => $this->user->email, 'username' => $this->user->username])
            ->assertResponseStatus(201);
    }

    public function test_it_should_allow_authorized_user_to_update()
    {
        $this->expectsEvents(\App\Events\Note\NotePageUpdated::class);
        $this->authUserPut($this->route."/".$this->page->id, ['title' => 'updated'])
            ->seeApiSuccess()
            ->seeJson(['title'=>'updated'])
            ->seeJsonContains(['id' => $this->user->id, 'email' => $this->user->email, 'username' => $this->user->username])
            ->assertResponseOk();
    }

    public function test_it_should_allow_user_with_write_permission_to_update()
    {
        $this->expectsEvents(\App\Events\Note\NotePageUpdated::class);
        $this->makeNotAllowedUser();
        $this->user->pivot->update(['note_write' => true]);
        $this->authUserPut($this->route."/".$this->page->id, ['title' => 'updated'])
            ->seeApiSuccess()
            ->seeJson(['title'=>'updated'])
            ->seeJsonContains(['id' => $this->user->id, 'email' => $this->user->email, 'username' => $this->user->username])
            ->assertResponseOk();
    }

    public function test_it_should_not_allow_unauthorized_user_to_update()
    {
        $this->makeNotAllowedUser();
        $this->authUserPut($this->route."/".$this->page->id, ['title' => 'update'])
            ->assertResponseStatus(403);
    }


    public function test_it_should_allow_authorized_user_to_delete()
    {
        $this->authUserDelete($this->route."/".$this->page->id)
            ->assertResponseStatus(204);
    }

    public function test_it_should_allow_user_with_write_permission_to_delete()
    {
        $this->makeNotAllowedUser();
        $this->user->pivot->update(['note_write' => true]);
        $this->authUserDelete($this->route."/".$this->page->id)
            ->assertResponseStatus(204);
    }

    public function test_it_should_not_allow_unauthorized_user_to_delete()
    {
        $this->makeNotAllowedUser();
        $this->authUserDelete($this->route."/".$this->page->id)
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
