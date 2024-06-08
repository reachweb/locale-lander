<?php

namespace Reach\LocaleLander\Tests;

use Facades\Reach\LocaleLander\Tests\Factories\EntryFactory;

trait CreatesEntries
{
    protected function makeEntry($site, $collection, $slug)
    {
        return EntryFactory::id($slug)->locale($site)->collection($collection)->slug($slug)->make();
    }

    public function createMultisiteEntries()
    {
        $this->collection->structure()->makeTree('en')->save();
        $this->collection->structure()->makeTree('fr')->save();
        $this->collection->structure()->makeTree('gr')->save();
        $this->collection->structure()->makeTree('de')->save();

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
}
