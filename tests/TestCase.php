<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Use an isolated compiled-view path per test run to avoid Windows file-lock collisions.
        $compiledPath = storage_path('framework/views/testing/'.bin2hex(random_bytes(8)));

        if (! is_dir($compiledPath)) {
            mkdir($compiledPath, 0777, true);
        }

        config(['view.compiled' => $compiledPath]);

        $bladeCompiler = app('blade.compiler');
        if (method_exists($bladeCompiler, 'setCompiledPath')) {
            $bladeCompiler->setCompiledPath($compiledPath);
        }
    }
}
