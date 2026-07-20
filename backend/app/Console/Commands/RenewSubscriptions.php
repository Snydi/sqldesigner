<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\SubscriptionPaymentService;
use Illuminate\Console\Command;

class RenewSubscriptions extends Command
{
    protected $signature = 'subscriptions:renew';

    protected $description = 'Initiate due recurring Pro subscription payments.';

    public function handle(SubscriptionPaymentService $payments): int
    {
        $this->info('Initiated '.$payments->renewDueSubscriptions().' subscription renewal(s).');

        return self::SUCCESS;
    }
}
