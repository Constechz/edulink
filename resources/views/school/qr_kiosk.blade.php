@extends('layouts.app')

@section('title', 'Attendance QR Kiosk | EduLink')
@section('header_title', 'QR Attendance Check-In Kiosk')

@section('styles')
<style>
    .kiosk-container {
        max-width: 800px;
        margin: 0 auto;
    }

    .scanner-card {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: #ffffff;
        border-radius: 24px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        overflow: hidden;
        position: relative;
    }

    .scanner-target {
        width: 220px;
        height: 220px;
        border: 4px dashed var(--accent-color);
        border-radius: 20px;
        position: relative;
        margin: 2rem auto;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .scanner-line {
        position: absolute;
        width: 100%;
        height: 4px;
        background-color: var(--accent-color);
        top: 0;
        left: 0;
        box-shadow: 0 0 15px var(--accent-color);
        animation: scan 2.5s linear infinite;
    }

    @keyframes scan {
        0% { top: 0%; }
        50% { top: 100%; }
        100% { top: 0%; }
    }

    .scan-status-indicator {
        min-height: 120px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        transition: all 0.3s ease;
        padding: 1.5rem;
        background: rgba(255, 255, 255, 0.05);
    }

    .status-success {
        background: rgba(16, 185, 129, 0.15) !important;
        border: 1px solid #10b981;
        color: #34d399;
        animation: pulse-green 1.5s infinite;
    }

    .status-warning {
        background: rgba(245, 158, 11, 0.15) !important;
        border: 1px solid #f59e0b;
        color: #fbbf24;
        animation: pulse-yellow 1.5s infinite;
    }

    .status-danger {
        background: rgba(239, 68, 68, 0.15) !important;
        border: 1px solid #ef4444;
        color: #f87171;
    }

    @keyframes pulse-green {
        0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); }
        70% { box-shadow: 0 0 0 15px rgba(16, 185, 129, 0); }
        100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }

    @keyframes pulse-yellow {
        0% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.4); }
        70% { box-shadow: 0 0 0 15px rgba(245, 158, 11, 0); }
        100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); }
    }

    .digital-clock {
        font-family: 'Courier New', monospace;
        font-size: 2.2rem;
        font-weight: 700;
        letter-spacing: 2px;
        color: var(--accent-color);
    }
</style>
@endsection

