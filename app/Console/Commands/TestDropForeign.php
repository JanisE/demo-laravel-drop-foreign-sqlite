<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TestDropForeign extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:foreignDrop';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
		Schema::enableForeignKeyConstraints();

		Schema::create('referenced_table', function (Blueprint $table) {
			$table->increments('id');
		});

		Schema::create('taken_tests', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('test_class_id')->unsigned();
			$table->integer('prev_test_class_id')->unsigned();
			$table->string('result_value', 16)->unique();
			$table->string('result_value2', 16);

			$table->unique(['result_value2'], 'idx_name_2');
			$table->foreign('test_class_id')->references('id')->on('referenced_table');
			// The name parameter has no effect.
			$table->foreign('prev_test_class_id', 'fk_previous_class')->references('id')->on('referenced_table');
		});

		var_export(DB::select('SELECT `sql` FROM sqlite_master where tbl_name = "taken_tests"'));
		print "\n\n";

		try {
			DB::insert('INSERT INTO taken_tests VALUES (1, 2, 3, "a", "b")');
		}
		catch (\Illuminate\Database\QueryException $exception) {
			print $exception->getMessage() . "\n\n";
		}

		Schema::table('taken_tests', function($table)
		{
			// These have no effect:
			$table->dropForeign(['prev_test_class_id']); // Auto-built index name.
			$table->dropForeign('taken_tests_test_class_id_foreign'); // Auto-built index name given manually.
			$table->dropForeign('fk_previous_class'); // Specific index name.

			// These do work:
			$table->dropUnique(['result_value']); // Auto-built index name.
			$table->dropUnique('idx_name_2'); // Specific index name.
		});

		var_export(DB::select('SELECT `sql` FROM sqlite_master where tbl_name = "taken_tests"'));
		print "\n\n";

		try {
			DB::insert('INSERT INTO taken_tests VALUES (1, 2, 3, "a", "b")');
		}
		catch (\Illuminate\Database\QueryException $exception) {
			print "This exception should not happen if the foreign keys are properly dropped:\n";
			print $exception->getMessage() . "\n\n";
		}
    }
}
