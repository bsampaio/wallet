<?php

namespace App\Console\Commands;

use App\Jobs\ProcessAuthorizedTransfer;
use App\Models\Transfer;
use App\Models\Withdraw;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProcessAuthorizedTransfers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transfers.authorized:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process all open authorized transfers.';

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
        $this->info("Starting processing of authorized transfers at {$start}");
        $transfers = Transfer::authorized()->unprocessed()->get();

        foreach($transfers as $t) {
            dispatch(new ProcessAuthorizedTransfer($t));
        }

        $this->info('All compensation jobs have been dispatched.');
        return 0;
    }
}