@section('content')
<div class="container-fluid p-0 kiosk-container">
    
    <div class="text-center mb-4">
        <a href="{{ route('school.attendance') }}" class="btn btn-sm btn-outline-secondary float-start">
            <i class="bi bi-arrow-left me-1"></i> Back to Manual Register
        </a>
        <h4 class="mb-1 font-weight-bold" style="font-weight: 700;">Check-In Terminal</h4>
        <p class="text-muted small">Place student ID card under barcode reader or enter ID below to scan.</p>
    </div>

    <!-- Scanner Device Card -->
    <div class="scanner-card p-5 text-center mb-4">
        <div class="mb-3">
            <div id="clockDisplay" class="digital-clock">00:00:00 AM</div>
            <div class="text-muted small">{{ date('l, d F Y') }}</div>
        </div>

        <div class="scanner-target">
            <div class="scanner-line"></div>
            <i class="bi bi-qr-code-scan display-1 text-secondary opacity-25"></i>
        </div>

        <!-- Input Handler (Auto Focused) -->
        <div class="col-md-6 mx-auto mb-4">
            <form id="scanForm" autocomplete="off">
                @csrf
                <div class="input-group">
                    <input type="text" id="student_id_number" name="student_id_number" class="form-control text-center py-2.5" placeholder="Scanning/Inputting Student ID..." autofocus required>
                    <button class="btn btn-warning px-4" type="submit" id="submitBtn">
                        <i class="bi bi-send-fill"></i> Check In
                    </button>
                </div>
            </form>
        </div>

        <!-- Real-Time Notification Container -->
        <div class="col-md-8 mx-auto mt-2">
            <div id="statusIndicator" class="scan-status-indicator">
                <i class="bi bi-upc-scan display-6 mb-2"></i>
                <div class="fw-bold" id="statusMessage">READY TO SCAN</div>
                <div class="small text-secondary" id="statusDetails">Kiosk input field auto-focused for hardware scans.</div>
            </div>
        </div>
    </div>

    <!-- Demo Mock Scans Panel (For developer testing and manual testing) -->
    <div class="glass-card p-4">
        <h6 class="font-weight-bold mb-3"><i class="bi bi-terminal me-2"></i>Developer Simulation & Testing Console</h6>
        <div class="row g-2">
            <div class="col-sm-6">
                <label class="form-label small">Sample Student ID Number</label>
                <div class="input-group">
                    <input type="text" id="demo_id_input" class="form-control" placeholder="e.g. STU-2026-001">
                    <button class="btn btn-secondary" onclick="simulateScan()">Simulate Scan</button>
                </div>
            </div>
            <div class="col-sm-6">
                <label class="form-label small">Frequently Scanned Demo IDs</label>
                <div class="d-flex flex-wrap gap-2">
                    <button class="btn btn-sm btn-outline-dark" onclick="fillAndScan('STU-2026-0001')">STU-2026-0001</button>
                    <button class="btn btn-sm btn-outline-dark" onclick="fillAndScan('STU-DEMO-001')">STU-DEMO-001</button>
                    <button class="btn btn-sm btn-outline-dark" onclick="fillAndScan('INVALID-ID')">INVALID-ID</button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const scanForm = document.getElementById('scanForm');
        const idInput = document.getElementById('student_id_number');
        const statusIndicator = document.getElementById('statusIndicator');
        const statusMessage = document.getElementById('statusMessage');
        const statusDetails = document.getElementById('statusDetails');
        const clockDisplay = document.getElementById('clockDisplay');

        // Keeping input focused at all times (needed for hardware barcode scan reader)
        idInput.focus();
        document.addEventListener('click', () => {
            idInput.focus();
        });

        // Digital Clock Live Updates
        function updateClock() {
            const now = new Date();
            let hours = now.getHours();
            let minutes = now.getMinutes();
            let seconds = now.getSeconds();
            const ampm = hours >= 12 ? 'PM' : 'AM';
            
            hours = hours % 12;
            hours = hours ? hours : 12; // hour '0' should be '12'
            minutes = minutes < 10 ? '0' + minutes : minutes;
            seconds = seconds < 10 ? '0' + seconds : seconds;
            
            clockDisplay.textContent = `${hours}:${minutes}:${seconds} ${ampm}`;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Submit form handler via AJAX
        scanForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const idNumber = idInput.value.trim();
            if (!idNumber) return;

            processCheckIn(idNumber);
        });

        function processCheckIn(idNumber) {
            statusIndicator.className = "scan-status-indicator";
            statusMessage.textContent = "PROCESSING...";
            statusDetails.textContent = "Verifying credentials with database records...";

            const csrfToken = document.querySelector('input[name="_token"]').value;

            fetch("{{ route('school.attendance.qr-checkin') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ student_id_number: idNumber })
            })
            .then(async response => {
                const data = await response.json();
                if (response.ok && data.success) {
                    showSuccessStatus(data);
                } else {
                    showErrorStatus(data.error || "Failed to process check-in.");
                }
            })
            .catch(err => {
                showErrorStatus("Network Connection Interrupted. Unable to reach school host.");
            })
            .finally(() => {
                idInput.value = '';
                idInput.focus();
            });
        }

        function showSuccessStatus(data) {
            statusIndicator.className = "scan-status-indicator";
            
            if (data.status === 'Late') {
                statusIndicator.classList.add('status-warning');
                statusMessage.innerHTML = `<i class="bi bi-clock-history me-2"></i>LATE: ${data.student_name}`;
                statusDetails.innerHTML = `Checked In at <strong>${data.time}</strong> (${data.late_minutes} minutes past arrival threshold of 08:30 AM).`;
            } else {
                statusIndicator.classList.add('status-success');
                statusMessage.innerHTML = `<i class="bi bi-check-circle-fill me-2"></i>ON TIME: ${data.student_name}`;
                statusDetails.innerHTML = `Checked In successfully at <strong>${data.time}</strong>. Attendance register updated.`;
            }

            // Return to ready state after 5 seconds
            setTimeout(resetStatus, 5000);
        }

        function showErrorStatus(message) {
            statusIndicator.className = "scan-status-indicator status-danger";
            statusMessage.innerHTML = `<i class="bi bi-x-circle-fill me-2"></i>SCAN ERROR`;
            statusDetails.textContent = message;

            setTimeout(resetStatus, 6000);
        }

        function resetStatus() {
            // Only reset if currently showing success or error state
            if (statusMessage.textContent !== "READY TO SCAN" && statusMessage.textContent !== "PROCESSING...") {
                statusIndicator.className = "scan-status-indicator";
                statusMessage.innerHTML = `<i class="bi bi-upc-scan display-6 mb-2"></i>READY TO SCAN`;
                statusDetails.textContent = "Kiosk input field auto-focused for hardware scans.";
            }
        }
    });

    // Simulated scanner trigger
    function simulateScan() {
        const val = document.getElementById('demo_id_input').value.trim();
        if (val) {
            document.getElementById('student_id_number').value = val;
            document.getElementById('scanForm').dispatchEvent(new Event('submit'));
        }
    }

    function fillAndScan(id) {
        document.getElementById('demo_id_input').value = id;
        simulateScan();
    }
</script>
@endsection
