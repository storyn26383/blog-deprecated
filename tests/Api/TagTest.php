<?php

namespace Tests;

use App\Tag;

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
        $tag = Tag::create([
            'name' => 'foo',
        ]);

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
        $tag = Tag::create([
            'name' => 'foo',
        ]);

        $this->json(
            'PUT',
            "api/v1/tag/{$tag->id}",
            ['name' => 'bar'],
            ['Authorization' => "Bearer {$this->user->api_token}"]
        );

        $this->seeJson(['success' => true]);

        $this->assertEquals('bar', $tag->fresh()->name);
    }

    public function testDelete()
    {
        $tag = Tag::create([
            'name' => 'foo',
        ]);

        $this->json(
            'DELETE',
            'api/v1/tag/1',
            [],
            ['Authorization' => "Bearer {$this->user->api_token}"]
        );

        $this->seeJson(['success' => true]);

        $this->assertNull($tag->fresh());
    }

    public function testList()
    {
        Tag::create([
            'name' => 'foo',
        ]);

        $this->json(
            'GET',
            'api/v1/tags',
            [],
            ['Authorization' => "Bearer {$this->user->api_token}"]
        );

        $this->seeJsonStructure(['*' => ['name']]);
    }
}
