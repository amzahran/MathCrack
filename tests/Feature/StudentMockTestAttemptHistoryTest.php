<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Level;
use App\Models\StudentTest;
use App\Models\Test;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Database\Schema\Blueprint;
use Tests\TestCase;

class StudentMockTestAttemptHistoryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);

        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $this->withoutMiddleware();
        $this->createSchema();
        $this->shareLayoutData();
    }

    public function test_student_with_no_remaining_attempts_still_sees_completed_attempt_history(): void
    {
        $level = Level::create(['name' => 'Digital SAT']);
        $course = Course::create([
            'name' => 'Digital SAT Course',
            'level_id' => $level->id,
            'price' => 0,
        ]);
        $test = Test::create([
            'name' => 'Mock Test 9',
            'course_id' => $course->id,
            'price' => 0,
            'total_score' => 800,
            'initial_score' => 200,
            'default_question_score' => 15,
            'part1_questions_count' => 0,
            'part1_time_minutes' => 35,
            'part2_questions_count' => 0,
            'part2_time_minutes' => 35,
            'break_time_minutes' => 10,
            'max_attempts' => 1,
            'is_active' => true,
        ]);
        $student = User::factory()->create(['level_id' => $level->id]);

        StudentTest::create([
            'student_id' => $student->id,
            'test_id' => $test->id,
            'attempt_number' => 1,
            'status' => 'completed',
            'current_score' => 200,
            'final_score' => 200,
            'completed_at' => now(),
        ]);

        $response = $this->actingAs($student)->get("/dashboard/users/tests/{$test->id}");

        $response->assertOk();
        $response->assertSee('No More Attempts Available');
        $response->assertSee('Previous Attempts');
        $response->assertSee('View Details');
    }

    public function test_student_cannot_open_test_from_another_level(): void
    {
        $studentLevel = Level::create(['name' => 'Digital SAT']);
        $otherLevel = Level::create(['name' => 'EST I']);
        $otherCourse = Course::create([
            'name' => 'EST I Course',
            'level_id' => $otherLevel->id,
            'price' => 0,
        ]);
        $otherTest = Test::create([
            'name' => 'EST Mock Test',
            'course_id' => $otherCourse->id,
            'price' => 0,
            'total_score' => 800,
            'initial_score' => 200,
            'default_question_score' => 15,
            'part1_questions_count' => 0,
            'part1_time_minutes' => 35,
            'part2_questions_count' => 0,
            'part2_time_minutes' => 35,
            'break_time_minutes' => 10,
            'max_attempts' => 1,
            'is_active' => true,
        ]);
        $student = User::factory()->create(['level_id' => $studentLevel->id]);

        $response = $this->actingAs($student)->get("/dashboard/users/tests/{$otherTest->id}");

        $response->assertRedirect(route('dashboard.users.tests.index'));
        $response->assertSessionHas('error', 'You are not allowed to access this test');
    }

    private function createSchema(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('firstname');
            $table->string('lastname');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->unsignedBigInteger('level_id')->nullable();
            $table->timestamps();
        });

        Schema::create('levels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('level_id');
            $table->string('price')->nullable();
            $table->string('tests_price')->nullable();
            $table->integer('access_duration_days')->default(90);
            $table->string('track_slug', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('tests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('course_id');
            $table->decimal('price', 8, 2)->default(0);
            $table->integer('total_score')->default(800);
            $table->integer('initial_score')->default(200);
            $table->integer('default_question_score')->default(15);
            $table->integer('part1_questions_count')->default(0);
            $table->integer('part1_time_minutes')->default(35);
            $table->integer('part2_questions_count')->default(0);
            $table->integer('part2_time_minutes')->default(35);
            $table->integer('part3_questions_count')->nullable();
            $table->integer('part3_time_minutes')->nullable();
            $table->integer('part4_questions_count')->nullable();
            $table->integer('part4_time_minutes')->nullable();
            $table->integer('part5_questions_count')->nullable();
            $table->integer('part5_time_minutes')->nullable();
            $table->integer('break_time_minutes')->default(10);
            $table->unsignedInteger('max_attempts')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('student_tests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('test_id');
            $table->integer('attempt_number')->default(1);
            $table->string('status')->default('not_started');
            $table->decimal('current_score', 8, 2)->default(200);
            $table->decimal('final_score', 8, 2)->nullable();
            $table->unsignedTinyInteger('current_module')->default(1);
            $table->timestamp('current_module_started_at')->nullable();
            $table->integer('remaining_seconds')->nullable();
            $table->boolean('is_paused')->default(false);
            $table->timestamp('paused_at')->nullable();
            $table->integer('paused_seconds')->nullable();
            $table->json('progress_data')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('part1_started_at')->nullable();
            $table->timestamp('part1_ended_at')->nullable();
            $table->timestamp('break_started_at')->nullable();
            $table->timestamp('part2_started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->integer('time_spent_part1')->nullable();
            $table->integer('time_spent_part2')->nullable();
            $table->timestamps();
        });

        Schema::create('test_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('test_id');
            $table->text('question_text');
            $table->string('question_image')->nullable();
            $table->string('type')->default('mcq');
            $table->string('part')->default('part1');
            $table->integer('question_order')->default(1);
            $table->integer('score')->default(15);
            $table->text('correct_answer')->nullable();
            $table->timestamps();
        });

        Schema::create('student_test_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_test_id');
            $table->unsignedBigInteger('test_question_id');
            $table->unsignedBigInteger('selected_option_id')->nullable();
            $table->text('answer_text')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->decimal('score_earned', 8, 2)->default(0);
            $table->timestamp('answered_at')->nullable();
            $table->timestamps();
        });
    }

    private function shareLayoutData(): void
    {
        View::share([
            'settings' => [
                'author' => 'MathCrack',
                'favicon' => 'favicon.ico',
                'headerCode' => '',
                'logo' => 'logo.png',
                'footerCode' => '',
            ],
            'headerLanguages' => collect(),
            'headerCurrencies' => collect(),
            'defaultLanguage' => null,
            'defaultCurrency' => null,
        ]);
    }
}
