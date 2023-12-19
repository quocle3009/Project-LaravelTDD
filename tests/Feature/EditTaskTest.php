<?php
//
//namespace Tests\Feature;
//
//use App\Models\Task;
//use App\Models\User;
//use Illuminate\Foundation\Testing\RefreshDatabase;
//use Illuminate\Foundation\Testing\WithFaker;
//use Tests\TestCase;
//
//class EditTaskTest extends TestCase
//{
//    use RefreshDatabase;
//
//    /** @test */
//    public function authenticated_user_can_edit_task()
//    {
//        $this->actingAs(User::factory()->create());
//        $task = Task::factory()->create();
//
//        // Sửa thông tin task
//        $editedTaskData = [
//            'name' => 'Edited Task Name',
//            'content' => 'Edited Task Content',
//        ];
//
//        $response = $this->put(route('tasks.update', ['task' => $task->id]), $editedTaskData);
//
//        $response->assertStatus(302);
//        $this->assertDatabaseHas('tasks', $editedTaskData);
//        $response->assertRedirect(route('tasks.index'));
//    }
//
//    /** @test */
//    public function authenticated_user_cannot_edit_task_if_name_field_is_null()
//    {
//        $this->actingAs(User::factory()->create());
//        $task = Task::factory()->create();
//
//        // Sửa thông tin task với trường name là null
//        $editedTaskData = [
//            'name' => null,
//            'content' => 'Edited Task Content',
//        ];
//
//        $response = $this->from(route('tasks.edit', ['task' => $task->id]))
//            ->put(route('tasks.update', ['task' => $task->id]), $editedTaskData);
//
//        $response->assertSessionHasErrors(['name']);
//    }
//
//    /** @test */
//    public function authenticated_user_can_view_edit_task_form()
//    {
//        $this->actingAs(User::factory()->create());
//        $task = Task::factory()->create();
//
//        $response = $this->get(route('tasks.edit', ['task' => $task->id]));
//
//        $response->assertViewIs('tasks.edit');
//        $response->assertViewHas('task', $task);
//    }
//
//    /** @test */
//    public function authenticated_user_can_see_name_required_text_if_validate_error()
//    {
//        $this->actingAs(User::factory()->create());
//        $task = Task::factory()->create();
//
//        // Sửa thông tin task với trường name là null để gây lỗi validate
//        $editedTaskData = [
//            'name' => null,
//            'content' => 'Edited Task Content',
//        ];
//
//        $response = $this->from(route('tasks.edit', ['task' => $task->id]))
//            ->put(route('tasks.update', ['task' => $task->id]), $editedTaskData);
//
//        $response->assertRedirect(route('tasks.edit', ['task' => $task->id]));
//    }
//}
