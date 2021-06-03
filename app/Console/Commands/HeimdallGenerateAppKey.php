<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class HeimdallGenerateAppKey extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'heimdall:key';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates a random key for external app access';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $bytes = random_bytes(20);
        $key = bin2hex($bytes);
        $this->newLine(1);
        $this->info('The key was sucessfully generated: ' . $key);
        return 0;
    }
}
