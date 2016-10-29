<?php

namespace Tests;

use App\User;
use App\Category;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CategoryTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;

    public function setUp()
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }

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
        $category = Category::create([
            'name' => 'foo',
            'slug' => 'foo',
        ]);

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
        $category = Category::create([
            'name' => 'foo',
            'slug' => 'foo',
        ]);

        $this->json(
            'PUT',
            "api/v1/category/{$category->id}",
            ['slug' => 'bar'],
            ['Authorization' => "Bearer {$this->user->api_token}"]
        );

        $this->seeJson(['success' => true]);

        $this->assertEquals('bar', $category->fresh()->slug);
    }

    public function testDelete()
    {
        $category = Category::create([
            'name' => 'foo',
            'slug' => 'foo',
        ]);

        $this->json(
            'DELETE',
            'api/v1/category/1',
            [],
            ['Authorization' => "Bearer {$this->user->api_token}"]
        );

        $this->seeJson(['success' => true]);

        $this->assertNull($category->fresh());
    }

    public function testList()
    {
        Category::create([
            'name' => 'foo',
            'slug' => 'foo',
        ]);

        $this->json(
            'GET',
            'api/v1/categories',
            [],
            ['Authorization' => "Bearer {$this->user->api_token}"]
        );

        $this->seeJsonStructure(['*' => ['name' , 'slug']]);
    }
}
