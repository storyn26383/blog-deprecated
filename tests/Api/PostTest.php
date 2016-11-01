<?php

namespace Tests;

use App\Tag;
use App\Post;
use App\Category;

class PostTest extends TestCase
{
    public function testCreate()
    {
        $this->json(
            'POST',
            'api/v1/post',
            ['title' => 'foo', 'content' => 'bar'],
            ['Authorization' => "Bearer {$this->user->api_token}"]
        );

        $this->seeJsonStructure(['id']);

        $this->seeInDatabase('posts', ['title' => 'foo', 'content' => 'bar']);
    }

    public function testRead()
    {
        $post = factory(Post::class)->create();

        $this->json(
            'GET',
            "api/v1/post/{$post->id}",
            [],
            ['Authorization' => "Bearer {$this->user->api_token}"]
        );

        $this->seeJson($post->toArray());
    }

    public function testUpdate()
    {
        $post = factory(Post::class)->create();

        $this->json(
            'PUT',
            "api/v1/post/{$post->id}",
            ['content' => 'Hello World'],
            ['Authorization' => "Bearer {$this->user->api_token}"]
        );

        $this->seeJson(['success' => true]);

        $this->assertEquals('Hello World', $post->fresh()->content);
    }

    public function testDelete()
    {
        $post = factory(Post::class)->create();

        $this->json(
            'DELETE',
            'api/v1/post/1',
            [],
            ['Authorization' => "Bearer {$this->user->api_token}"]
        );

        $this->seeJson(['success' => true]);

        $this->assertNull($post->fresh());
    }

    public function testList()
    {
        factory(Post::class, 3)->create();

        $this->json(
            'GET',
            'api/v1/posts',
            [],
            ['Authorization' => "Bearer {$this->user->api_token}"]
        );

        $this->seeJsonStructure([
            'current_page',
            'data' => [
                '*' => [
                    'title',
                    'content',
                    'categories',
                    'tags',
                ]
            ]
        ]);
    }

    public function testCreateWithCategories()
    {
        $categories = factory(Category::class, 3)->create();

        $this->json(
            'POST',
            'api/v1/post',
            [
                'title' => 'foo',
                'content' => 'bar',
                'categories' => $categories->pluck('id')->implode(','),
            ],
            ['Authorization' => "Bearer {$this->user->api_token}"]
        );

        $post = Post::find($this->response->getOriginalContent()['id']);

        $categories->each(function ($category) use (&$post) {
            $this->assertTrue($post->categories->contains($category));
        });
    }

    public function testCreateWithTags()
    {
        $tags = factory(Tag::class, 3)->create();

        $this->json(
            'POST',
            'api/v1/post',
            [
                'title' => 'foo',
                'content' => 'bar',
                'tags' => $tags->pluck('id')->implode(','),
            ],
            ['Authorization' => "Bearer {$this->user->api_token}"]
        );

        $post = Post::find($this->response->getOriginalContent()['id']);

        $tags->each(function ($tag) use (&$post) {
            $this->assertTrue($post->tags->contains($tag));
        });
    }
}
