<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Task;

class SearchTaskTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_search_for_tasks()
    {
        $task1 = Task::factory()->create(['name' => 'Task 1']);
        $task2 = Task::factory()->create(['name' => 'Task 2']);
        $task3 = Task::factory()->create(['name' => 'Gino Dickens']);

        $response = $this->get('/tasks?search=Task');

        $response->assertStatus(200)
            ->assertSee('Task 1')
            ->assertSee('Task 2')
            ->assertDontSee('Gino Dickens');
    }
}
