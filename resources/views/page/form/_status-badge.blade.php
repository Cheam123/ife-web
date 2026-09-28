@if($status === 'pending')
    <span class="badge bg-warning text-dark px-3 py-2">
        <i class="mdi mdi-clock-outline me-1"></i>Pending
    </span>
@elseif($status === 'approved')
    <span class="badge bg-success px-3 py-2">
        <i class="mdi mdi-check-circle-outline me-1"></i>Approved
    </span>
@elseif($status === 'rejected')
    <span class="badge bg-danger px-3 py-2">
        <i class="mdi mdi-close-circle-outline me-1"></i>Rejected
    </span>
@elseif($status === 'closed')
    <span class="badge px-3 py-2" style="background:#495057;">
        <i class="mdi mdi-lock-outline me-1"></i>Closed
    </span>
@elseif($status === 'cancelled')
    <span class="badge px-3 py-2" style="background:#6c757d;">
        <i class="mdi mdi-cancel me-1"></i>Cancelled
    </span>
@else
    <span class="badge bg-secondary px-3 py-2">{{ ucfirst($status) }}</span>
@endif
