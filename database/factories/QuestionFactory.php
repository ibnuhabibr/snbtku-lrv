<?php

namespace Database\Factories;

use App\Models\Topic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Question>
 */
class QuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $correctAnswer = $this->faker->randomElement(['a', 'b', 'c', 'd', 'e']);
        
        return [
            'topic_id' => Topic::factory(),
            'question_text' => $this->faker->paragraph(3),
            'option_a' => $this->faker->sentence(),
            'option_b' => $this->faker->sentence(),
            'option_c' => $this->faker->sentence(),
            'option_d' => $this->faker->sentence(),
            'option_e' => $this->faker->sentence(),
            'correct_answer' => $correctAnswer,
            'explanation' => $this->faker->paragraph(2),
        ];
    }
}
