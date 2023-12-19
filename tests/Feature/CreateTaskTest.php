<?php

namespace Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateTaskTest extends TestCase
{
    /** @test */
    public function authenticated_user_can_new_task()
    {
        $this->actingAs(User::factory()->create());
        $task = Task::factory()->make()->toArray();
        $response = $this->post($this->getCreateTaskRoute(), $task);

        $response->assertStatus(302);
        $this->assertDatabaseHas('tasks', $task);
        $response->assertRedirect(route('tasks.index'));
    }

    /** @test */
    public function unauthenticated_user_can_not_new_task()
    {
        $task = Task::factory()->make()->toArray();
        $response = $this->post($this->getCreateTaskRoute(), $task);
        $response->assertRedirect('/login');
    }


    /** @test */
    public function authenticated_user_can_not_create_task_if_name_field_is_null(){
        $this->actingAs(User::factory()->create());
        $task = Task::factory()->make(['name'=>null])->toArray();
        $response = $this->post($this->getCreateTaskRoute(), $task);
        $response->assertSessionHasErrors(['name']);
    }
    /** @test */
    public function authenticated_user_can_view_create_task_form()
    {
        $this->actingAs(User::factory()->create());
        $response = $this->get($this->getCreatetaskViewRoute());
        $response->assertViewIs('tasks.create');
    }

    /** @test */
    public function authenticated_user_can_see_name_required_text_if_validate_error()
    {
        $this->actingAs(User::factory()->create());
        $task = Task::factory()->make(['name'=>null])->toArray();
        $response = $this->from($this->getCreatetaskViewRoute())->post($this->getCreateTaskRoute(), $task);
        $response->assertRedirect($this->getCreatetaskViewRoute());
    }

    /** @test */
    public function authenticated_user_can_not_see_create_task_form_view()
    {
        $response = $this->get($this->getCreatetaskViewRoute());
        $response->assertRedirect('/login');
    }
    public function getCreateTaskRoute()
    {

        return route('tasks.store');
    }
    public function getCreatetaskViewRoute()
    {
        return route('tasks.create');
    }
}
