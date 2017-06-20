<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BorrowOrder;
use Log;

class OrderCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Order check, such as network timeout';

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
     * @return mixed
     */
    public function handle() {
        $timeout = env('ORDER_NETWORK_TIMEOUT', 30); //30s
		$orders = BorrowOrder::where('status', BorrowOrder::ORDER_STATUS_PAID)->where('borrow_time', '<', time()-$timeout)->get();

		// 网络超时退款到用户账户
		foreach ($orders as $order) {
			$order->revertPaidOrder(BorrowOrder::ORDER_SUB_STATUS_FAIL_NETWORK_TIMEOUT);
		}
    }
}
