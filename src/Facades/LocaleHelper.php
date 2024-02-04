<?php

namespace Reach\LocaleLander\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static self boot($request)
 * @method static bool isCurrentLocaleCorrect()
 * @method static ?\Statamic\Sites\Site getMatchingSite()
 * @method static mixed browserLocale()
 * @method static mixed browserLanguage()
 */
class LocaleHelper extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'localehelper';
    }
}
