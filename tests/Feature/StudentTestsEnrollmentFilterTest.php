<?php

namespace Tests\Feature;

use App\Http\Controllers\Web\Back\Users\Tests\TestsController;
use App\Models\Course;
use App\Models\Level;
use App\Models\Test;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class StudentTestsEnrollmentFilterTest extends TestCase
{
    private User $student;
    private Level $digitalSat;
    private Level $est;
    private Course $digitalSatCourse;
    private Course $estCourse;
    private Test $digitalSatTest;
    private Test $estTest;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);

        DB::purge('sqlite');
        DB::setDefaultConnection('sqlite');

        $this->createTables();
        $this->createRecords();
        $this->actingAs($this->student);
    }

    public function test_index_ignores_foreign_level_and_course_filters(): void
    {
        $request = Request::create('/users/tests', 'GET', [
            'level' => 'EST I',
            'level_id' => $this->est->id,
            'course_id' => $this->estCourse->id,
        ]);

        $view = app(TestsController::class)->index($request);
        $data = $view->getData();

        $this->assertSame([$this->digitalSat->id], $data['levels']->pluck('id')->all());
        $this->assertSame([$this->digitalSatCourse->id], $data['courses']->pluck('id')->all());
        $this->assertSame([$this->digitalSatCourse->id], $data['coursesWithTests']->pluck('id')->all());
        $this->assertSame($this->digitalSat->id, $data['levelId']);
        $this->assertNull($data['courseId']);
    }

    public function test_index_defaults_to_the_students_enrolled_level(): void
    {
        $view = app(TestsController::class)->index(Request::create('/users/tests', 'GET'));
        $data = $view->getData();

        $this->assertSame($this->digitalSat->id, $data['levelId']);
        $this->assertSame([$this->digitalSatCourse->id], $data['coursesWithTests']->pluck('id')->all());
        $this->assertNotContains($this->estCourse->id, $data['coursesWithTests']->pluck('id')->all());
    }

    public function test_student_cannot_open_a_test_from_another_level(): void
    {
        $this->expectException(ModelNotFoundException::class);

        app(TestsController::class)->show($this->estTest->id);
    }

    public function test_student_cannot_open_an_inactive_test(): void
    {
        $inactiveTest = Test::create(array_merge(
            $this->testAttributes('Inactive Digital SAT Test', $this->digitalSatCourse->id),
            ['is_active' => false]
        ));

        $this->expectException(ModelNotFoundException::class);

        app(TestsController::class)->show($inactiveTest->id);
    }

    private function createRecords(): void
    {
        $this->digitalSat = Level::create(['name' => 'Digital SAT']);
        $this->est = Level::create(['name' => 'EST I']);

        $this->digitalSatCourse = Course::create([
            'name' => 'Digital SAT Practice',
            'level_id' => $this->digitalSat->id,
            'track_slug' => 'digital-sat',
        ]);
        $this->estCourse = Course::create([
            'name' => 'EST I Practice',
            'level_id' => $this->est->id,
            'track_slug' => 'est-i',
        ]);

        $this->digitalSatTest = Test::create($this->testAttributes('Digital SAT Test', $this->digitalSatCourse->id));
        $this->estTest = Test::create($this->testAttributes('EST I Test', $this->estCourse->id));

        $this->student = User::create([
            'email' => 'digital-sat-student@example.test',
            'password' => 'unused',
            'level_id' => $this->digitalSat->id,
        ]);
    }

    private function testAttributes(string $name, int $courseId): array
    {
        return [
            'name' => $name,
            'course_id' => $courseId,
            'price' => 0,
            'total_score' => 800,
            'initial_score' => 200,
            'default_question_score' => 10,
            'part1_questions_count' => 1,
            'part1_time_minutes' => 35,
            'part2_questions_count' => 1,
            'part2_time_minutes' => 35,
            'break_time_minutes' => 10,
            'max_attempts' => 1,
            'is_active' => true,
        ];
    }

    private function createTables(): void
    {
        Schema::create('levels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('level_id')->nullable();
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('level_id');
            $table->string('track_slug')->nullable();
            $table->decimal('tests_price', 10, 2)->nullable();
            $table->integer('access_duration_days')->nullable();
            $table->timestamps();
        });

        Schema::create('tests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('course_id');
            $table->decimal('price', 10, 2)->default(0);
            $table->integer('total_score')->default(800);
            $table->integer('initial_score')->default(200);
            $table->integer('default_question_score')->default(10);
            $table->integer('part1_questions_count')->default(0);
            $table->integer('part1_time_minutes')->default(0);
            $table->integer('part2_questions_count')->default(0);
            $table->integer('part2_time_minutes')->default(0);
            $table->integer('break_time_minutes')->default(0);
            $table->integer('max_attempts')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('student_tests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('test_id');
            $table->string('status')->nullable();
            $table->integer('attempt_number')->default(1);
            $table->timestamps();
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('category')->nullable();
            $table->string('type')->nullable();
            $table->string('type_value')->nullable();
            $table->unsignedBigInteger('course_id')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }
}
