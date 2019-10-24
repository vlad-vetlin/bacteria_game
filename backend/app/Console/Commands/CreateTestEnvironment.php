<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Kernel;

/**
 * @property string testing_folder
 * @property string db_name
 */
class CreateTestEnvironment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'prepare:tests';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prepares a special testing environment to speed up testing';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $this->testing_folder = base_path('tests');

        $this->createTestDatabase();
        $this->migrateTestDatabase();
        $this->seedTestDatabase();

        $this->info('Done at ' . $this->testing_folder . '/test.db');

        return;
    }

    private function createTestDatabase() {
        $this->db_name = $this->testing_folder . '/test.db';

        if (file_exists($this->db_name)) {
            unlink($this->db_name);
        }
        touch($this->db_name);
    }

    private function migrateTestDatabase() {
        return app()[Kernel::class]->call('migrate:fresh', [
            "--database" => "test"
        ]);
    }

    private function seedTestDatabase() {
        // return app()[Kernel::class]->call('db:seed', ['--class' => \PrivilegesSeeder::class]);
    }
}
