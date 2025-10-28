<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\TryoutPackage;
use App\Models\Question;
use App\Models\UserTryout;
use App\Models\UserTryoutAnswer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use App\Livewire\ConductTryout;

class ConductTryoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test data
        $this->user = User::factory()->create();
        $this->package = TryoutPackage::factory()->create();
        $this->questions = Question::factory()->count(3)->create();
        
        // Attach questions to package with order
        foreach ($this->questions as $index => $question) {
            $this->package->questions()->attach($question->id, ['order' => $index + 1]);
        }
    }

    /** @test */
    public function it_can_save_answers_for_multiple_questions()
    {
        $this->actingAs($this->user);

        // Create UserTryout
        $userTryout = UserTryout::create([
            'user_id' => $this->user->id,
            'tryout_package_id' => $this->package->id,
            'start_time' => now(),
            'status' => 'ongoing',
        ]);

        $component = Livewire::test(ConductTryout::class, ['userTryoutId' => $userTryout->id]);

        // Test first question
        $firstQuestion = $this->questions->first();
        $component->call('selectAnswer', $firstQuestion->id, 'a');
        
        // Verify answer is saved in component
        $this->assertEquals('a', $component->get('userAnswers')[$firstQuestion->id]);
        
        // Verify answer is saved in database
        $this->assertDatabaseHas('user_tryout_answers', [
            'question_id' => $firstQuestion->id,
            'user_answer' => 'a'
        ]);

        // Move to next question
        $component->call('nextQuestion');
        
        // Test second question
        $secondQuestion = $this->questions->skip(1)->first();
        $component->call('selectAnswer', $secondQuestion->id, 'b');
        
        // Verify both answers are preserved
        $userAnswers = $component->get('userAnswers');
        $this->assertEquals('a', $userAnswers[$firstQuestion->id]);
        $this->assertEquals('b', $userAnswers[$secondQuestion->id]);
        
        // Verify both answers are in database
        $this->assertDatabaseHas('user_tryout_answers', [
            'question_id' => $firstQuestion->id,
            'user_answer' => 'a'
        ]);
        
        $this->assertDatabaseHas('user_tryout_answers', [
            'question_id' => $secondQuestion->id,
            'user_answer' => 'b'
        ]);
    }

    /** @test */
    public function it_preserves_answers_when_navigating_between_questions()
    {
        $this->actingAs($this->user);

        // Create UserTryout
        $userTryout = UserTryout::create([
            'user_id' => $this->user->id,
            'tryout_package_id' => $this->package->id,
            'start_time' => now(),
            'status' => 'ongoing',
        ]);

        $component = Livewire::test(ConductTryout::class, ['userTryoutId' => $userTryout->id]);

        $firstQuestion = $this->questions->first();
        $secondQuestion = $this->questions->skip(1)->first();

        // Answer first question
        $component->call('selectAnswer', $firstQuestion->id, 'a');
        
        // Move to second question and answer it
        $component->call('nextQuestion');
        $component->call('selectAnswer', $secondQuestion->id, 'c');
        
        // Go back to first question
        $component->call('prevQuestion');
        
        // Verify first question answer is still preserved
        $userAnswers = $component->get('userAnswers');
        $this->assertEquals('a', $userAnswers[$firstQuestion->id]);
        $this->assertEquals('c', $userAnswers[$secondQuestion->id]);
    }
}