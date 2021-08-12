<?php

namespace App\Jobs;

use App\Models\Transfer;
use App\Models\Withdraw;
use App\Services\TransferService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;

class ProcessAuthorizedTransfer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    protected $transfer;

    /**
     * Create a new job instance.
     * @param Transfer $transfer
     * @return void
     */
    public function __construct(Transfer $transfer)
    {
        $this->transfer = $transfer;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $service = new TransferService();
        $service->processAuthorizedTransfer($this->transfer);
    }


    public function middleware(): array
    {
        return [new WithoutOverlapping($this->transfer->id)];
    }
}
