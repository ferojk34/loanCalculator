<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use Modules\Blog\Entities\Blog;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BlogTest extends TestCase
{
    use RefreshDatabase;

    public function testCreateBlogPost(): void
    {
        $blogData = $this->getBlogData();
        $response = $this->post($this->getRoute("blog.store"), $blogData);
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonFragment([
            "status" => "success",
            "message" => "create-success",
        ]);
    }

    public function testUpdateBlogPost(): void
    {
        $blog = $this->createBlog();
        $response = $this->put(
            uri: "api/blog/{$blog->id}",
            data: array_merge(
                    $this->getBlogData(),
                    ["description" => "Hello World"]
                )
            );
        $response->assertOk();
        $response->assertJsonFragment([
            "status" => "success",
            "message" => "update-success",
        ]);
    }

    public function testShouldReturnValidationErrorOnBlogPost(): void
    {
        $response = $this->post($this->getRoute("blog.store"), []);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testShowBlogPost(): void
    {
        $blog = $this->createBlog();
        $response = $this->get("api/blog/{$blog->id}");
        $response->assertOk();
        $response->assertJsonFragment([
            "status" => "success",
            "message" => "fetch-success",
        ]);
    }

    public function testAllBlogPost(): void
    {
        $blog = $this->createBlog();
        $response = $this->get("api/blog");
        $response->assertOk();
        $response->assertJsonFragment([
            "status" => "success",
            "message" => "fetch-success",
        ]);
    }

    public function testDeleteBlogPost(): void
    {
        $blog = $this->createBlog();
        $response = $this->delete("api/blog/{$blog->id}");
        $response->assertOk();
        $response->assertJsonFragment([
            "status" => "success",
            "message" => "delete-success",
        ]);
    }

    private function createBlog(): object
    {
        $blog = Blog::create($this->getBlogData());
        return $blog;
    }

    private function getBlogData(): array
    {
        $blogData = [
            "title" => "blog1",
            "description" => "lorem lorem",
            "slug" => Str::slug("blog1"),
            "created_at" => now(),
            "updated_at" => now(),
        ];
        return $blogData;
    }

    private function getRoute(string $method, ?array $parameters = null): string
    {
        return route("{$method}", $parameters);
    }
}
