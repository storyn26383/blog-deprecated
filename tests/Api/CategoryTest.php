<?php

namespace Tests;

use App\Post;
use App\Category;

class CategoryTest extends TestCase
{
    public function testCreate()
    {
        $this->json(
            'POST',
            'api/v1/category',
            ['name' => 'foo', 'slug' => 'foo'],
            ['Authorization' => "Bearer {$this->user->api_token}"]
        );

        $this->seeJsonStructure(['id']);

        $this->seeInDatabase('categories', ['name' => 'foo', 'slug' => 'foo']);
    }

    public function testRead()
    {
        $category = factory(Category::class)->create();

        $this->json(
            'GET',
            "api/v1/category/{$category->id}",
            [],
            ['Authorization' => "Bearer {$this->user->api_token}"]
        );

        $this->seeJson($category->toArray());
    }

    public function testUpdate()
    {
        $category = factory(Category::class)->create();

        $this->json(
            'PUT',
            "api/v1/category/{$category->id}",
            ['slug' => 'foo'],
            ['Authorization' => "Bearer {$this->user->api_token}"]
        );

        $this->seeJson(['success' => true]);

        $this->assertEquals('foo', $category->fresh()->slug);
    }

    public function testDelete()
    {
        $category = factory(Category::class)->create();

        $this->json(
            'DELETE',
            "api/v1/category/{$category->id}",
            [],
            ['Authorization' => "Bearer {$this->user->api_token}"]
        );

        $this->seeJson(['success' => true]);

        $this->assertNull($category->fresh());
    }

    public function testList()
    {
        factory(Category::class, 3)->create();

        $this->json(
            'GET',
            'api/v1/categories',
            [],
            ['Authorization' => "Bearer {$this->user->api_token}"]
        );

        $this->seeJsonStructure(['*' => ['name' , 'slug']]);
    }

    public function testPostsOfCategory()
    {
        $category = factory(Category::class)->create();

        $category->posts()->sync(factory(Post::class, 3)->create());

        $this->json(
            'GET',
            "api/v1/category/{$category->id}/posts",
            [],
            ['Authorization' => "Bearer {$this->user->api_token}"]
        );

        $this->seeJsonStructure(['*' => ['title' , 'content', 'categories', 'tags']]);
    }
}
