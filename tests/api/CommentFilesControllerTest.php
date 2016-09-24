<?php

/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */
class CommentFilesController extends TestCase
{
    /**
     * @var \App\Models\File
     */
    protected $file;
    /**
     * @var \App\Models\Ticket
     */
    protected $ticket;
    /**
     * @var \App\Models\BoardItem
     */
    protected $boardItem;

    /**
     * @var \App\Models\Comment
     */
    protected $comment;


    protected $boardRoute;


    protected $ticketRoute;

    /**
     *
     */
    public function setUp()
    {
        parent::setUp();

        $this->project = factory(App\Models\Project::class)->create(['company_id' => $this->company->id]);
        $this->project->team()->attach($this->user->id);
        $this->user = $this->project->team()->find($this->user->id);
        $this->ticket = factory(App\Models\Ticket::class)->create([
            'project_id' => $this->project->id,
            'created_by' => $this->user->id,
            'assigned_by' => $this->user->id
        ]);
        $this->boardItem = factory(App\Models\BoardItem::class)->create(['project_id' => $this->project->id, 'created_by' => $this->user->id]);
        $this->file = $this->company->files()->save(new App\Models\File(['name' => 'sample.png', 'salt' => str_random(), 'ext' => 'png', 'created_by' => $this->user->id, 'company_id' => $this->company->id]));
        $this->file->is_orphan = true;
        $this->file->save();
        $this->ticketRoute = "/projects/{$this->project->id}/tickets/{$this->ticket->sequence_id}/files";
        $this->boardRoute = "/projects/{$this->project->id}/board_items/{$this->boardItem->id}/files";
        $this->comment = factory(\App\Models\Comment::class)->create(['created_by' => $this->user->id]);
    }

    public function tearDown()
    {
        $this->project->team()->detach($this->user->id);
        $this->user->forceDelete();
        $this->project->forceDelete();
        if ($this->ticket)
            $this->ticket->forceDelete();
        if ($this->boardItem)
            $this->boardItem->forceDelete();
        if ($this->comment)
            $this->comment->forceDelete();
        parent::tearDown();
    }

    /**
     * @return mixed
     */
    private function makeNotAllowedUser()
    {
        $this->user->pivot->update(['ticket_read' => false, 'ticket_write' => false, 'board_read' => false, 'board_write' => false]);
        $this->user->designation = "designer";
        $this->user->save();
    }

    /************* All comments related files route for board and ticket ************/
    /** get listing routes */
    public function test_it_should_list_all_the_files_belongs_to_ticket_comment()
    {
        $this->ticket->comments()->save($this->comment);
        $this->comment->files()->save($this->file);
        $this->authUserGet("/projects/{$this->project->id}/tickets/{$this->ticket->sequence_id}/comments/{$this->comment->id}/files")
            ->seeApiSuccess()
            ->seeJson(['salt' => $this->file->salt])
            ->assertResponseOk();
    }

    public function test_it_should_list_all_the_files_to_read_permission_ticket_comment()
    {
        $this->makeNotAllowedUser();
        $this->user->pivot->update(['ticket_read' => true]);
        $this->test_it_should_list_all_the_files_belongs_to_ticket_comment();
    }

    public function test_it_should_not_list_files_to_ticket_comment_if_unauthorized_user()
    {
        $this->ticket->comments()->save($this->comment);
        $this->makeNotAllowedUser();
        $this->authUserGet("/projects/{$this->project->id}/tickets/{$this->ticket->sequence_id}/comments/{$this->comment->id}/files")
            ->assertResponseStatus(403);
    }

    /** post create routes */
    public function test_it_should_allow_user_to_attach_file_to_ticket_comment()
    {
        $this->ticket->comments()->save($this->comment);
        $this->authUserPost("/projects/{$this->project->id}/tickets/{$this->ticket->sequence_id}/comments/{$this->comment->id}/files", $this->params())
            ->seeApiSuccess()
            ->seeJson(['salt' => $this->file->salt ])
            ->seeJsonContains(['id' => $this->user->id,'email' => $this->user->email])
            ->assertResponseStatus(201);
    }

    public function test_it_should_allow_user_with_write_permission_to_add_file_to_ticket_comment()
    {
        $this->makeNotAllowedUser();
        $this->user->pivot->update(['ticket_write' => true]);
        $this->test_it_should_allow_user_to_attach_file_to_ticket_comment();
    }

