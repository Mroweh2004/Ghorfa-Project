<?php

namespace App\Console\Commands;

use App\Models\Transaction;
use Illuminate\Console\Command;

class CompleteOverdueTransactions extends Command
{
    protected $signature = 'transactions:complete-overdue';

    protected $description = 'Mark transactions as completed when their end date has passed (status: paid, end_date < today)';

    public function handle(): int
    {
        $query = Transaction::eligibleForAutoComplete();
        $count = $query->count();

        if ($count === 0) {
            $this->info('No overdue transactions to complete.');
            return self::SUCCESS;
        }

        $completed = 0;
        foreach ($query->get() as $transaction) {
            if ($transaction->toCompleted()) {
                $completed++;
                $this->line("Completed transaction #{$transaction->id} (property: {$transaction->property_id}, end_date: {$transaction->end_date}).");
            }
        }

        $this->info("Marked {$completed} transaction(s) as completed.");
        return self::SUCCESS;
    }
}
