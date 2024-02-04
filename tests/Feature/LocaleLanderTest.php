<?php

use Facades\Reach\LocaleLander\Tests\Factories\EntryFactory;
use Reach\LocaleLander\Tests\FakesViews;
use Reach\LocaleLander\Tests\PreventSavingStacheItemsToDisk;
use Reach\LocaleLander\Tests\TestCase;
use Statamic\Facades;

class LocaleLanderTest extends TestCase
{
    use FakesViews, PreventSavingStacheItemsToDisk;

    public $collection;

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
    }

    protected function makeEntry($site, $collection, $slug)
    {
        return EntryFactory::id($slug)->locale($site)->collection($collection)->slug($slug)->make();
    }

    public function createMultisiteEntries()
    {
        $this->makeEntry('en', $this->collection, 'home')->set('content', 'Home')->save();
        $this->makeEntry('en', $this->collection, 'about')->set('content', 'About')->save();

        $this->collection->structure()->in('en')->tree(
            [
                ['entry' => 'home'],
                ['entry' => 'about'],
            ]
        )->save();
    }

    /** @test */
    public function it_loads_homepage_for_each_language()
    {
        $this->withStandardFakeViews();

        $this->createMultisiteEntries();

        ray(Facades\Entry::all()->toArray());

        $this->get('/')->assertSee('Home');
        $this->get('/about')->assertSee('About');

    }
}
