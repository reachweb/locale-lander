<?php

use Facades\Reach\LocaleLander\Tests\Factories\EntryFactory;
use Reach\LocaleLander\Tests\FakesViews;
use Reach\LocaleLander\Tests\PreventSavingStacheItemsToDisk;
use Reach\LocaleLander\Tests\TestCase;
use Statamic\Facades;

class LocaleLanderTest extends TestCase
{
    use FakesViews, PreventSavingStacheItemsToDisk;

    public function setUp(): void
    {
        parent::setUp();

        $this->collection = Facades\Collection::make('pages')
            ->defaultPublishState('published')
            ->sites(['en', 'fr', 'de', 'gr'])
            ->routes('{slug}')
            ->structureContents([
                'root' => true,
                'max_depth' => 1,
            ])
            ->save();

        $this->collection->structure()->in('en')->tree(
            [
                ['entry' => 'home'],
                ['entry' => 'about'],
            ]
        )->save();
    }

    protected function makeEntry($site, $collection, $slug)
    {
        return EntryFactory::id($slug)->locale($site)->collection($collection)->slug($slug)->make();
    }

    public function createMultisiteEntries()
    {
        $this->makeEntry('en', $this->collection, 'home')->set('content', 'Home')->save();
        // $this->makeEntry('fr', $this->collection, 'home')->set('content', 'Accueil')->save();
        // $this->makeEntry('de', $this->collection, 'home')->set('content', 'Startseite')->save();
        // $this->makeEntry('gr', $this->collection, 'home')->set('content', 'Αρχική')->save();

        $this->makeEntry('en', $this->collection, 'about')->set('content', 'About')->save();
        // $this->makeEntry('fr', $this->collection, 'a-propos')->set('content', 'À propos')->save();
        // $this->makeEntry('de', $this->collection, 'uber')->set('content', 'Über uns')->save();
        // $this->makeEntry('gr', $this->collection, 'sxetika')->set('content', 'Σχετικά')->save();
    }

    /** @test */
    public function it_loads_homepage_for_each_language()
    {
        //$this->withoutExceptionHandling();
        $this->withStandardFakeViews();

        $this->createMultisiteEntries();

        $this->get('/')->assertSee('Home');
        $this->get('/about')->assertSee('About');
        // $this->get('/gr')->assertSee('Αρχική');

        //dd(Facades\Entry::all());
        //dd($this->collection);
    }
}
