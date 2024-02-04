<?php

use Facades\Reach\LocaleLander\Tests\Factories\EntryFactory;
use Illuminate\Support\Facades\Config;
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
        $home = $this->makeEntry('en', $this->collection, 'home')->set('content', 'Home');
        $homeFr = $home->makeLocalization('fr')->set('content', 'Accueil');
        $homeGr = $home->makeLocalization('gr')->set('content', 'Αρχική');
        $homeDe = $home->makeLocalization('de')->set('content', 'Startseite');

        $home->save();
        $homeFr->save();
        $homeGr->save();
        $homeDe->save();

        $about = $this->makeEntry('en', $this->collection, 'about')->set('content', 'About');
        $aboutFr = $about->makeLocalization('fr')->slug('a-props')->set('content', 'À propos');
        $aboutGr = $about->makeLocalization('gr')->slug('sxetika')->set('content', 'Σχετικά');

        $about->save();
        $aboutFr->save();
        $aboutGr->save();

        $this->collection->structure()->in('en')->tree(
            [
                ['entry' => 'home'],
                ['entry' => 'about'],
            ]
        )->save();

        $this->collection->structure()->in('fr')->tree(
            [
                ['entry' => $homeFr->id()],
                ['entry' => $aboutFr->id()],
            ]
        )->save();

        $this->collection->structure()->in('gr')->tree(
            [
                ['entry' => $homeGr->id()],
                ['entry' => $aboutGr->id()],
            ]
        )->save();

        $this->collection->structure()->in('de')->tree(
            [
                ['entry' => $homeDe->id()],
            ]
        )->save();
    }

    /** @test */
    public function it_loads_homepage_for_each_language()
    {
        $this->withStandardFakeViews();

        $this->createMultisiteEntries();

        Facades\Stache::clear();

        $this->get('/')->assertSee('Home');
        $this->get('/fr')->assertSee('Accueil');
        $this->get('/gr')->assertSee('Αρχική');
        $this->get('/de')->assertSee('Startseite');

    }

    /** @test */
    public function it_loads_about_page_for_each_language()
    {
        $this->withStandardFakeViews();

        $this->createMultisiteEntries();

        Facades\Stache::clear();

        $this->get('/about')->assertSee('About');
        $this->get('/fr/a-props')->assertSee('propos');
        $this->get('/gr/sxetika')->assertSee('Σχετικά');

    }

    /** @test */
    public function homepage_gets_redirected_to_the_right_language_if_content_exists()
    {
        $this->withStandardFakeViews();

        $this->createMultisiteEntries();

        Facades\Stache::clear();

        $response = $this->withHeaders([
            'Accept-Language' => 'fr_FR',
        ])->get('/');

        $response->assertRedirect('/fr')->assertSessionHas('locale_lander', 'completed');
    }

    /** @test */
    public function about_page_gets_redirected_to_the_right_language_if_content_exists()
    {
        $this->withStandardFakeViews();

        $this->createMultisiteEntries();

        Facades\Stache::clear();

        $response = $this->withHeaders([
            'Accept-Language' => 'el_GR',
        ])->get('/about');

        $response->assertRedirect('/gr/sxetika')->assertSessionHas('locale_lander', 'completed');
    }

    /** @test */
    public function it_does_not_redirect_if_content_missing()
    {
        $this->withoutExceptionHandling();
        $this->withStandardFakeViews();

        $this->createMultisiteEntries();

        Facades\Stache::clear();

        $response = $this->withHeaders([
            'Accept-Language' => 'de_DE',
        ])->get('/about');

        $response->assertSee('About')->assertSessionMissing('locale_lander', 'completed');
    }

    /** @test */
    public function it_does_not_redirect_if_redirect_disabled()
    {
        Config::set('locale-lander.enable_redirection', false);
        $this->withStandardFakeViews();

        $this->createMultisiteEntries();

        Facades\Stache::clear();

        $response = $this->withHeaders([
            'Accept-Language' => 'fr_FR',
        ])->get('/');

        $response->assertSee('Home')->assertSessionMissing('locale_lander', 'completed');
    }

    /** @test */
    public function it_does_not_redirect_if_already_did()
    {
        $this->withStandardFakeViews();

        $this->createMultisiteEntries();

        Facades\Stache::clear();

        $response = $this->withHeaders([
            'Accept-Language' => 'el_GR',
        ])->get('/');

        $response->assertRedirect('/gr')->assertSessionHas('locale_lander', 'completed');

        $response = $this->withHeaders([
            'Accept-Language' => 'el_GR',
        ])->get('/');

        $response->assertSee('Home')->assertSessionHas('locale_lander', 'completed');
    }
}
