<?php

namespace Database\Seeders;

use App\Models\Member;
use Illuminate\Database\Seeder;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 50 members
        Member::factory()->count(50)->create();

        $this->command->info('Created 50 members successfully!');
    }
}