    public function test_it_should_not_allow_unauthorized_user_to_add_file_to_ticket_comment() {
        $this->ticket->comments()->save($this->comment);
        $this->makeNotAllowedUser();
        $this->authUserPost("/projects/{$this->project->id}/tickets/{$this->ticket->sequence_id}/comments/{$this->comment->id}/files", $this->params())
            ->assertResponseStatus(403);
    }

    /** get single resource routes */
    public function test_it_should_allow_user_to_get_single_file_for_ticket_comment()
    {
        $this->ticket->comments()->save($this->comment);
        $this->comment->files()->save($this->file);
        $this->authUserGet("/projects/{$this->project->id}/tickets/{$this->ticket->sequence_id}/comments/{$this->comment->id}/files/{$this->file->salt}")
            ->seeApiSuccess()
            ->seeJson(['salt' => $this->file->salt])
            ->assertResponseOk();
    }

    public function test_it_should_allow_user_with_read_permission_to_get_single_file_for_ticket_comment()
    {
        $this->makeNotAllowedUser();
        $this->user->pivot->update(['ticket_read' => true]);
        $this->test_it_should_allow_user_to_get_single_file_for_ticket_comment();
    }

    public function test_it_should_not_allow_unauthorized_user_to_get_single_file_for_ticket_comment()
    {
        $this->ticket->comments()->save($this->comment);
        $this->comment->files()->save($this->file);
        $this->makeNotAllowedUser();
        $this->authUserGet("/projects/{$this->project->id}/tickets/{$this->ticket->sequence_id}/comments/{$this->comment->id}/files/{$this->file->salt}")
            ->assertResponseStatus(403);
    }

    /** delete single resource routes */
    public function test_it_should_allow_user_to_delete_file_from_ticket_comment()
    {
        $this->ticket->comments()->save($this->comment);
        $this->comment->files()->save($this->file);
        $this->authUserDelete("/projects/{$this->project->id}/tickets/{$this->ticket->sequence_id}/comments/{$this->comment->id}/files/{$this->file->salt}")
            ->assertResponseStatus(204);
    }

    public function test_it_should_allow_user_with_write_permission_to_delete_file_from_ticket_comment()
    {
        $this->makeNotAllowedUser();
        $this->user->pivot->update(['ticket_write' => true]);
        $this->test_it_should_allow_user_to_delete_file_from_ticket_comment();
    }

    public function test_it_should_not_allow_unauthorized_user_to_delete_file_from_ticket_comment()
    {
        $this->ticket->comments()->save($this->comment);
        $this->comment->files()->save($this->file);
        $this->makeNotAllowedUser();
        $this->authUserDelete("/projects/{$this->project->id}/tickets/{$this->ticket->sequence_id}/comments/{$this->comment->id}/files/{$this->file->salt}")
            ->assertResponseStatus(403);
    }


    /** comment board item routes */
    /** get listing routes */
    public function test_it_should_list_all_the_files_belongs_to_board_comment()
    {
        $this->boardItem->comments()->save($this->comment);
        $this->comment->files()->save($this->file);
        $this->authUserGet("/projects/{$this->project->id}/board_items/{$this->boardItem->id}/comments/{$this->comment->id}/files")
            ->seeApiSuccess()
            ->seeJson(['salt' => $this->file->salt])
            ->assertResponseOk();
    }

    public function test_it_should_list_all_the_files_to_read_permission_board_comment()
    {
        $this->makeNotAllowedUser();
        $this->user->pivot->update(['board_read' => true]);
        $this->test_it_should_list_all_the_files_belongs_to_board_comment();
    }

    public function test_it_should_not_list_files_to_board_comment_if_unauthorized_user()
    {
        $this->makeNotAllowedUser();
        $this->boardItem->comments()->save($this->comment);
        $this->authUserGet("/projects/{$this->project->id}/board_items/{$this->boardItem->id}/comments/{$this->comment->id}/files")
            ->assertResponseStatus(403);
    }

    /** post create routes */
    public function test_it_should_allow_user_to_attach_file_to_board_comment()
    {
        $this->boardItem->comments()->save($this->comment);
        $this->authUserPost("/projects/{$this->project->id}/board_items/{$this->boardItem->id}/comments/{$this->comment->id}/files",$this->params())
            ->seeApiSuccess()
            ->seeJson(['salt' => $this->file->salt ])
            ->seeJsonContains(['id' => $this->user->id,'email' => $this->user->email])
            ->assertResponseStatus(201);
    }

