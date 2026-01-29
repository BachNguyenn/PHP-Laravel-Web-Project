<?php

namespace Database\Factories;

use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Member>
 */
class MemberFactory extends Factory
{
    protected $model = Member::class;

    /**
     * Static counter for unique member_id
     */
    private static int $counter = 0;

    /**
     * Generate unique member_id for factory
     */
    private function generateUniqueMemberId(): string
    {
        $prefix = date('ym'); // e.g., "2601" for January 2026
        
        // Get max existing sequence
        if (self::$counter === 0) {
            $lastMember = Member::where('member_id', 'like', $prefix . '%')
                ->orderBy('member_id', 'desc')
                ->first();
            
            if ($lastMember) {
                self::$counter = (int) substr($lastMember->member_id, 4);
            }
        }
        
        self::$counter++;
        
        return $prefix . str_pad(self::$counter, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-1 year', 'now');
        $endDate = $this->faker->dateTimeBetween($startDate, '+1 year');

        return [
            'member_id' => $this->generateUniqueMemberId(),
            'name' => $this->faker->name(),
            'gender' => $this->faker->randomElement(['male', 'female', 'other']),
            'mobile' => $this->faker->numerify('09########'),
            'blood_group' => $this->faker->randomElement(['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-']),
            'address' => $this->faker->address(),
            'photo' => null,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $this->faker->boolean(80), // 80% active
        ];
    }

    /**
     * Indicate that the member is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => true,
        ]);
    }

    /**
     * Indicate that the member is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => false,
        ]);
    }
}

