<?php
/**
* Author: sagar <sam.coolone70@gmail.com>
*
*/


class TicketsControllerTest extends TestCase
{
    /**
     * @var \App\Models\Ticket
     */
    protected $ticket;
    /**
     * @var
     */
    protected $route;

    /**
     * @var \App\Models\User
     */
    protected $tempUser;

    /**
     *
     */
    public function setUp()
    {
        parent::setUp();
        $this->project = factory(App\Models\Project::class)->create(['company_id'=>$this->company->id]);
        $this->project->team()->attach($this->user->id);
        $this->user = $this->project->team()->find($this->user->id);
        $this->ticket = factory(App\Models\Ticket::class)->create([
            'project_id'    =>    $this->project->id
        ]);
        $this->route = "/projects/".$this->project->id."/tickets";
    }

    public function tearDown()
    {
        $this->project->team()->detach($this->user->id);
        $this->ticket->forceDelete();
        $this->project->forceDelete();
        if($this->tempUser) {
            $this->project->team()->detach($this->tempUser->id);
            $this->tempUser->forceDelete();
        }
        parent::tearDown();
    }

    /**
     * @return array
     */
    private function params()
    {
        return [
            'title'    =>    $this->faker->sentence(5),
            'description'    =>    $this->faker->paragraph(5),
            'assigned_to'    =>    $this->user->id,
            'priority'    =>    'high'
        ];
    }

    /**
     * @return mixed
     */
    private function makeNotAllowedUser()
    {
        $this->user->pivot->update(['ticket_write' => false, 'ticket_read' => false]);
        $this->user->designation = "designer";
        $this->user->save();
    }

    public function test_it_should_list_all_tickets_belongs_to_project()
    {
        $this->authUserGet($this->route)
            ->seeApiSuccess()
            ->seeJson(['id'=>$this->ticket->id])
            ->assertResponseOk();
    }

    public function test_it_should_give_tickets_assigned_to_user_if_given()
    {
        $this->authUserGet($this->route."?assigned_to=".$this->user->id)
            ->seeApiSuccess()
            ->dontSeeJson(['id'=>$this->ticket->id])
            ->assertResponseOk();

        // to prevent event logging in for ticket meta assigned to
        $this->ticket->assigned_to = $this->user->id;
        $this->ticket->save();

        $this->authUserGet($this->route."?assigned_to=".$this->user->id)
            ->seeApiSuccess()
            ->seeJson(['id'=>$this->ticket->id])
            ->assertResponseOk();
    }

    public function it_should_allow_to_filter_tickets_with_status() {
        $this->authUserGet($this->route."?status=resolved")
            ->seeApiSuccess()
            ->dontSeeJson(['id'=>$this->ticket->id])
            ->assertResponseOk();

        $this->authUserGet($this->route."?status=new")
            ->seeApiSuccess()
            ->seeJson(['id'=>$this->ticket->id])
            ->assertResponseOk();
    }

    public function test_it_should_not_give_tickets_to_unauthorized_user()
    {
        $this->makeNotAllowedUser();
        $this->authUserGet($this->route)
            ->assertResponseStatus(403);
    }

    public function test_it_should_allow_authorized_user_to_create_ticket()
    {
        $this->expectsEvents(\App\Events\Ticket\TicketCreated::class);
        $params = $this->params();
        $this->authUserPost($this->route, $params)
            ->seeApiSuccess()
            ->seeJson(['title'=>$params['title'], 'description'=>$params['description']])
            ->seeJsonContains(['id' => $this->user->id, 'email' => $this->user->email, 'username' => $this->user->username])
            ->assertResponseStatus(201);
    }

    public function test_it_should_give_error_if_parameter_is_invalid() {
        $this->authUserPost($this->route)
            ->assertResponseStatus(422);
    }

    public function test_it_should_allow_user_with_write_permission_to_create() {
        $this->makeNotAllowedUser();
        $this->user->pivot->update(['ticket_write' => true]);
        $this->test_it_should_allow_authorized_user_to_create_ticket();
    }

    public function test_it_should_not_allow_unauthorized_user_to_create()
    {
        $this->makeNotAllowedUser();
        $params = $this->params();
        $this->authUserPost($this->route, $params)
            ->assertResponseStatus(403);
    }