    public function test_it_should_allow_user_with_write_permission_to_add_file_to_board_comment()
    {
        $this->makeNotAllowedUser();
        $this->user->pivot->update(['board_write' => true]);
        $this->test_it_should_allow_user_to_attach_file_to_board_comment();
    }

    public function test_it_should_not_allow_unauthorized_user_to_add_file_to_board_comment() {
        $this->makeNotAllowedUser();
        $this->boardItem->comments()->save($this->comment);
        $this->authUserPost("/projects/{$this->project->id}/board_items/{$this->boardItem->id}/comments/{$this->comment->id}/files", $this->params())
            ->assertResponseStatus(403);
    }

    /** get single resource routes */
    public function test_it_should_allow_user_to_get_single_file_for_board_comment()
    {
        $this->boardItem->comments()->save($this->comment);
        $this->comment->files()->save($this->file);
        $this->authUserGet("/projects/{$this->project->id}/board_items/{$this->boardItem->id}/comments/{$this->comment->id}/files/{$this->file->salt}")
            ->seeApiSuccess()
            ->seeJson(['salt' => $this->file->salt])
            ->assertResponseOk();
    }

    public function test_it_should_allow_user_with_read_permission_to_get_single_file_for_board_comment()
    {
        $this->makeNotAllowedUser();
        $this->user->pivot->update(['board_read' => true]);
        $this->test_it_should_allow_user_to_get_single_file_for_board_comment();
    }

    public function test_it_should_not_allow_unauthorized_user_to_get_single_file_for_board_comment()
    {
        $this->boardItem->comments()->save($this->comment);
        $this->comment->files()->save($this->file);
        $this->makeNotAllowedUser();
        $this->authUserGet("/projects/{$this->project->id}/board_items/{$this->boardItem->id}/comments/{$this->comment->id}/files/{$this->file->salt}")
            ->assertResponseStatus(403);
    }

    /** delete single resource routes */
    public function test_it_should_allow_user_to_delete_file_from_board_comment()
    {
        $this->boardItem->comments()->save($this->comment);
        $this->comment->files()->save($this->file);
        $this->authUserDelete("/projects/{$this->project->id}/board_items/{$this->boardItem->id}/comments/{$this->comment->id}/files/{$this->file->salt}")
            ->assertResponseStatus(204);
    }

    public function test_it_should_allow_user_with_write_permission_to_delete_file_from_board_comment()
    {
        $this->makeNotAllowedUser();
        $this->user->pivot->update(['board_write' => true]);
        $this->test_it_should_allow_user_to_delete_file_from_board_comment();
    }

    public function test_it_should_not_allow_unauthorized_user_to_delete_file_from_board_comment()
    {
        $this->boardItem->comments()->save($this->comment);
        $this->comment->files()->save($this->file);
        $this->makeNotAllowedUser();
        $this->authUserDelete("/projects/{$this->project->id}/board_items/{$this->boardItem->id}/comments/{$this->comment->id}/files/{$this->file->salt}")
            ->assertResponseStatus(403);
    }


    public function it_should_allow_user_to_upload_public_image_as_file()
    {
        $mockLocalStorage = Mockery::mock(\Illuminate\Filesystem\FilesystemAdapter::class);
        $mockLocalStorage->shouldReceive('writeStream')->once()->withAnyArgs()->andReturn(true);
        Storage::shouldReceive('disk')->once()->andReturn($mockLocalStorage);
        $file = new \Symfony\Component\HttpFoundation\File\UploadedFile(base_path('tests/test_files/sample.png'), 'sample.png');
        $server = ['HTTP_Authorization' => 'Bearer '.$this->token];
        $this->call("POST", '/api/companies/' . $this->company->id . "/public_image", [], [], ['file' => $file], $server);
        $this->assertResponseStatus(201);
    }

    public function test_it_should_allow_user_to_download_the_file_for_ticket_comment()
    {
        $this->ticket->comments()->save($this->comment);
        $this->comment->files()->save($this->file);
        $this->authUserGet("/projects/{$this->project->id}/tickets/{$this->ticket->sequence_id}/comments/{$this->comment->id}/files/{$this->file->salt}?download=true")
            ->assertInstanceOf(\Symfony\Component\HttpFoundation\StreamedResponse::class, $this->response);
    }

