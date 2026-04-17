<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\JobPost;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create a test worker
        $worker = User::factory()->create([
            'name' => 'Test Worker',
            'email' => 'worker@example.com',
            'role' => 'worker'
        ]);
        $worker->profile()->create();

        // Create a test employer
        $employer = User::factory()->create([
            'name' => 'PT Test Company',
            'email' => 'employer@example.com',
            'role' => 'employer'
        ]);
        $employer->profile()->create();

        // Create sample job posts
        JobPost::create([
            'employer_id' => $employer->id,
            'title' => 'Senior Laravel Developer',
            'category' => 'Software Development',
            'type' => 'full-time',
            'salary_range' => '15 - 20 juta',
            'description' => 'Kami mencari developer Laravel berpengalaman untuk bergabung dengan tim kami. Minimum 3 tahun pengalaman dengan Laravel dan PostgreSQL.',
            'requirements' => "- Pengalaman 3+ tahun dengan Laravel\n- Ahli dalam MySQL/PostgreSQL\n- Paham REST API\n- Familiar dengan Git",
            'status' => 'open'
        ]);

        JobPost::create([
            'employer_id' => $employer->id,
            'title' => 'Frontend React Developer',
            'category' => 'Software Development',
            'type' => 'full-time',
            'salary_range' => '12 - 18 juta',
            'description' => 'Mencari frontend developer yang passionate dengan React dan modern JavaScript. Akan bekerja pada proyek e-commerce skala besar.',
            'requirements' => "- 2+ tahun React experience\n- Familiar dengan TypeScript\n- Paham Tailwind CSS atau Bootstrap\n- Good problem-solving skills",
            'status' => 'open'
        ]);

        JobPost::create([
            'employer_id' => $employer->id,
            'title' => 'UI/UX Designer',
            'category' => 'Design',
            'type' => 'part-time',
            'salary_range' => '8 - 12 juta',
            'description' => 'Kami membutuhkan designer berbakat untuk membuat interface yang menarik dan user-friendly untuk aplikasi mobile dan web kami.',
            'requirements' => "- Portfolio dengan 5+ project design\n- Familiar dengan Figma\n- Paham design principles\n- Attention to detail",
            'status' => 'open'
        ]);
    }
}
