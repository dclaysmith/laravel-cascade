<?php

declare(strict_types=1);

namespace Dclaysmith\LaravelCascade\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Container\Attributes\Config;
use Illuminate\Contracts\Console\Isolatable;
use Illuminate\Support\Facades\DB;

final class DropPartitions extends Command implements Isolatable
{
    /**
     * @var string
     *
     * vendor/bin/testbench cascade:drop-partition lc_events_rollup_1day public week '2024-10-01'
     */
    protected $signature = 'cascade:drop-partitions
                            {existing_table : The table to be partitioned}
                            {schema : The Postgres Schema of partition}
                            {interval : The interval of the partition}
                            {prior_to_date : Delete partitions prior to this date}';

    /**
     * @var string
     */
    protected $description = 'Drop a time-based partitions of the rollup tables (or any table).';

    /**
     * Create a new command instance.
     */
    public function __construct(
        #[Config('cascade.database.tablePrefix')] protected ?string $prefix = null,
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {

        $existingTable = $this->argument('existing_table');
        $schema = $this->argument('schema');
        $interval = $this->argument('interval');
        $priorToDate = $this->argument('prior_to_date');

        $startDate = Carbon::parse($priorToDate);

        $select = sprintf(
            "SELECT table_schema, table_name FROM information_schema.tables WHERE table_schema = '%s' AND table_name ILIKE '%s_%s%%' and table_name < '%s_%s_%s'",
            ...[
                $schema,
                $existingTable,
                $interval,
                $existingTable,
                $interval,
                preg_replace('/[^A-Za-z0-9 ]/', '', $startDate->format('YmdHis')),
            ]
        );

        $tables = DB::select($select);

        foreach ($tables as $table) {
            $sql = "DROP TABLE IF EXISTS $table->table_schema.$table->table_name";
            DB::unprepared($sql);
        }
    }
}
