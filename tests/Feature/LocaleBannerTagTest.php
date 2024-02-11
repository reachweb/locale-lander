<?php

use Reach\LocaleLander\Tags\LocaleBanner;
use Reach\LocaleLander\Tests\CreatesEntries;
use Reach\LocaleLander\Tests\FakesViews;
use Reach\LocaleLander\Tests\PreventSavingStacheItemsToDisk;
use Reach\LocaleLander\Tests\TestCase;
use Statamic\Facades;
use Statamic\Facades\Antlers;

class LocaleBannerTagTest extends TestCase
{
    use CreatesEntries, FakesViews, PreventSavingStacheItemsToDisk;

    public $collection;

    public $tag;

    public function setUp(): void
    {
        parent::setUp();

        $this->collection = Facades\Collection::make('pages')
            ->defaultPublishState('published')
            ->sites(['en', 'fr', 'de', 'gr'])
            ->routes('{parent_uri}/{slug}')
            ->structureContents([
                'root' => true,
                'max_depth' => 1,
            ])
            ->save();

        $this->tag = (new LocaleBanner)
            ->setParser(Antlers::parser())
            ->setContext([]);
    }

    /** @test */
    public function the_tag_returns_the_entry_if_locale_exists()
    {
        $this->withFakeViews();

        $this->viewShouldReturnRaw('layout', '{{ template_content }}');
        $this->viewShouldReturnRaw('default', '{{ locale_banner }}{{ /locale_banner }}');

        $this->createMultisiteEntries();

        Facades\Stache::clear();

        $response = $this->withHeaders([
            'Accept-Language' => 'fr_FR',
        ])->get('/');

        $tag = $this->tag->index();

        $this->assertIsArray($tag);
        $this->assertArrayHasKey('entry', $tag);
        $this->assertIsArray($tag['entry']);

        $this->assertArrayHasKey('site', $tag);
        $this->assertIsArray($tag['site']->toArray());

        $this->assertArrayHasKey('title', $tag['entry']);
        $this->assertArrayHasKey('url', $tag['entry']);

        $this->assertInstanceOf(\Statamic\Fields\Value::class, $tag['entry']['url']);
        $this->assertEquals('/fr', $tag['entry']['url']->raw());
        $this->assertEquals('French', $tag['site']->name());

    }

    private function setTagParameters($parameters)
    {
        $this->tag->setParameters($parameters);
    }
}
