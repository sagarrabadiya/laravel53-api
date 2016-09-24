<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */


class CommentsControllerTest extends TestCase
{
	/**
	 * @var
	 */
	protected $boardRoute;
	/**
	 * @var \App\Models\BoardItem
	 */
	protected $boardItem;
	/**
	 * @var \App\Models\Comment
	 */
	protected $comment;
	/**
	 * @var
	 */
	protected $ticketRoute;
	/**
	 * @var \App\Models\Ticket
	 */
	protected $ticket;

	/**
	 *
	 */
	public function setUp()
	{
		parent::setUp();
		$this->project = factory(App\Models\Project::class)->create(['company_id'=>$this->company->id]);
		$this->project->team()->attach($this->user->id);
        $this->user = $this->project->team()->find($this->user->id);
	}

	/**
	 *
	 */
	private function prepareBoardItem() {
		$this->boardItem = factory(App\Models\BoardItem::class)->create(['project_id'=>$this->project->id,'created_by'=>$this->user->id]);
		$this->boardRoute = "/projects/".$this->project->id."/board_items/".$this->boardItem->id."/comments";
		$this->comment = $this->boardItem->comments()->save(factory(\App\Models\Comment::class)->make());
	}

	public function tearDown()
	{
		if($this->boardItem)
			$this->boardItem->forceDelete();
		if($this->ticket)
			$this->ticket->forceDelete();
		$this->project->team()->detach($this->user->id);
		$this->comment->forceDelete();
		parent::tearDown();
	}

	/**
	 *
	 */
	private function prepareTicket() {
		$this->ticket = factory(App\Models\Ticket::class)->create([
			'project_id'	=>	$this->project->id
		]);
		$this->ticketRoute = "/projects/".$this->project->id."/tickets/".$this->ticket->sequence_id."/comments";
		$this->comment = $this->ticket->comments()->save(factory(\App\Models\Comment::class)->make());
	}

	/**
	 * @return mixed
	 */
	private function makeNotAllowedUser() {
        $this->user->pivot->update([
            'board_read' => false,
            'board_write' => false,
            'ticket_read' => false,
            'ticket_write' => false
        ]);
        $this->user->designation = 'manager';
        $this->user->save();
	}

	/**
	 * @return array
	 */
	private function params() {
		return [
			'text'	=>	$this->faker->paragraph(5),
			'created_by'	=>	$this->user->id
		];
	}

	public function test_it_should_list_comments_for_board_item() {
		$this->prepareBoardItem();
		$this->authUserGet($this->boardRoute)
			->seeApiSuccess()
			->seeJson(['id'=>$this->comment->id])
        	->assertResponseOk();
	}

    public function test_it_should_list_comment_of_board_item_for_user_with_read_permission() {
        $this->prepareBoardItem();
        $this->makeNotAllowedUser();
        $this->user->pivot->update(['board_read' => true]);
        $this->test_it_should_list_comments_for_board_item();
    }

	public function test_it_should_not_list_comment_for_unauthorized_user() {
		$this->prepareBoardItem();
		$this->makeNotAllowedUser();
		$this->authUserGet($this->boardRoute)
			->assertResponseStatus(403);
	}

	public function test_it_should_allow_user_to_store_comment_for_board_item() {
	    $this->expectsEvents(\App\Events\Comment\CommentCreated::class);
		$this->prepareBoardItem();
		$params = $this->params();
		$this->authUserPost($this->boardRoute,$params)
			->seeApiSuccess()
			->seeJson(['text'=>$params['text']])
        	->seeJsonContains(['id' => $this->user->id, 'email' => $this->user->email])
        	->assertResponseStatus(201);
	}

    public function test_it_should_allow_user_with_write_permission_to_create_comment_board_item() {
        $this->makeNotAllowedUser();
        $this->user->pivot->update(['board_write' => true]);
        $this->test_it_should_allow_user_to_store_comment_for_board_item();
    }

	public function test_it_should_not_allow_unauthorized_user_to_add_comment_for_board_item() {
		$this->prepareBoardItem();
		$this->makeNotAllowedUser();
		$params = $this->params();
		$this->authUserPost($this->boardRoute, $params)
			->assertResponseStatus(403);
	}

	public function test_it_should_list_single_comment_for_board_item() {
		$this->prepareBoardItem();
		$this->authUserGet($this->boardRoute."/".$this->comment->id)
			->seeApiSuccess()
			->seeJson(['text'=>$this->comment->text])
        	->assertResponseOk();
	}

    public function test_it_should_list_single_comment_for_board_item_for_user_with_read_permission() {
        $this->makeNotAllowedUser();
        $this->user->pivot->update(['board_read' => true]);
        $this->test_it_should_list_single_comment_for_board_item();
    }


