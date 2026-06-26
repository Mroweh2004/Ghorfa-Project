<div class="request-details-content">
    <h3 class="request-details-title"><i class="fas fa-clipboard-list"></i> Request Details</h3>

    @include('landlord.partials.request-details-body', ['transaction' => $transaction, 'forPdf' => false])

    <div class="request-details-actions">
        <button type="button" class="btn btn-primary" onclick="generateAndSendContract({{ $transaction->id }})">
            <i class="fas fa-file-pdf"></i> Generate Contract & Send
        </button>
        <button type="button" class="btn btn-secondary" onclick="exportRequestDetailsPdf({{ $transaction->id }})">
            <i class="fas fa-file-pdf"></i> Export PDF
        </button>
        <button type="button" class="btn btn-secondary" onclick="downloadTransactionReport({{ $transaction->id }})">
            <i class="fas fa-download"></i> Download Report
        </button>
        <a href="{{ url('/transactions/' . $transaction->id . '/edit') }}" class="btn btn-secondary" target="_blank">
            <i class="fas fa-edit"></i> Edit Request
        </a>
        <button type="button" class="btn btn-secondary" onclick="closeRequestDetailsModal()">Close</button>
    </div>
</div>
