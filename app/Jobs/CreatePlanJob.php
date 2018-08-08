<?php

namespace App\Jobs;

use App\Repositories\PlanRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

//use Illuminate\Foundation\Bus\Dispatchable;

class CreatePlanJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $data;
    protected $planRepo;

    public function __construct( $data, $planRepo)
    {
        $this->data     = $data;
        $this->planRepo = $planRepo;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
       $this->planRepo->createPlan($this->data);
    }

}