	public function test_it_should_not_list_single_comment_for_unauthorized_user() {
		$this->prepareBoardItem();
		$this->makeNotAllowedUser();
		$this->authUserGet($this->boardRoute."/".$this->comment->id)
			->assertResponseStatus(403);
	}

	public function test_it_should_allow_user_to_delete_comment_for_board_item() {
		$this->prepareBoardItem();
		$this->authUserDelete($this->boardRoute."/".$this->comment->id)
			->assertResponseStatus(204);
	}

    public function test_it_should_allow_user_with_write_permission_to_delete_comment_for_board_item() {
        $this->makeNotAllowedUser();
        $this->user->pivot->update(['board_write' => true]);
        $this->test_it_should_allow_user_to_delete_comment_for_board_item();
    }

	public function test_it_should_not_allow_unauthorized_user_to_delete_comment_for_board_item() {
		$this->prepareBoardItem();
		$this->makeNotAllowedUser();
		$this->authUserDelete($this->boardRoute."/".$this->comment->id)
			->assertResponseStatus(403);
	}


    /*****
     *  all the test cases for comment with ticket resources
     */

    public function test_it_should_list_comments_for_ticket() {
        $this->prepareTicket();
		$this->authUserGet($this->ticketRoute)
			->seeApiSuccess()
			->seeJson(['id' => $this->comment->id, 'text' => $this->comment->text])
            ->assertResponseOk();
    }

    public function test_it_should_list_comments_for_ticket_with_user_read_permission() {
        $this->makeNotAllowedUser();
        $this->user->pivot->update(['ticket_read' => true]);
        $this->test_it_should_list_comments_for_ticket();
    }

	public function test_it_should_not_list_comments_for_ticket_to_unauthorized_user() {
		$this->prepareTicket();
		$this->makeNotAllowedUser();
		$this->authUserGet($this->ticketRoute)
			->assertResponseStatus(403);
	}

	public function test_it_should_create_comment_for_ticket() {
	    $this->expectsEvents(\App\Events\Comment\CommentCreated::class);
		$this->prepareTicket();
		$params = $this->params();
		$this->authUserPost($this->ticketRoute, $params)
			->seeApiSuccess()
			->seeJson(['text'=>$params['text']])
        	->seeJson(['id' => $this->user->id, 'username' => $this->user->username, 'email' => $this->user->email])
        	->assertResponseStatus(201);
	}

	public function test_it_should_allow_user_with_write_permission_to_create_comment_for_ticket() {
		$this->makeNotAllowedUser();
		$this->user->pivot->update(['ticket_write' => true]);
		$this->test_it_should_create_comment_for_ticket();
	}

	public function test_it_should_not_allow_unauthorized_user_to_create_comment_for_ticket() {
		$this->prepareTicket();
		$this->makeNotAllowedUser();
		$params = $this->params();
		$this->authUserPost($this->ticketRoute, $params)
			->assertResponseStatus(403);
	}

	public function test_it_should_list_single_comment_for_ticket() {
		$this->prepareTicket();
		$this->authUserGet($this->ticketRoute."/".$this->comment->id)
			->seeApiSuccess()
			->seeJson(['id'=>$this->comment->id])
			->assertResponseOk();
	}

	public function test_it_should_list_single_comment_for_ticket_with_read_permission() {
		$this->makeNotAllowedUser();
		$this->user->pivot->update(['ticket_read' => true]);
		$this->test_it_should_list_single_comment_for_ticket();
	}

	public function test_it_should_not_list_comment_for_ticket_for_unauthorized_user() {
		$this->prepareTicket();
		$this->makeNotAllowedUser();
		$this->authUserGet($this->ticketRoute."/".$this->comment->id)
			->assertResponseStatus(403);
	}

	public function test_it_should_allow_to_delete_comment_for_ticket() {
		$this->prepareTicket();
		$this->authUserDelete($this->ticketRoute."/".$this->comment->id)
			->assertResponseStatus(204);
	}

	public function test_it_should_allow_user_with_write_permission_to_delete_comment_for_ticket() {
		$this->makeNotAllowedUser();
		$this->user->pivot->update(['ticket_write' => true]);
		$this->test_it_should_allow_to_delete_comment_for_ticket();
	}

	public function test_it_should_not_allow_unauthorized_user_to_delete_comment_for_ticket() {
		$this->prepareTicket();
		$this->makeNotAllowedUser();
		$this->authUserDelete($this->ticketRoute."/".$this->comment->id)
			->assertResponseStatus(403);
	}
}
