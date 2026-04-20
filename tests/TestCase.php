<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        // Keep localized UI assertions deterministic across environments.
        $this->withHeader('Accept-Language', config('app.locale', 'es'));
    }
}
