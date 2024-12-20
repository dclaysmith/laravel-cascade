<?php

use Dclaysmith\LaravelCascade\Filters\Base as Filter;
use Workbench\App\Models\User;

use function Pest\Faker\fake;

test('Creates correct sql for date', function () {

    $prefix = config('cascade.database.tablePrefix');

    $date = fake()->date();

    $filter = Filter::date('a_column')
        ->willOccurAfter($date);

    $builder = User::query();

    $filter->test->applyFilter($builder, $filter->target, 'and');

    expect($builder->toSql())->toBe(
        sprintf('select * from "users" where (a_column::date > ? and a_column::date > ?)', $prefix)
    );

    expect(collect($builder->getBindings())->first())->toBe($date);

    expect(Carbon\Carbon::createFromDate(collect($builder->getBindings())->last())->toDateString())->toEqual(Carbon\Carbon::today()->toDateString());
});

test('Creates correct sql for datetime', function () {

    $prefix = config('cascade.database.tablePrefix');

    $datetime = fake()->datetime();

    $filter = Filter::datetime('a_column')
        ->willOccurAfter($datetime);

    $builder = User::query();

    $filter->test->applyFilter($builder, $filter->target, 'and');

    expect($builder->toSql())->toBe(
        sprintf('select * from "users" where (a_column::datetime > ? and a_column::datetime > ?)', $prefix)
    );

    expect(collect($builder->getBindings())->first())->toBe($datetime);

    // This is testing 'now' but we can only test on the date not the datetime
    expect(Carbon\Carbon::createFromDate(collect($builder->getBindings())->last())->toDateString())->toEqual(Carbon\Carbon::today()->toDateString());
});
