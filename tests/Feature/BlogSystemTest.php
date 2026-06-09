<?php

namespace Tests\Feature;

use App\Models\Blog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogSystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_lists_blogs_from_database(): void
    {
        Blog::create([
            'title' => 'Exam Result Announced',
            'category' => 'Result',
            'short_description' => 'The result is now available.',
            'content' => 'Detailed announcement for the latest result.',
            'image' => 'https://via.placeholder.com/600x400',
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Exam Result Announced');
    }

    public function test_ajax_filter_returns_matching_blogs(): void
    {
        Blog::create([
            'title' => 'Admit Card Released',
            'category' => 'Admit Card',
            'short_description' => 'Admit cards are released.',
            'content' => 'Students can now download their admit cards.',
            'image' => 'https://via.placeholder.com/600x400',
            'published_at' => now()->subDay(),
        ]);

        Blog::create([
            'title' => 'Session Result Updated',
            'category' => 'Result',
            'short_description' => 'Result has been updated.',
            'content' => 'The latest result summary is ready.',
            'image' => 'https://via.placeholder.com/600x400',
            'published_at' => now(),
        ]);

        $response = $this->getJson('/blogs/filter?category=Admit%20Card');

        $response->assertStatus(200);
        $response->assertJsonFragment(['title' => 'Admit Card Released']);
        $response->assertJsonMissing(['title' => 'Session Result Updated']);
    }
}