    public function test_it_should_not_allow_unauthorized_user_to_download_the_file_for_ticket_comment()
    {
        $this->makeNotAllowedUser();
        $this->ticket->comments()->save($this->comment);
        $this->comment->files()->save($this->file);
        $this->authUserGet("/projects/{$this->project->id}/tickets/{$this->ticket->sequence_id}/comments/{$this->comment->id}/files/{$this->file->salt}?download=true")
            ->assertResponseStatus(403);
    }

    public function test_it_should_allow_user_to_download_the_file_for_board_comment()
    {
        $this->boardItem->comments()->save($this->comment);
        $this->comment->files()->save($this->file);
        $this->authUserGet("/projects/{$this->project->id}/board_items/{$this->boardItem->id}/comments/{$this->comment->id}/files/{$this->file->salt}?download=true")
            ->assertInstanceOf(\Symfony\Component\HttpFoundation\StreamedResponse::class, $this->response);
    }

    public function test_it_should_not_allow_unauthorized_user_to_download_the_file_for_board_comment()
    {
        $this->makeNotAllowedUser();
        $this->boardItem->comments()->save($this->comment);
        $this->comment->files()->save($this->file);
        $this->authUserGet("/projects/{$this->project->id}/board_items/{$this->boardItem->id}/comments/{$this->comment->id}/files/{$this->file->salt}?download=true")
            ->assertResponseStatus(403);
    }

    public function test_it_should_allow_user_to_read_the_file_for_ticket_comment()
    {
        $mockLocalStorage = Mockery::mock(\Illuminate\Filesystem\FilesystemAdapter::class);
        $mockLocalStorage->shouldReceive('get')->once()->withAnyArgs()->andReturn(file_get_contents(base_path('tests/test_files/sample.png')));
        Storage::shouldReceive('disk')->once()->andReturn($mockLocalStorage);
        $this->ticket->comments()->save($this->comment);
        $this->comment->files()->save($this->file);
        $this->authUserGet("/projects/{$this->project->id}/tickets/{$this->ticket->sequence_id}/comments/{$this->comment->id}/files/{$this->file->salt}?read=true")
            ->assertEquals(file_get_contents(base_path('tests/test_files/sample.png')),$this->response->getContent());
    }

    public function test_it_should_allow_user_to_read_the_file_for_board_comment()
    {
        $mockLocalStorage = Mockery::mock(\Illuminate\Filesystem\FilesystemAdapter::class);
        $mockLocalStorage->shouldReceive('get')->once()->withAnyArgs()->andReturn(file_get_contents(base_path('tests/test_files/sample.png')));
        Storage::shouldReceive('disk')->once()->andReturn($mockLocalStorage);
        $this->boardItem->comments()->save($this->comment);
        $this->comment->files()->save($this->file);
        $this->authUserGet("/projects/{$this->project->id}/board_items/{$this->boardItem->id}/comments/{$this->comment->id}/files/{$this->file->salt}?read=true")
            ->assertEquals(file_get_contents(base_path('tests/test_files/sample.png')),$this->response->getContent());
    }

    public function test_it_should_not_allow_unauthorized_user_to_read_the_file_for_ticket_comment()
    {
        $this->makeNotAllowedUser();
        $this->ticket->comments()->save($this->comment);
        $this->comment->files()->save($this->file);
        $this->authUserGet("/projects/{$this->project->id}/tickets/{$this->ticket->sequence_id}/comments/{$this->comment->id}/files/{$this->file->salt}?read=true")
            ->assertResponseStatus(403);
    }

    public function test_it_should_not_allow_unauthorized_user_to_read_the_file_for_board_comment()
    {
        $this->makeNotAllowedUser();
        $this->boardItem->comments()->save($this->comment);
        $this->comment->files()->save($this->file);
        $this->authUserGet("/projects/{$this->project->id}/board_items/{$this->boardItem->id}/comments/{$this->comment->id}/files/{$this->file->salt}?read=true")
            ->assertResponseStatus(403);
    }


    /**
     * @return array
     */
    public function params()
    {
        $parameters = ['id' => $this->file->id, 'salt' => $this->file->salt, 'name' => $this->file->name];
        return $parameters;
    }

}