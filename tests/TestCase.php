<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Test tidak butuh aset hasil `npm run build` (public/build/manifest.json).
        $this->withoutVite();
    }
}
