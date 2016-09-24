<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */


class BoardItemsControllerTest extends TestCase
{
    /**
     * @var App\Models\BoardItem
     */
    protected $boardItem;

    /**
     * @var App\Models\BoardItem
     */
    protected $tempBoardItem;

    /**
     * @var App\Models\Project
     */
    protected $project;

    /**
     * @var
     */
    protected $route;

    /**
     *
     */
    public function setUp()
    {
        parent::setUp();
        $this->project = factory(App\Models\Project::class)->create(['company_id'=>$this->company->id]);
        $this->project->team()->attach($this->user->id);
        $this->user = $this->project->team()->find($this->user->id);
        $this->boardItem = factory(App\Models\BoardItem::class)->create(['project_id'=>$this->project->id, 'created_by' => $this->user->id]);
        $this->route = "/projects/".$this->project->id."/board_items";
    }

    public function tearDown()
    {
        $this->project->team()->detach($this->user->id);
        $this->boardItem->forceDelete();
        $this->project->forceDelete();
        if ($this->tempBoardItem) {
            $this->tempBoardItem->forceDelete();
        }
        parent::tearDown();
    }


    /**
     * @return array
     */
    private function params()
    {
        return factory(App\Models\BoardItem::class)->make()->toArray();
    }

    public function test_it_should_list_all_board_items_in_project()
    {
        $this->authUserGet($this->route)
            ->seeApiSuccess()
            ->seeJson(['id'=>$this->boardItem->id])
            ->assertResponseOk();
    }

    public function test_it_should_not_allow_unauthorized_user_to_get_project()
    {
        $this->makeNotAllowedUser();
        $this->authUserGet($this->route)
            ->assertResponseStatus(403);
    }

    public function test_it_should_allow_user_to_create_board_item()
    {
        $this->expectsEvents(App\Events\BoardItem\BoardItemCreated::class);
        $params = $this->params();
        $this->authUserPost($this->route, $params)
            ->seeApiSuccess()
            ->seeJson(['title'=>$params['title']])
            ->seeJsonContains(['id' => $this->user->id, 'username' => $this->user->username, 'email' => $this->user->email])
            ->assertResponseStatus(201);
    }

    public function test_it_should_give_error_if_invalid_parameters()
    {
        $this->authUserPost($this->route, [])
            ->assertResponseStatus(422);
    }

    public function test_it_should_allow_unauthorized_user_to_create_board_item()
    {
        $this->makeNotAllowedUser();
        $params = $this->params();
        $this->authUserPost($this->route, $params)
            ->assertResponseStatus(403);
    }

    public function test_it_should_allow_user_with_board_write_permission_to_create_board_item()
    {
        $this->expectsEvents(App\Events\BoardItem\BoardItemCreated::class);
        $this->makeUserNonAdmin();
        $this->user->pivot->board_write = true;
        $this->user->pivot->save();
        $params = $this->params();
        $this->authUserPost($this->route, $params)
            ->seeApiSuccess()
            ->seeJson(['title' => $params['title']])
            ->seeJsonContains(['id' => $this->user->id, 'username' => $this->user->username, 'email' => $this->user->email])
            ->assertResponseStatus(201);
    }

    public function test_it_should_give_single_board_item_by_id()
    {
        $this->authUserGet($this->route."/".$this->boardItem->id)
            ->seeApiSuccess()
            ->seeJson(['id'=>$this->boardItem->id])
            ->assertResponseOk();
    }

    public function test_it_should_not_allow_unauthorized_user_to_get_single_board_item()
    {
        $this->makeNotAllowedUser();
        $this->authUserGet($this->route."/".$this->boardItem->id)
            ->assertResponseStatus(403);
    }

    public function test_it_should_allow_user_to_update_board_item()
    {
        $this->expectsEvents(\App\Events\BoardItem\BoardItemUpdated::class);
        $this->authUserPut($this->route."/".$this->boardItem->id, ['title' => 'updated'])
            ->seeApiSuccess()
            ->seeJson(['title'=>'updated'])
            ->seeJsonContains(['id' => $this->user->id, 'username' => $this->user->username, 'email' => $this->user->email])
            ->assertResponseOk();
    }

    public function test_it_should_allow_user_with_write_permission_to_update()
    {
        $this->expectsEvents(\App\Events\BoardItem\BoardItemUpdated::class);
        $this->makeNotAllowedUser();
        $this->user->pivot->update(['board_write' => true]);
        $this->authUserPut($this->route."/".$this->boardItem->id, ['title' => 'updated'])
            ->seeApiSuccess()
            ->seeJson(['title'=>'updated'])
            ->seeJsonContains(['id' => $this->user->id, 'username' => $this->user->username, 'email' => $this->user->email])
            ->assertResponseOk();
    }

    public function test_it_should_not_allow_unauthorized_user_to_update_board_item()
    {
        $this->makeNotAllowedUser();
        $this->authUserPut($this->route."/".$this->boardItem->id, ['title' => 'updated'])
            ->assertResponseStatus(403);
    }

    public function test_it_should_allow_authorized_user_to_delete_board_item()
    {
        $this->authUserDelete($this->route."/".$this->boardItem->id)
            ->assertResponseStatus(204);
    }

    public function test_it_should_allow_user_with_write_permission_to_delete()
    {
        $this->makeNotAllowedUser();
        $this->user->pivot->update(['board_write' => true]);
        $this->authUserDelete($this->route."/".$this->boardItem->id)
            ->assertResponseStatus(204);
    }

    public function test_it_should_not_allow_unauthorized_user_to_delete()
    {
        $this->makeNotAllowedUser();
        $this->authUserDelete($this->route."/".$this->boardItem->id)
            ->assertResponseStatus(403);
    }

    /**
     * @return mixed
     */
    private function makeNotAllowedUser()
    {
        $this->user->pivot->update(['board_read' => false, 'board_write' => false]);
        $this->user->designation = "designer";
        $this->user->save();
    }
}
