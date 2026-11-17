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
        $response->assertSee('href="/admin/login"', false);
        $response->assertDontSee('href="http://localhost/admin/login"', false);
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

    public function test_ajax_filter_combines_category_and_date_and_handles_no_matches(): void
    {
        Blog::create([
            'title' => 'Current Result',
            'category' => 'Result',
            'short_description' => 'Published on the selected date.',
            'content' => 'Current result content.',
            'published_at' => '2026-11-17 09:00:00',
        ]);

        Blog::create([
            'title' => 'Earlier Result',
            'category' => 'Result',
            'short_description' => 'Published on another date.',
            'content' => 'Earlier result content.',
            'published_at' => '2026-11-16 09:00:00',
        ]);

        Blog::create([
            'title' => 'Current Admit Card',
            'category' => 'Admit Card',
            'short_description' => 'Another category on the selected date.',
            'content' => 'Admit card content.',
            'published_at' => '2026-11-17 10:00:00',
        ]);

        $this->getJson('/blogs/filter?category=Result&date=2026-11-17')
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment(['title' => 'Current Result'])
            ->assertJsonMissing(['title' => 'Earlier Result'])
            ->assertJsonMissing(['title' => 'Current Admit Card']);

        $this->getJson('/blogs/filter?category=Unknown')
            ->assertOk()
            ->assertExactJson([]);
    }

    public function test_blog_detail_shows_post_content_and_unknown_blog_returns_404(): void
    {
        $blog = Blog::create([
            'title' => 'Detailed Blog',
            'category' => 'News',
            'short_description' => 'A detailed blog summary.',
            'content' => 'Full detailed blog content.',
        ]);

        $this->get("/blogs/{$blog->id}")
            ->assertOk()
            ->assertSee('Detailed Blog')
            ->assertSee('Full detailed blog content.')
            ->assertSee('href="/"', false);

        $this->get('/blogs/999999')->assertNotFound();
    }

    public function test_admin_can_log_in_access_dashboard_and_log_out(): void
    {
        $this->get('/admin/login')->assertOk();

        $this->post('/admin/login', [
            'email' => 'admin@jobyaari.dev',
            'password' => 'admin12345',
        ])->assertRedirect('/admin/dashboard');

        $this->get('/admin/dashboard')->assertOk();

        $this->post('/admin/logout')
            ->assertRedirect('/admin/login');

        $this->get('/admin/dashboard')
            ->assertRedirect('/admin/login');
    }

    public function test_invalid_admin_credentials_show_an_error(): void
    {
        $this->from('/admin/login')->post('/admin/login', [
            'email' => 'admin@jobyaari.dev',
            'password' => 'incorrect',
        ])->assertRedirect('/admin/login')
            ->assertSessionHasErrors('email');

        $this->get('/admin/login')
            ->assertOk()
            ->assertSee('Invalid admin credentials.');
    }

    public function test_admin_can_create_update_and_delete_a_blog(): void
    {
        $this->withSession(['admin_logged_in' => true]);

        $this->get('/admin/blogs/create')->assertOk();

        $blogData = [
            'title' => 'CRUD test blog',
            'category' => 'Testing',
            'short_description' => 'A blog used to verify admin CRUD.',
            'content' => 'CRUD test content.',
            'image' => 'https://example.com/image.jpg',
            'published_at' => '2026-11-17',
        ];

        $this->post('/admin/blogs', $blogData)
            ->assertRedirect('/admin/dashboard')
            ->assertHeader('Location', '/admin/dashboard');

        $blog = Blog::where('title', 'CRUD test blog')->firstOrFail();
        $this->assertDatabaseHas('blogs', [
            'id' => $blog->id,
            'title' => 'CRUD test blog',
        ]);
        $this->get('/')->assertSee('CRUD test blog');

        $this->get('/admin/dashboard')
            ->assertOk()
            ->assertSee("href=\"/admin/blogs/{$blog->id}/edit\"", false)
            ->assertSee("action=\"/admin/blogs/{$blog->id}\"", false)
            ->assertSee('name="_method" value="DELETE"', false);

        $this->get("/admin/blogs/{$blog->id}")
            ->assertRedirect("/admin/blogs/{$blog->id}/edit");

        $this->get("/admin/blogs/{$blog->id}/edit")
            ->assertOk()
            ->assertSee('CRUD test blog');

        $this->put("/admin/blogs/{$blog->id}", [
            ...$blogData,
            'title' => 'Updated CRUD test blog',
            'content' => 'Updated CRUD test content.',
        ])->assertRedirect('/admin/dashboard');

        $this->assertDatabaseHas('blogs', [
            'id' => $blog->id,
            'title' => 'Updated CRUD test blog',
        ]);
        $this->get('/')->assertSee('Updated CRUD test blog');
        $this->get('/admin/dashboard')
            ->assertOk()
            ->assertSee('Updated CRUD test blog');
        $this->get("/blogs/{$blog->id}")
            ->assertOk()
            ->assertSee('Updated CRUD test content.');

        $this->delete("/admin/blogs/{$blog->id}")
            ->assertRedirect('/admin/dashboard');

        $this->assertDatabaseMissing('blogs', ['id' => $blog->id]);
        $this->get('/')->assertDontSee('Updated CRUD test blog');
    }

    public function test_admin_form_shows_validation_errors_when_a_blog_cannot_be_saved(): void
    {
        $this->withSession(['admin_logged_in' => true]);

        $this->from('/admin/blogs/create')->post('/admin/blogs', [
            'title' => 'Invalid image blog',
            'category' => 'Testing',
            'short_description' => 'Invalid image URL should be reported.',
            'content' => 'Test content.',
            'image' => 'not-a-url',
        ])->assertRedirect('/admin/blogs/create');

        $this->get('/admin/blogs/create')
            ->assertOk()
            ->assertSee('The image field must be a valid URL.');

        $this->assertDatabaseMissing('blogs', ['title' => 'Invalid image blog']);

        $this->from('/admin/blogs/create')->post('/admin/blogs', [
            'category' => 'Testing',
            'short_description' => 'A required title is missing.',
            'content' => 'Test content.',
        ])->assertRedirect('/admin/blogs/create');

        $this->get('/admin/blogs/create')
            ->assertOk()
            ->assertSee('The title field is required.');
    }

    public function test_admin_form_rejects_missing_required_blog_fields(): void
    {
        $this->withSession(['admin_logged_in' => true]);

        $this->from('/admin/blogs/create')->post('/admin/blogs', [
            'category' => 'Testing',
            'short_description' => 'A required title is missing.',
            'content' => 'Test content.',
        ])->assertRedirect('/admin/blogs/create')
            ->assertSessionHasErrors('title');

        $this->assertDatabaseMissing('blogs', [
            'short_description' => 'A required title is missing.',
        ]);
    }
}
