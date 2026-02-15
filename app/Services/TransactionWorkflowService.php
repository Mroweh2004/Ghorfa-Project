<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Property;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class TransactionWorkflowService
{
    /**
     * Validate if a property is available for rental during specified dates
     */
    public function validatePropertyAvailability(Property $property, string $startDate, string $endDate): bool
    {
        // Property is considered AVAILABLE when there is NO blocking rental
        // transaction that overlaps with the requested date range.
        //
        // We look for existing rental transactions on this property whose
        // status blocks availability (see Transaction::scopeBlocksAvailability),
        // and whose date ranges overlap [startDate, endDate].

        return !$property->transactions()
            ->blocksAvailability()
            ->where(function ($q) use ($startDate, $endDate) {
                $q
                    // Existing booking starts inside requested range
                    ->whereBetween('start_date', [$startDate, $endDate])
                    // OR ends inside requested range
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    // OR fully covers the requested range
                    ->orWhere(function ($q2) use ($startDate, $endDate) {
                        $q2->where('start_date', '<=', $startDate)
                           ->where('end_date', '>=', $endDate);
                    });
            })
            ->exists();
    }

    /**
     * Create a new rental request (initial transaction in pending status)
     */
    public function createRentalRequest(
        int $userId,
        int $propertyId,
        string $startDate,
        string $endDate,
        bool $rulesAccepted = false,
        ?string $rulesExceptions = null,
        ?string $notes = null
    ): Transaction {
        $property = Property::findOrFail($propertyId);

        // Validate availability
        if (!$this->validatePropertyAvailability($property, $startDate, $endDate)) {
            throw ValidationException::withMessages([
                'dates' => 'Property is not available for the requested dates.',
            ]);
        }

        return Transaction::create([
            'user_id' => $userId,
            'property_id' => $propertyId,
            'type' => 'rent',
            'price' => $property->price,
            'currency' => 'USD', // Default, adjust as needed
            'status' => 'pending',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'rules_accepted' => $rulesAccepted,
            'rules_exceptions' => $rulesExceptions,
            'notes' => $notes,
        ]);
    }

    /**
     * Create a new purchase request (initial transaction in pending status)
     */
    public function createPurchaseRequest(
        int $userId,
        int $propertyId,
        ?string $notes = null
    ): Transaction {
        $property = Property::findOrFail($propertyId);

        return Transaction::create([
            'user_id' => $userId,
            'property_id' => $propertyId,
            'type' => 'buy',
            'price' => $property->price,
            'currency' => 'USD', // Default
            'status' => 'pending',
            'notes' => $notes,
        ]);
    }

    /**
     * Admin generates contract/transaction file
     * In real implementation, this would generate a PDF
     */
    public function generateContract(Transaction $transaction, string $filePath): bool
    {
        if (!$transaction->isPending()) {
            throw ValidationException::withMessages([
                'status' => 'Contract can only be generated for pending transactions.',
            ]);
        }

        // TODO: Implement actual PDF generation and storage
        // For now, we just store the path
        return $transaction->generateContract($filePath);
    }

    /**
     * Buyer reviews and approves the contract
     */
    public function approveContract(Transaction $transaction): bool
    {
        if (!$transaction->canBuyerApprove()) {
            throw ValidationException::withMessages([
                'status' => 'Contract must be generated before buyer can approve.',
            ]);
        }

        return $transaction->approveBuyerContract();
    }

    /**
     * Buyer rejects the contract (cancels transaction)
     */
    public function rejectContract(Transaction $transaction, string $reason = null): bool
    {
        if (!$transaction->isPending()) {
            throw ValidationException::withMessages([
                'status' => 'Can only reject contracts in pending status.',
            ]);
        }

        return $transaction->update([
            'status' => 'cancelled_by_buyer',
            'cancel_reason' => $reason,
        ]);
    }

    /**
     * Move transaction to confirmed after buyer approves
     */
    public function confirmTransaction(Transaction $transaction): bool
    {
        if (!$transaction->canBeConfirmed()) {
            throw ValidationException::withMessages([
                'status' => 'Buyer must approve contract before confirming.',
            ]);
        }

        return $transaction->toConfirmed();
    }

    /**
     * Seller/Landlord confirms payment received from buyer
     */
    public function confirmPayment(Transaction $transaction): bool
    {
        if (!$transaction->canBePaid()) {
            throw ValidationException::withMessages([
                'status' => 'Transaction must be confirmed before marking as paid.',
            ]);
        }

        return $transaction->confirmSellerPayment();
    }

    /**
     * Mark transaction as completed (rental period finished or sale/delivery complete)
     */
    public function completeTransaction(Transaction $transaction): bool
    {
        if (!$transaction->canBeCompleted()) {
            throw ValidationException::withMessages([
                'status' => 'Transaction must be paid and not already in refund process.',
            ]);
        }

        return $transaction->toCompleted();
    }

    /**
     * Buyer cancels before payment is made
     */
    public function buyerCancelBeforePayment(Transaction $transaction, string $reason = null): bool
    {
        if (!$transaction->canBuyerCancelBeforePayment()) {
            throw ValidationException::withMessages([
                'status' => 'Cannot cancel after payment has been made.',
            ]);
        }

        return $transaction->cancelByBuyer($reason);
    }

    /**
     * Seller/Landlord cancels before payment is made
     */
    public function sellerCancelBeforePayment(Transaction $transaction, string $reason = null): bool
    {
        if (!$transaction->canSellerCancelBeforePayment()) {
            throw ValidationException::withMessages([
                'status' => 'Can only cancel confirmed transactions before payment.',
            ]);
        }

        return $transaction->cancelBySeller($reason);
    }

    /**
     * Seller/Landlord requests refund (after payment was made)
     * This initiates the refund workflow
     */
    public function requestRefund(Transaction $transaction, string $reason = null): bool
    {
        if (!$transaction->canSellerInitiateRefund()) {
            throw ValidationException::withMessages([
                'status' => 'Can only request refund for paid transactions.',
            ]);
        }

        return $transaction->requestRefund($reason);
    }

    /**
     * Buyer confirms they received the refund
     * This completes the refund workflow
     */
    public function confirmRefundReceived(Transaction $transaction): bool
    {
        if (!$transaction->canBeRefunded()) {
            throw ValidationException::withMessages([
                'status' => 'Refund must be requested before confirmation.',
            ]);
        }

        return $transaction->confirmRefund();
    }

    /**
     * Get the current transaction workflow state as a descriptive string
     */
    public function getWorkflowState(Transaction $transaction): string
    {
        // State machine based on status and workflow fields
        if ($transaction->isRefunded()) {
            return 'REFUNDED';
        }

        if ($transaction->isCancelled()) {
            return strtoupper($transaction->status);
        }

        if ($transaction->isCompleted()) {
            return 'COMPLETED';
        }

        if ($transaction->isPaid()) {
            if ($transaction->isRefundRequested()) {
                return 'REFUND_REQUESTED';
            }
            return 'PAID';
        }

        if ($transaction->isConfirmed()) {
            return 'CONFIRMED';
        }

        if ($transaction->isPending()) {
            if ($transaction->isBuyerApproved()) {
                return 'BUYER_APPROVED';
            }
            if ($transaction->hasContractGenerated()) {
                return 'CONTRACT_READY_FOR_APPROVAL';
            }
            return 'PENDING';
        }

        return 'UNKNOWN';
    }

    /**
     * Get available actions for the current transaction state
     */
    public function getAvailableActions(Transaction $transaction): array
    {
        $actions = [];

        // Pending state actions
        if ($transaction->isPending() && !$transaction->hasContractGenerated()) {
            $actions[] = 'generate_contract'; // Admin action
        }

        if ($transaction->canBuyerApprove()) {
            $actions[] = 'approve_contract'; // Buyer action
            $actions[] = 'reject_contract'; // Buyer action
        }

        if ($transaction->canBeConfirmed()) {
            $actions[] = 'confirm_transaction'; // System/Admin action
        }

        if ($transaction->canBuyerCancelBeforePayment()) {
            $actions[] = 'buyer_cancel'; // Buyer action
        }

        if ($transaction->canSellerCancelBeforePayment()) {
            $actions[] = 'seller_cancel'; // Seller/Landlord action
        }

        // Confirmed state actions
        if ($transaction->canBePaid()) {
            $actions[] = 'confirm_payment'; // Seller/Landlord action
        }

        // Paid state actions
        if ($transaction->canBeCompleted()) {
            $actions[] = 'complete_transaction'; // System/Admin action
        }

        if ($transaction->canSellerInitiateRefund()) {
            $actions[] = 'request_refund'; // Seller/Landlord action
        }

        // Refund state actions
        if ($transaction->canBeRefunded()) {
            $actions[] = 'confirm_refund'; // Buyer action
        }

        return $actions;
    }
}
