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
        $this->createMultisiteEntries();

        Facades\Stache::clear();

        ray($this->tag->index());
    }

    private function setTagParameters($parameters)
    {
        $this->tag->setParameters($parameters);
    }
}
