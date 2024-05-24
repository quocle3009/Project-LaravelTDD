<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EditTaskTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function authenticated_user_can_edit_task()
    {
        $this->actingAs(User::factory()->create());
        $task = Task::factory()->create();
        $response = $this->put($this->getEditTaskRoute($task->id), $task->toArray());

        $response->assertStatus(302);
        $this->assertDatabaseHas('tasks', $task->toArray());
        $response->assertRedirect(route('tasks.index'));
    }

    /** @test */
    public function unauthenticated_user_can_not_edit_task()
    {
        $task = Task::factory()->create();
        $response = $this->put($this->getEditTaskRoute($task->id), $task->toArray());
        $response->assertRedirect('/login');
    }

    // /** @test */
    // public function authenticated_user_can_not_edit_task_form_if_field_is_null()
    // {
    //     $this->actingAs(User::factory()->create());
    //     $task = Task::factory()->create();

    //     $response = $this->put($this->getEditTaskRoute($task->id), $data);

    //     $response->assertViewIs('tasks.edit');
    //     $response->assertViewHas('task', $task);
    // }

    /** @test */
    public function authenticated_user_can_not_edit_task_form_if_field_is_null()
    {
        $this->actingAs(User::factory()->create());
        $task = Task::factory()->create();
        $data = [
            'name' => null,
            'content' => $task->content
        ];

        $response = $this->put($this->getEditTaskRoute($task->id), $data);
        $response->assertSessionHasErrors(['name']);
    }
    public function getEditTaskRoute($id)
    {
        return route('tasks.update', $id);
    }
}
