<?php

namespace Reach\LocaleLander\Tags;

use Reach\LocaleLander\Facades\LocaleHelper;
use Statamic\Tags\Tags;

class LocaleBanner extends Tags
{
    public function index()
    {
        $helper = LocaleHelper::boot(request());

        if ($helper->hasCookie() ||
        $helper->isCurrentLocaleCorrect()) {

            return $this->notFound();
        }

        $site = $helper->getMatchingSite();
        if (! $site) {
            return $this->notFound();
        }

        $content = $helper->findContentInLocale($site);
        if ($content) {
            return [
                'entry' => $content->toAugmentedArray(['title', 'url']),
                'site' => $site,
            ];
        }

        return $this->notFound();
    }

    protected function notFound()
    {
        return [
            'entry' => false,
        ];
    }
}
