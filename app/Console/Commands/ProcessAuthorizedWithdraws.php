<?php

namespace App\Console\Commands;

use App\Models\Withdraw;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProcessAuthorizedWithdraws extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'withdraws.authorized:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process all open authorized withdraws.';

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
        $now = now();
        $start = $now->format('d/m/Y H:i:s');
        $this->info("Starting processing of authorized withdraws at {$start}");
        $waiting = Withdraw::authorized()->unprocessed()->get();

        foreach($waiting as $w) {
            dispatch(new ProcessWaitingTransaction($w));
        }

        $this->info('All compensation jobs have been dispatched.');
        return 0;
    }
}