    public function test_it_should_give_single_ticket_by_id()
    {
        $this->authUserGet($this->route."/".$this->ticket->sequence_id)
            ->seeApiSuccess()
            ->seeJson(['id'=>$this->ticket->id])
            ->assertResponseStatus(200);
    }

    public function test_it_should_not_allow_unauthorized_user_to_get_by_id()
    {
        $this->makeNotAllowedUser();
        $this->authUserGet($this->route."/".$this->ticket->sequence_id)
            ->assertResponseStatus(403);
    }

    public function test_it_should_allow_authorized_user_to_update()
    {
        $this->expectsEvents(\App\Events\Ticket\TicketUpdated::class);
        $this->authUserPut($this->route."/".$this->ticket->sequence_id, ['title' => 'updated'])
            ->seeApiSuccess()
            ->seeJson(['title'=>'updated'])
            ->assertResponseOk();

    }

    public function test_it_should_allow_user_with_write_permission_to_update() {
        $this->makeNotAllowedUser();
        $this->user->pivot->update(['ticket_write' => true]);
        $this->test_it_should_allow_authorized_user_to_update();
    }

    public function test_it_should_log_activity_on_status_change() {
        $this->expectsEvents(\App\Events\Ticket\TicketStatusChanged::class);
        $this->authUserPut($this->route."/".$this->ticket->sequence_id, ['status' => 'closed'])
            ->seeApiSuccess()
            ->seeJson(['status'=>'closed'])
            ->assertResponseOk();
    }

    public function test_it_should_log_activity_on_assignee_change() {
        $this->expectsEvents(\App\Events\Ticket\TicketAssigneeChanged::class);
        $this->tempUser = factory(\App\Models\User::class)->create(['company_id' => $this->user->id]);
        $this->project->team()->attach($this->tempUser->id);
        $this->authUserPut($this->route."/".$this->ticket->sequence_id, ['assigned_to' => $this->tempUser->id])
            ->seeApiSuccess()
            ->seeJson(['id' => $this->tempUser->id])
            ->assertResponseOk();
    }

    public function test_it_should_log_activity_on_status_change_and_assignee_change() {
        $this->expectsEvents(\App\Events\Ticket\TicketStatusChanged::class);
        $this->expectsEvents(\App\Events\Ticket\TicketAssigneeChanged::class);
        $this->tempUser = factory(\App\Models\User::class)->create(['company_id' => $this->user->id]);
        $this->project->team()->attach($this->tempUser->id);
        $this->authUserPut($this->route."/".$this->ticket->sequence_id,['assigned_to'=>$this->tempUser->id, 'status' => 'closed'])
            ->seeApiSuccess()
            ->seeJson(['id' => $this->tempUser->id])
            ->assertResponseOk();
    }

    public function test_it_should_not_allow_unauthorized_user_to_update()
    {
        $this->makeNotAllowedUser();
        $this->authUserPut($this->route."/".$this->ticket->sequence_id, ['title' => 'updated'])
            ->assertResponseStatus(403);
    }

    public function test_it_should_allow_authorized_user_to_delete()
    {
        $this->authUserDelete($this->route."/".$this->ticket->sequence_id)
            ->assertResponseStatus(204);
    }

    public function test_it_should_allow_user_with_write_permission_to_delete() {
        $this->makeNotAllowedUser();
        $this->user->pivot->update(['ticket_write' => true]);
        $this->test_it_should_allow_authorized_user_to_delete();
    }

    public function test_it_should_not_allow_unauthorized_user_to_delete()
    {
        $this->makeNotAllowedUser();
        $this->authUserDelete($this->route."/".$this->ticket->sequence_id)
            ->assertResponseStatus(403);
    }

    public function test_it_should_list_logs_for_single_ticket()
    {
//        $this->authUserGet($this->route."/".$this->ticket->id."/logs")
//            ->seeApiSuccess()
//            ->seeJsonContains(['id' => $this->user->id, 'username' => $this->user->username, 'email' => $this->user->email])
//            ->assertResponseOk();
//
//        $this->ticket->activity()->delete();
    }

    public function test_it_should_not_list_logs_for_single_ticket_unauthorized_user()
    {
//        $this->makeNotAllowedUser();
//        $this->authUserGet($this->route."/".$this->ticket->id."/logs")
//            ->assertResponseStatus(403);
//
//        $this->ticket->activity()->delete();
    }
}
