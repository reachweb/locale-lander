<?php

namespace Reach\LocaleLander\Tests;

use Statamic\Testing\Concerns\PreventsSavingStacheItemsToDisk as BasePreventsSavingStacheItemsToDisk;

trait PreventSavingStacheItemsToDisk
{
    use BasePreventsSavingStacheItemsToDisk;
}
