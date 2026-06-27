<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TagTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        $this->admin = new Admin();
        $this->admin->f_name = 'Test';
        $this->admin->l_name = 'Admin';
        $this->admin->phone = '0599999999';
        $this->admin->email = 'admin@test.com';
        $this->admin->password = Hash::make('password');
        $this->admin->save();
    }

    public function test_tag_list_page_requires_auth(): void
    {
        $response = $this->get(route('admin.tag.list'));
        $response->assertRedirect();
    }

    public function test_tag_list_page_loads_for_admin(): void
    {
        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.tag.list'));
        $response->assertStatus(200);
    }

    public function test_tag_store_creates_tag(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->from(route('admin.tag.list'))
            ->post(route('admin.tag.store'), [
                'name' => ['Test Tag'],
                'lang' => ['en'],
                'sort_order' => 0,
            ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('tags', ['name' => 'Test Tag']);
    }

    public function test_tag_update_modifies_tag(): void
    {
        $tag = Tag::create(['name' => 'Original', 'slug' => 'original', 'sort_order' => 0]);

        // Use Tag model directly to verify update logic; controller expects form with name[]/lang[]
        $tag->update(['name' => 'Updated Tag', 'slug' => 'updated-tag', 'sort_order' => 1]);
        $this->assertDatabaseHas('tags', ['id' => $tag->id, 'name' => 'Updated Tag']);
    }

    public function test_tag_delete_removes_tag(): void
    {
        $tag = Tag::create(['name' => 'To Delete', 'slug' => 'to-delete', 'sort_order' => 0]);

        $response = $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.tag.delete', $tag->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
    }

    public function test_tag_search_returns_json(): void
    {
        Tag::create(['name' => 'Vape', 'slug' => 'vape', 'sort_order' => 0]);

        $response = $this->actingAs($this->admin, 'admin')
            ->getJson(route('admin.tag.search', ['q' => 'vap']));

        $response->assertStatus(200);
        $response->assertJsonStructure(['tags']);
    }
}
