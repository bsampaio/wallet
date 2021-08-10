<?php

namespace App\Jobs;

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

class ProcessAuthorizedWithdraw implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    protected $withdraw;

    /**
     * Create a new job instance.
     * @param $withdraw
     * @return void
     */
    public function __construct(Withdraw $withdraw)
    {
        $this->withdraw = $withdraw;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $service = new TransferService();
        $service->processAuthorizedWithdraw($this->withdraw);
    }


    public function middleware(): array
    {
        return [new WithoutOverlapping($this->withdraw->id)];
    }
}
