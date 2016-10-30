<?php

namespace Tests;

use App\Post;

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
        $post = Post::create([
            'title' => 'foo',
            'content' => 'bar',
        ]);

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
        $post = Post::create([
            'title' => 'foo',
            'content' => 'bar',
        ]);

        $this->json(
            'PUT',
            "api/v1/post/{$post->id}",
            ['content' => 'biz'],
            ['Authorization' => "Bearer {$this->user->api_token}"]
        );

        $this->seeJson(['success' => true]);

        $this->assertEquals('biz', $post->fresh()->content);
    }

    public function testDelete()
    {
        $post = Post::create([
            'title' => 'foo',
            'content' => 'bar',
        ]);

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
        Post::create([
            'title' => 'foo',
            'content' => 'bar',
        ]);

        $this->json(
            'GET',
            'api/v1/posts',
            [],
            ['Authorization' => "Bearer {$this->user->api_token}"]
        );

        $this->seeJsonStructure(['*' => ['title', 'content']]);
    }
}
