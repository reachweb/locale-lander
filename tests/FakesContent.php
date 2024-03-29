<?php

namespace Reach\LocaleLander\Tests;

use Facades\Tests\Factories\EntryFactory;
use Statamic\Facades\Collection;

trait FakesContent
{
    protected function createPage($slug, $attributes = [])
    {
        $this->makeCollection()->save();

        return tap($this->makePage($slug, $attributes))->save();
    }

    protected function makePage($slug, $attributes = [])
    {
        return EntryFactory::slug($slug)
            ->id($slug)
            ->collection('pages')
            ->data($attributes['with'] ?? [])
            ->make();
    }

    protected function makeCollection()
    {
        return Collection::make('pages')
            ->routes('{slug}')
            ->template('default');
    }
}
