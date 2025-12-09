<?php

use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\Test;
use Reach\LocaleLander\Tests\CreatesEntries;
use Reach\LocaleLander\Tests\FakesViews;
use Reach\LocaleLander\Tests\PreventSavingStacheItemsToDisk;
use Reach\LocaleLander\Tests\TestCase;
use Statamic\Facades;

class LocaleLanderTest extends TestCase
{
    use CreatesEntries, FakesViews, PreventSavingStacheItemsToDisk;

    public $collection;

    public function setUp(): void
    {
        parent::setUp();

        $this->withStandardFakeViews();

        $this->collection = Facades\Collection::make('pages')
            ->defaultPublishState('published')
            ->sites(['en', 'fr', 'de', 'gr'])
            ->routes('{parent_uri}/{slug}')
            ->structureContents([
                'root' => true,
            ])
            ->save();

        $this->createMultisiteEntries();
    }

    #[Test]
    public function it_loads_homepage_for_each_language()
    {
        $this->get('/')->assertSee('Home');
        $this->get('/fr')->assertSee('Accueil');
        $this->get('/gr')->assertSee('Αρχική');
        $this->get('/de')->assertSee('Startseite');
    }

    #[Test]
    public function it_loads_about_page_for_each_language()
    {
        $this->get('/about')->assertSee('About');
        $this->get('/fr/a-props')->assertSee('propos');
        $this->get('/gr/sxetika')->assertSee('Σχετικά');
    }

    #[Test]
    public function homepage_gets_redirected_to_the_right_language_if_content_exists()
    {
        $response = $this->withHeaders([
            'Accept-Language' => 'fr_FR',
        ])->get('/');

        $response->assertRedirect('/fr')->assertSessionHas('locale_lander', 'completed');
    }

    #[Test]
    public function about_page_gets_redirected_to_the_right_language_if_content_exists()
    {
        $response = $this->withHeaders([
            'Accept-Language' => 'el_GR',
        ])->get('/about');

        $response->assertRedirect('/gr/sxetika')->assertSessionHas('locale_lander', 'completed');
    }

    #[Test]
    public function it_does_not_redirect_if_content_missing()
    {
        $response = $this->withHeaders([
            'Accept-Language' => 'de_DE',
        ])->get('/about');

        $response->assertSee('About')->assertSessionMissing('locale_lander', 'completed');
    }

    #[Test]
    public function it_does_not_redirect_if_content_unpublished()
    {
        $this->withoutExceptionHandling();

        Facades\Entry::find('home')->in('de')->published(false)->save();

        $response = $this->withHeaders([
            'Accept-Language' => 'de_DE',
        ])->get('/');

        $response->assertSee('Home')->assertSessionMissing('locale_lander', 'completed');
    }

    #[Test]
    public function it_does_not_redirect_if_redirect_disabled()
    {
        Config::set('locale-lander.enable_redirection', false);

        Facades\Stache::clear();

        $response = $this->withHeaders([
            'Accept-Language' => 'fr_FR',
        ])->get('/');

        $response->assertSee('Home')->assertSessionMissing('locale_lander', 'completed');
    }

    #[Test]
    public function it_does_not_redirect_if_already_did()
    {
        $response = $this->withHeaders([
            'Accept-Language' => 'el_GR',
        ])->get('/');

        $response->assertRedirect('/gr')->assertSessionHas('locale_lander', 'completed');

        $response = $this->withHeaders([
            'Accept-Language' => 'el_GR',
        ])->get('/');

        $response->assertSee('Home')->assertSessionHas('locale_lander', 'completed');
    }

    #[Test]
    public function homepage_gets_redirected_to_the_right_language_if_homepage_filter_enabled()
    {
        Config::set('locale-lander.redirect_only_homepage', true);

        $response = $this->withHeaders([
            'Accept-Language' => 'fr_FR',
        ])->get('/');

        $response->assertRedirect('/fr')->assertSessionHas('locale_lander', 'completed');
    }

    #[Test]
    public function about_page_gets_not_redirected_if_homepage_filter_enabled()
    {
        Config::set('locale-lander.redirect_only_homepage', true);

        $response = $this->withHeaders([
            'Accept-Language' => 'fr_FR',
        ])->get('/about');

        $response->assertSee('About')->assertSessionMissing('locale_lander', 'completed');
    }
}
