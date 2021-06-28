<?php

namespace App\Console\Commands;

use App\Jobs\ProcessWaitingTransaction;
use App\Models\Transaction;
use Illuminate\Bus\Batch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class ProcessWaitingTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transactions.waiting:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
    public function handle(): int
    {
        $now = now();
        $start = $now->format('d/m/Y H:i:s');
        $this->info("Starting compensation of waiting transactions at {$start}");
        $waiting = Transaction::waitingCompensation($now)->get();

        foreach($waiting as $w) {
            dispatch(new ProcessWaitingTransaction($w));
        }

        $this->info('All compensation jobs have been dispatched.');
        return 0;
    }
}
