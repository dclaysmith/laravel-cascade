<?php

use Dclaysmith\LaravelCascade\Repositories\PostgresAnalyticsRepository;

it('can be instantiated', function () {
    $repository = new PostgresAnalyticsRepository;
    expect($repository)->toBeInstanceOf(PostgresAnalyticsRepository::class);
});
