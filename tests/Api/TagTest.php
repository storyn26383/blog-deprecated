<?php

namespace Tests;

use App\Tag;
use App\Post;

class TagTest extends TestCase
{
    public function testCreate()
    {
        $this->json(
            'POST',
            'api/v1/tag',
            ['name' => 'foo'],
            ['Authorization' => "Bearer {$this->user->api_token}"]
        );

        $this->seeJsonStructure(['id']);

        $this->seeInDatabase('tags', ['name' => 'foo']);
    }

    public function testRead()
    {
        $tag = factory(Tag::class)->create();

        $this->json(
            'GET',
            "api/v1/tag/{$tag->id}",
            [],
            ['Authorization' => "Bearer {$this->user->api_token}"]
        );

        $this->seeJson($tag->toArray());
    }

    public function testUpdate()
    {
        $tag = factory(Tag::class)->create();

        $this->json(
            'PUT',
            "api/v1/tag/{$tag->id}",
            ['name' => 'foo'],
            ['Authorization' => "Bearer {$this->user->api_token}"]
        );

        $this->seeJson(['success' => true]);

        $this->assertEquals('foo', $tag->fresh()->name);
    }

    public function testDelete()
    {
        $tag = factory(Tag::class)->create();

        $this->json(
            'DELETE',
            "api/v1/tag/{$tag->id}",
            [],
            ['Authorization' => "Bearer {$this->user->api_token}"]
        );

        $this->seeJson(['success' => true]);

        $this->assertNull($tag->fresh());
    }

    public function testList()
    {
        factory(Tag::class, 3)->create();

        $this->json(
            'GET',
            'api/v1/tags',
            [],
            ['Authorization' => "Bearer {$this->user->api_token}"]
        );

        $this->seeJsonStructure(['*' => ['name']]);
    }

    public function testPostsOfTag()
    {
        $tag = factory(Tag::class)->create();

        $tag->posts()->sync(factory(Post::class, 3)->create());

        $this->json(
            'GET',
            "api/v1/tag/{$tag->id}/posts",
            [],
            ['Authorization' => "Bearer {$this->user->api_token}"]
        );

        $this->seeJsonStructure(['*' => ['title' , 'content', 'categories', 'tags']]);
    }
}
