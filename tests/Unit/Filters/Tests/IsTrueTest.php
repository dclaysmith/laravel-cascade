<?php

use Dclaysmith\LaravelCascade\Filters\Base as Filter;
use Workbench\App\Models\User;

test('Creates correct sql', function () {

    $prefix = config('cascade.database.tablePrefix');

    $filter = Filter::boolean('a_column')
        ->isFalse();

    $builder = User::query();

    $filter->test->applyFilter($builder, $filter->target, 'and');

    expect($builder->toSql())->toBe(
        sprintf('select * from "users" where a_column::boolean = ?', $prefix)
    );

    expect(collect($builder->getBindings())->first())->toBeFalse();

});