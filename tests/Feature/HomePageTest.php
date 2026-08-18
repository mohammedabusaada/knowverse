<?php

/**
 * Smoke test: the public landing page must render for guests without
 * authentication, since it is the entry point for unregistered visitors.
 */
it('renders the public home page for guests', function () {
    /** @var \Tests\TestCase $this */
    $this->get('/')->assertOk();
});
