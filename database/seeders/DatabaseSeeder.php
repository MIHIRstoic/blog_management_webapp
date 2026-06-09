<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        Blog::create([
            'title' => 'Admit Card Released for the Upcoming Exam',
            'category' => 'Admit Card',
            'short_description' => 'Students can now download their admit cards and prepare for the exam schedule.',
            'content' => 'The admit cards are now available for download on the student portal. Please review your exam center details and carry a valid ID on the day of the exam.',
            'image' => 'https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?auto=format&fit=crop&w=900&q=80',
            'published_at' => now()->subDays(2),
        ]);

        Blog::create([
            'title' => 'Session Results Are Now Available',
            'category' => 'Result',
            'short_description' => 'The latest academic result has been published online for all departments.',
            'content' => 'The result sheets are now live. Please check your score, grade summary, and result status in the portal. Students with queries may contact the academic office.',
            'image' => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=900&q=80',
            'published_at' => now()->subDay(),
        ]);
    }
}
