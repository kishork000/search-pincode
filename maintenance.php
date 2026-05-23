<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>System Maintenance - Pincode Serviceability</title>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: "Segoe UI", -apple-system, BlinkMacSystemFont, "Helvetica Neue", sans-serif;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        padding: 0;
        overflow-x: hidden;
        margin: 0;
    }

    .maintenance-wrapper {
        width: 100%;
        max-width: none;
    }

    .maintenance-container {
        background: white;
        border-radius: 0;
        box-shadow: none;
        overflow: hidden;
        animation: slideUp 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
        height: 100vh;
        display: flex;
        flex-direction: column;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(50px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.5rem 2rem;
        text-align: center;
        flex-shrink: 0;
    }

    .status-badge {
        display: inline-block;
        background: rgba(255, 255, 255, 0.25);
        backdrop-filter: blur(10px);
        color: white;
        padding: 0.4rem 0.8rem;
        border-radius: 30px;
        font-weight: 600;
        font-size: 0.75rem;
        margin-bottom: 0.5rem;
        border: 1px solid rgba(255, 255, 255, 0.3);
        letter-spacing: 0.5px;
    }

    .icon {
        font-size: 2.5rem;
        margin-bottom: 0.5rem;
        animation: bounce 2s ease-in-out infinite;
    }

    @keyframes bounce {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-10px);
        }
    }

    .header h1 {
        font-size: 1.5rem;
        margin-bottom: 0.25rem;
        font-weight: 700;
    }

    .header p {
        font-size: 0.85rem;
        opacity: 0.9;
        line-height: 1.4;
    }

    .content {
        padding: 1.5rem 2rem;
        background: #fafafa;
        flex: 1;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .message-box {
        background: white;
        border-left: 5px solid #667eea;
        padding: 1rem;
        margin-bottom: 0;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.1);
    }

    .message-box h3 {
        color: #333;
        margin-bottom: 0.4rem;
        font-size: 0.95rem;
    }

    .message-box p {
        color: #666;
        line-height: 1.5;
        font-size: 0.85rem;
    }

    .details-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 0;
    }

    .detail-item {
        background: white;
        padding: 1rem;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        border-top: 3px solid #667eea;
    }

    .detail-item h4 {
        color: #667eea;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        margin-bottom: 0.3rem;
    }

    .detail-item p {
        color: #333;
        font-size: 0.9rem;
        font-weight: 500;
    }

    .department-info {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
        border-radius: 8px;
        padding: 1rem;
        margin: 0;
        border: 2px solid rgba(102, 126, 234, 0.1);
        text-align: center;
    }

    .department-logo {
        font-size: 1.5rem;
        margin-bottom: 0.3rem;
    }

    .department-name {
        color: #667eea;
        font-size: 0.95rem;
        font-weight: 700;
        margin-bottom: 0.2rem;
        letter-spacing: 0.3px;
    }

    .department-subtitle {
        color: #999;
        font-size: 0.75rem;
        margin-bottom: 0.5rem;
    }

    .department-message {
        color: #666;
        line-height: 1.5;
        font-size: 0.8rem;
    }

    .progress-container {
        margin: 0;
    }

    /* Circular Timer Styles */
    .timer-section {
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 0.3rem 0;
    }

    .circular-timer {
        position: relative;
        width: 120px;
        height: 120px;
        margin: 0 auto;
    }

    .timer-circle-bg {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background: white;
        box-shadow: 0 4px 20px rgba(102, 126, 234, 0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .timer-circle-progress {
        position: absolute;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background: conic-gradient(from 0deg, #667eea 0deg, #667eea 0deg, transparent 0deg);
        animation: rotateProgress 60s linear infinite;
    }

    @keyframes rotateProgress {
        from {
            background: conic-gradient(from 0deg, #667eea 0deg, #667eea 0deg, transparent 0deg);
        }

        to {
            background: conic-gradient(from 0deg, #667eea 0deg, #667eea 360deg, transparent 360deg);
        }
    }

    .timer-inner-circle {
        position: absolute;
        width: 110px;
        height: 110px;
        border-radius: 50%;
        background: white;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 1;
    }

    .timer-display {
        font-size: 1.8rem;
        font-weight: 700;
        color: #667eea;
        text-align: center;
        line-height: 1;
        margin-bottom: 0;
    }

    .timer-label {
        font-size: 0.65rem;
        color: #999;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .progress-label {
        color: #333;
        font-weight: 600;
        margin-bottom: 0.3rem;
        font-size: 0.8rem;
    }

    .progress-bar {
        width: 100%;
        height: 6px;
        background: #e0e0e0;
        border-radius: 10px;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        border-radius: 10px;
        animation: loading 2s ease-in-out infinite;
    }

    @keyframes loading {

        0%,
        100% {
            width: 0%;
        }

        50% {
            width: 100%;
        }
    }

    .features {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.6rem;
        margin: 0.3rem 0;
    }

    .feature {
        display: flex;
        align-items: flex-start;
        gap: 0.5rem;
        padding: 0.6rem;
        background: white;
        border-radius: 6px;
        border: 1px solid #f0f0f0;
    }

    .feature-icon {
        font-size: 1.2rem;
        color: #667eea;
        flex-shrink: 0;
        margin-top: 0.1rem;
    }

    .feature-text {
        color: #666;
        font-size: 0.75rem;
        line-height: 1.2;
    }

    .footer {
        background: #f0f0f0;
        padding: 0.8rem 2rem;
        text-align: center;
        border-top: 1px solid #e0e0e0;
        flex-shrink: 0;
    }

    .footer p {
        color: #999;
        font-size: 0.8rem;
        margin-bottom: 0.3rem;
        line-height: 1.4;
    }

    .support-contact {
        margin-top: 0.4rem;
        padding-top: 0.4rem;
        border-top: 1px solid #e0e0e0;
    }

    .support-text {
        color: #667eea;
        font-weight: 600;
        margin-bottom: 0.2rem;
        font-size: 0.8rem;
    }

    .contact-info {
        color: #666;
        font-size: 0.8rem;
    }

    @media (max-width: 768px) {
        .header {
            padding: 2rem 1.5rem;
        }

        .header h1 {
            font-size: 1.8rem;
        }

        .icon {
            font-size: 4rem;
        }

        .content {
            padding: 2rem 1.5rem;
        }

        .details-grid {
            grid-template-columns: 1fr;
        }

        .features {
            grid-template-columns: 1fr;
        }

        .department-info {
            padding: 1.5rem;
        }

        .maintenance-container {
            border-radius: 12px;
        }
    }

    @media (max-width: 480px) {
        .header {
            padding: 1.5rem 1rem;
        }

        .header h1 {
            font-size: 1.5rem;
        }

        .icon {
            font-size: 3rem;
        }

        .content {
            padding: 1.5rem 1rem;
        }

        .detail-item {
            padding: 1rem;
        }

        .department-info {
            padding: 1.2rem;
            margin: 1.5rem 0;
        }

        .department-name {
            font-size: 1.1rem;
        }
    }
    </style>
</head>

<body>
    <div class="maintenance-wrapper">
        <div class="maintenance-container">
            <!-- Header -->
            <div class="header">
                <div class="status-badge">⚙️ MAINTENANCE MODE</div>
                <div class="icon">🔧</div>
                <h1>System Maintenance</h1>
                <p>We're performing scheduled maintenance to enhance your experience</p>
            </div>

            <!-- Main Content -->
            <div class="content">
                <!-- Message Box -->
                <div class="message-box">
                    <h3>📢 Important Notice</h3>
                    <p>We're currently upgrading our Pincode Serviceability Checker system to provide you with better
                        performance, reliability, and features. Thank you for your patience during this brief
                        maintenance window.</p>
                </div>

                <!-- Circular Timer -->
                <div class="timer-section">
                    <div class="circular-timer">
                        <div class="timer-circle-progress"></div>
                        <div class="timer-inner-circle">
                            <div class="timer-display" id="timerDisplay">60:00</div>
                            <div class="timer-label">Remaining</div>
                        </div>
                    </div>
                </div>

                <!-- Details Grid -->
                <div class="details-grid">
                    <div class="detail-item">
                        <h4>⏰ Maintenance Started</h4>
                        <p id="maintenanceStartTime">Loading...</p>
                    </div>
                    <div class="detail-item">
                        <h4>✓ Expected Return</h4>
                        <p id="expectedReturnTime">Loading...</p>
                    </div>
                </div>

                <!-- Department Info -->
                <div class="department-info">
                    <div class="department-logo">🏢</div>
                    <div class="department-name">Sanyasi IT Department</div>
                    <div class="department-subtitle">Infrastructure & Systems Management</div>
                    <div class="department-message">
                        <p>We are committed to maintaining the highest standards of service reliability. This
                            maintenance activity is essential to ensure optimal performance, security updates, and
                            system enhancements for all users.</p>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="progress-container">
                    <div class="progress-label">Maintenance Progress</div>
                    <div class="progress-bar">
                        <div class="progress-fill"></div>
                    </div>
                </div>

                <!-- Features During Maintenance -->
                <div>
                    <div class="progress-label">What We're Improving</div>
                    <div class="features">
                        <div class="feature">
                            <div class="feature-icon">⚡</div>
                            <div class="feature-text">Performance Optimization</div>
                        </div>
                        <div class="feature">
                            <div class="feature-icon">🔒</div>
                            <div class="feature-text">Security Updates</div>
                        </div>
                        <div class="feature">
                            <div class="feature-icon">📊</div>
                            <div class="feature-text">Database Optimization</div>
                        </div>
                        <div class="feature">
                            <div class="feature-icon">🎨</div>
                            <div class="feature-text">UI/UX Improvements</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                <p>We appreciate your patience and understanding.</p>
                <p>For real-time status updates, please check back shortly or contact our support team.</p>

                <div class="support-contact">
                    <div class="support-text">📞 WhatsApp on IT Support Group</div>
                    <div class="contact-info">
                        Email: <strong>kishor@sanyasiayurveda.com</strong>
                    </div>
                    <div class="contact-info" style="margin-top: 0.3rem;">
                        Department: <strong>Sanyasi IT Department</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Function to format date and time
    function formatDateTime(date) {
        const options = {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: true
        };
        return date.toLocaleDateString('en-US', options);
    }

    // Format time display as MM:SS
    function formatTimeDisplay(seconds) {
        const minutes = Math.floor(seconds / 60);
        const secs = seconds % 60;
        return `${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
    }

    // Initialize maintenance times and timer
    let maintenanceStartTime = null;
    let maintenanceEndTime = null;

    function initializeTimes() {
        const now = new Date();
        maintenanceStartTime = now;
        maintenanceEndTime = new Date(now.getTime() + 60 * 60 * 1000); // 60 minutes

        // Maintenance started time
        const startTimeElement = document.getElementById('maintenanceStartTime');
        if (startTimeElement) {
            startTimeElement.textContent = formatDateTime(now);
        }

        // Expected return time
        const returnTimeElement = document.getElementById('expectedReturnTime');
        if (returnTimeElement) {
            returnTimeElement.textContent = formatDateTime(maintenanceEndTime);
        }

        // Update timer
        updateTimer();
    }

    function updateTimer() {
        const now = new Date();
        const remainingMs = maintenanceEndTime - now;
        const remainingSeconds = Math.max(0, Math.floor(remainingMs / 1000));

        const timerDisplay = document.getElementById('timerDisplay');
        if (timerDisplay) {
            timerDisplay.textContent = formatTimeDisplay(remainingSeconds);
        }

        // Update maintenance started every second to show real-time
        const startTimeElement = document.getElementById('maintenanceStartTime');
        if (startTimeElement) {
            startTimeElement.textContent = formatDateTime(now);
        }
    }

    // Update on page load
    document.addEventListener('DOMContentLoaded', function() {
        initializeTimes();

        // Update timer and times every second
        setInterval(updateTimer, 1000);
    });
    </script>
</body>

</html>