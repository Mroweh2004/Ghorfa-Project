# Transaction Workflow Documentation

## Overview
This document describes the complete transaction workflow for both **Renting** and **Selling** properties in the Ghorfa system.

## Database Schema
All transactions have the following key fields:
- `status` - Current transaction status
- `type` - Transaction type: 'buy' or 'rent'
- `contract_path` - Path to the generated contract/PDF
- `contract_generated_at` - When admin generated the contract
- `buyer_approved_at` - When buyer approved the contract
- `seller_payment_confirmed_at` - When seller confirmed payment received
- `paid_at` - When payment was marked as complete
- `refund_requested_at` - When seller requested a refund (after payment)
- `refund_confirmed_by_buyer_at` - When buyer confirmed refund receipt
- `cancel_reason` - Reason for cancellation or refund
- `rules_accepted` - Whether buyer accepted property rules
- `rules_exceptions` - What rules the buyer didn't accept

## Renting Workflow

### Status Progression
```
pending → confirmed → paid → completed
   ↓         ↓         ↓
cancelled_by_buyer / cancelled_by_seller / refunded
```

### Detailed Flow

#### Stage 1: Initial Request (User submits to Landlord)
1. **User submits rental request:**
   - Specifies: `start_date`, `end_date`, `duration`
   - Accepts/Rejects: property rules
   - Provides: `rules_exceptions` (what they didn't accept), `notes`
   - Status: `pending`

2. **System validates:**
   - Property availability for requested dates
   - Rule compliance
   - Date format and logic

#### Stage 2: Admin Review & Contract Generation
1. **Admin reviews request:**
   - Checks property availability again
   - Validates dates and rules
   - Generates legal transaction document (contract PDF)
   - Sets: `contract_path`, `contract_generated_at`

#### Stage 3: Buyer Approval or Rejection
1. **Buyer downloads & reviews contract**

2. **Option A: Buyer Accepts Contract**
   - Sets: `buyer_approved_at = now()`
   - Status remains: `pending`
   - Next: System can confirm transaction

3. **Option B: Buyer Rejects Contract**
   - Sets: `status = 'cancelled_by_buyer'`
   - Sets: `cancel_reason` (optional)
   - Workflow ends

#### Stage 4: Transaction Confirmation
- After buyer approves, admin/system moves to: `status = 'confirmed'`
- Buyer now needs to pay landlord (offline process)

#### Stage 5: Payment & Seller Confirmation
1. **Buyer pays landlord** (outside system - cash, bank transfer, etc.)
2. **Landlord confirms payment:**
   - Sets: `seller_payment_confirmed_at = now()`
   - Sets: `paid_at = now()`
   - Sets: `status = 'paid'`

#### Stage 6: Rental Completion
1. **Rental period ends or landlord marks complete**
   - Sets: `status = 'completed'`
   - Workflow ends successfully

#### Refund Path (only available if already paid)
1. **Landlord might cancel after payment received:**
   - Sets: `refund_requested_at = now()`
   - Sets: `cancel_reason` (reason for cancellation)
   - Status remains: `paid` (not yet refunded)

2. **Landlord refunds buyer** (outside system)

3. **Buyer confirms refund receipt:**
   - Sets: `refund_confirmed_by_buyer_at = now()`
   - Sets: `status = 'refunded'`
   - Workflow ends

#### Cancellation Before Payment
- **Buyer can cancel:** if status is `pending` or `confirmed` (before payment)
  - Sets: `status = 'cancelled_by_buyer'`
  - Workflow ends
- **Landlord can cancel:** if status is `confirmed` (before payment confirmed)
  - Sets: `status = 'cancelled_by_seller'`
  - Workflow ends

---

## Selling Workflow

### Status Progression (same as renting)
```
pending → confirmed → paid → completed
   ↓         ↓         ↓
cancelled_by_buyer / cancelled_by_seller / refunded
```

### Detailed Flow

#### Stage 1: Initial Request (Buyer submits offer)
1. **Buyer submits purchase request:**
   - Specifies: property to buy
   - Provides: optional notes/offer details
   - Status: `pending`

#### Stage 2: Admin Review & Contract Generation
1. **Admin reviews request**
2. **Admin generates contract** (legal document for sale)
   - Sets: `contract_path`, `contract_generated_at`

#### Stage 3: Buyer Approval or Rejection
1. **Buyer reviews contract**
2. **Option A: Buyer Accepts**
   - Sets: `buyer_approved_at = now()`
3. **Option B: Buyer Rejects**
   - Sets: `status = 'cancelled_by_buyer'`

#### Stage 4: Transaction Confirmation
- After buyer approves: `status = 'confirmed'`

#### Stage 5: Seller Confirmation of Payment
1. **Buyer pays seller** (outside system)
2. **Seller confirms payment received:**
   - Sets: `seller_payment_confirmed_at = now()`
   - Sets: `paid_at = now()`
   - Sets: `status = 'paid'`

#### Stage 6: Completion
- Admin marks: `status = 'completed'`
- Workflow ends

#### Refund Path (if seller cancels after payment)
1. **Seller cancels after receiving payment:**
   - Sets: `refund_requested_at = now()`
   - Cancel reason provided
2. **Seller refunds buyer** (outside system)
3. **Buyer confirms refund:**
   - Sets: `refund_confirmed_by_buyer_at = now()`
   - Sets: `status = 'refunded'`

---

## Implementation in Code

### Service Layer
Use `TransactionWorkflowService` to manage workflow:

```php
$service = new TransactionWorkflowService();

// Create request
$transaction = $service->createRentalRequest(
    userId: $userId,
    propertyId: $propertyId,
    startDate: '2026-03-01',
    endDate: '2026-03-31',
    rulesAccepted: true,
    rulesExceptions: 'No loud music',
    notes: 'Looking for quiet place'
);

// Admin generates contract
$service->generateContract($transaction, '/path/to/contract.pdf');

// Buyer approves
$service->approveContract($transaction);

// Confirm transaction
$service->confirmTransaction($transaction);

// Seller confirms payment
$service->confirmPayment($transaction);

// Complete transaction
$service->completeTransaction($transaction);
```

### Transaction Model State Checks
```php
$transaction->isPending();              // Current status is pending
$transaction->isConfirmed();            // Current status is confirmed
$transaction->isPaid();                 // Current status is paid
$transaction->isCompleted();            // Current status is completed
$transaction->isCancelled();            // Status is cancelled_by_* or refunded
$transaction->hasContractGenerated();   // Contract file exists
$transaction->isBuyerApproved();        // Buyer has approved contract
$transaction->isRefundRequested();      // Refund process started
$transaction->isRefundConfirmed();      // Buyer confirmed refund receipt
```

### Available Actions for UI
```php
$service = new TransactionWorkflowService();
$actions = $service->getAvailableActions($transaction);
// Returns array like: ['generate_contract', 'approve_contract', 'reject_contract']
```

---

## Key Business Rules

1. **Contract must be generated before buyer can approve**
2. **Buyer must approve before transaction can be confirmed**
3. **Seller/Landlord must confirm payment before marking paid**
4. **Refunds can only be requested after payment is made**
5. **Before payment, either party can cancel**
6. **Rental dates must not overlap with existing confirmed/paid transactions**
7. **Dates must have end_date after start_date**

---

## Status Values
- `pending` - Initial state, awaiting admin review and contract generation
- `confirmed` - Buyer approved, awaiting payment
- `paid` - Payment confirmed, transaction active
- `completed` - Transaction finished successfully
- `cancelled_by_buyer` - Buyer cancelled (before payment)
- `cancelled_by_seller` - Seller cancelled (before payment, or after payment = refund)
- `refunded` - Refund was requested and confirmed by buyer

---

## Migration Fields
All transaction workflow fields are stored in a single `transactions` table. See migration datetime stamps for when each feature was added.
