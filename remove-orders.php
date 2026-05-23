<?php
include("../config.php");

// ─── AJAX handler ────────────────────────────────────────────────────────────
if (isset($_POST['action'])) {
    header('Content-Type: application/json');

    // Accept either a direct cutoff date OR a days offset
    if (!empty($_POST['cutoff'])) {
        $cutoff = date("Y-m-d", strtotime($_POST['cutoff'])); // sanitise
    } else {
        $days   = intval($_POST['days']);
        $cutoff = date("Y-m-d", strtotime("-{$days} Days"));
    }

    if ($_POST['action'] === 'count') {
        $qCount = "SELECT COUNT(*) as cnt FROM c_order WHERE con <= '{$cutoff}'";
        $rc = $gVar->getSqlArray($qCount);
        echo json_encode(['count' => (int)$rc[0]['cnt'], 'cutoff' => $cutoff]);
        exit;
    }

    if ($_POST['action'] === 'delete') {
        $gVar->runSql("DELETE FROM c_order  WHERE con <= '{$cutoff}'");
        $rcDel = $gVar->getSqlArray("SELECT ROW_COUNT() as DelRowCount");
        $gVar->runSql("DELETE FROM c_otrack WHERE con <= '{$cutoff}'");
        $gVar->runSql("DELETE FROM c_odscr  WHERE con <= '{$cutoff}'");
        echo json_encode(['deleted' => (int)$rcDel[0]['DelRowCount'], 'cutoff' => $cutoff]);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Cleanup Utility</title>
    <style>
    @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600;700&family=Syne:wght@400;700;800&display=swap');

    *,
    *::before,
    *::after {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    :root {
        --bg: #0d0f14;
        --surface: #161921;
        --border: #252a35;
        --accent: #e8453c;
        --accent2: #ff7b54;
        --text: #e2e5ec;
        --muted: #6b7385;
        --success: #3cce7a;
        --warn: #f5a623;
        --mono: 'JetBrains Mono', monospace;
        --sans: 'Syne', sans-serif;
    }

    body {
        background: var(--bg);
        color: var(--text);
        font-family: var(--sans);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 24px;
    }

    /* subtle grid bg */
    body::before {
        content: '';
        position: fixed;
        inset: 0;
        background-image:
            linear-gradient(var(--border) 1px, transparent 1px),
            linear-gradient(90deg, var(--border) 1px, transparent 1px);
        background-size: 40px 40px;
        opacity: .35;
        pointer-events: none;
    }

    .card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 40px 44px;
        width: 100%;
        max-width: 540px;
        position: relative;
        box-shadow: 0 32px 80px rgba(0, 0, 0, .55);
    }

    .badge {
        font-family: var(--mono);
        font-size: 11px;
        letter-spacing: .12em;
        text-transform: uppercase;
        color: var(--accent);
        border: 1px solid var(--accent);
        border-radius: 4px;
        padding: 3px 8px;
        display: inline-block;
        margin-bottom: 18px;
    }

    h1 {
        font-size: 26px;
        font-weight: 800;
        line-height: 1.2;
        margin-bottom: 6px;
    }

    .subtitle {
        color: var(--muted);
        font-size: 14px;
        font-family: var(--mono);
        margin-bottom: 36px;
    }

    label {
        display: block;
        font-size: 12px;
        font-family: var(--mono);
        letter-spacing: .1em;
        text-transform: uppercase;
        color: var(--muted);
        margin-bottom: 10px;
    }

    /* ── New age picker ── */
    .picker-section {
        margin-bottom: 10px;
    }

    .picker-section>label {
        margin-bottom: 12px;
    }

    .option-tiles {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
        margin-bottom: 16px;
    }

    .tile {
        background: var(--bg);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 14px 10px;
        text-align: center;
        cursor: pointer;
        transition: all .18s;
    }

    .tile:hover {
        border-color: var(--accent2);
    }

    .tile.active {
        border-color: var(--accent2);
        background: rgba(255, 123, 84, .07);
        box-shadow: 0 0 0 1px var(--accent2);
    }

    .tile .t-value {
        font-family: var(--mono);
        font-size: 20px;
        font-weight: 700;
        color: var(--text);
        line-height: 1;
        display: block;
    }

    .tile .t-unit {
        font-family: var(--mono);
        font-size: 11px;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: .08em;
        display: block;
        margin-top: 4px;
    }

    .tile.active .t-value,
    .tile.active .t-unit {
        color: var(--accent2);
    }

    .fy-row {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 4px;
    }

    .fy-row>label {
        margin: 0;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .fy-select {
        flex: 1;
        background: var(--bg);
        border: 1px solid var(--border);
        border-radius: 8px;
        color: var(--text);
        font-family: var(--mono);
        font-size: 14px;
        font-weight: 600;
        padding: 10px 34px 10px 14px;
        outline: none;
        cursor: pointer;
        transition: border-color .2s;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%236b7385' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
    }

    .fy-select:focus {
        border-color: var(--accent2);
    }

    .fy-select option {
        background: #1a1e27;
    }

    .fy-btn {
        background: transparent;
        border: 1px solid var(--border);
        border-radius: 8px;
        color: var(--muted);
        font-family: var(--mono);
        font-size: 12px;
        padding: 10px 16px;
        cursor: pointer;
        white-space: nowrap;
        transition: all .18s;
        flex-shrink: 0;
    }

    .fy-btn:hover,
    .fy-btn.active {
        border-color: var(--accent2);
        color: var(--accent2);
        background: rgba(255, 123, 84, .07);
    }

    .selected-pill {
        display: none;
        align-items: center;
        gap: 8px;
        background: rgba(255, 123, 84, .06);
        border: 1px solid rgba(255, 123, 84, .25);
        border-radius: 8px;
        padding: 10px 14px;
        margin: 16px 0 22px;
        font-family: var(--mono);
        font-size: 13px;
    }

    .selected-pill.visible {
        display: flex;
    }

    .selected-pill .pill-label {
        color: var(--muted);
    }

    .selected-pill .pill-val {
        color: var(--accent2);
        font-weight: 700;
        margin-left: auto;
    }

    /* primary button */
    .btn {
        width: 100%;
        padding: 14px;
        border-radius: 10px;
        border: none;
        font-family: var(--sans);
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        transition: all .2s;
        letter-spacing: .03em;
    }

    .btn-primary {
        background: var(--accent);
        color: #fff;
    }

    .btn-primary:hover:not(:disabled) {
        background: #f55a51;
        box-shadow: 0 0 30px rgba(232, 69, 60, .4);
        transform: translateY(-1px);
    }

    .btn-primary:disabled {
        opacity: .45;
        cursor: not-allowed;
    }

    .btn-ghost {
        background: transparent;
        border: 1px solid var(--border);
        color: var(--muted);
        margin-top: 10px;
    }

    .btn-ghost:hover {
        border-color: var(--text);
        color: var(--text);
    }

    /* info box */
    .info-box {
        background: var(--bg);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 18px 20px;
        margin-bottom: 22px;
        display: none;
    }

    .info-box.visible {
        display: block;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-family: var(--mono);
        font-size: 13px;
        color: var(--muted);
        padding: 4px 0;
    }

    .info-row .val {
        font-size: 15px;
        font-weight: 700;
        color: var(--warn);
    }

    .info-row .val.big {
        font-size: 22px;
        color: var(--accent);
    }

    /* spinner */
    .spinner {
        width: 18px;
        height: 18px;
        border: 2px solid rgba(255, 255, 255, .2);
        border-top-color: #fff;
        border-radius: 50%;
        animation: spin .7s linear infinite;
        display: inline-block;
        vertical-align: middle;
        margin-right: 8px;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    /* ── Modal ── */
    .overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, .7);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 100;
        opacity: 0;
        pointer-events: none;
        transition: opacity .25s;
        padding: 24px;
    }

    .overlay.show {
        opacity: 1;
        pointer-events: all;
    }

    .modal {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 36px 40px;
        max-width: 420px;
        width: 100%;
        transform: translateY(20px) scale(.97);
        transition: transform .25s;
        box-shadow: 0 40px 100px rgba(0, 0, 0, .6);
    }

    .overlay.show .modal {
        transform: translateY(0) scale(1);
    }

    .modal-icon {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        background: rgba(232, 69, 60, .12);
        border: 1px solid rgba(232, 69, 60, .35);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        margin-bottom: 20px;
    }

    .modal h2 {
        font-size: 20px;
        font-weight: 800;
        margin-bottom: 10px;
    }

    .modal p {
        color: var(--muted);
        font-size: 14px;
        line-height: 1.6;
        margin-bottom: 6px;
    }

    .modal .highlight {
        color: var(--accent);
        font-family: var(--mono);
        font-size: 28px;
        font-weight: 700;
        display: block;
        margin: 14px 0 4px;
    }

    .modal .sub {
        font-size: 12px;
        font-family: var(--mono);
        color: var(--muted);
    }

    .modal-actions {
        display: flex;
        gap: 10px;
        margin-top: 28px;
    }

    .modal-actions .btn {
        flex: 1;
        margin: 0;
    }

    .btn-danger {
        background: var(--accent);
        color: #fff;
    }

    .btn-danger:hover {
        background: #f55a51;
        box-shadow: 0 0 24px rgba(232, 69, 60, .45);
    }

    .btn-cancel {
        background: transparent;
        border: 1px solid var(--border);
        color: var(--muted);
    }

    .btn-cancel:hover {
        border-color: var(--text);
        color: var(--text);
    }

    /* success state */
    .result-box {
        display: none;
        text-align: center;
        padding: 10px 0 4px;
    }

    .result-box.visible {
        display: block;
    }

    .result-box .check {
        font-size: 42px;
        margin-bottom: 10px;
        display: block;
    }

    .result-box h3 {
        font-size: 20px;
        font-weight: 800;
        color: var(--success);
        margin-bottom: 8px;
    }

    .result-box p {
        font-size: 13px;
        font-family: var(--mono);
        color: var(--muted);
    }

    .result-box .again-btn {
        margin-top: 22px;
        display: inline-block;
        padding: 10px 28px;
        border-radius: 8px;
        border: 1px solid var(--border);
        color: var(--muted);
        font-family: var(--mono);
        font-size: 13px;
        cursor: pointer;
        background: transparent;
        transition: all .18s;
        width: auto;
    }

    .result-box .again-btn:hover {
        border-color: var(--text);
        color: var(--text);
    }

    .divider {
        border: none;
        border-top: 1px solid var(--border);
        margin: 26px 0;
    }
    </style>
</head>

<body>

    <div class="card" id="mainCard">
        <span class="badge">⚙ Maintenance Utility</span>
        <h1>Order Cleanup</h1>
        <p class="subtitle">// purge c_order · c_otrack · c_odscr</p>

        <!-- Step 1 — age picker -->
        <div id="stepPicker">

            <div class="picker-section">
                <label>Select retention period</label>

                <!-- fixed tiles: minimum 90 days -->
                <div class="option-tiles">
                    <div class="tile" data-type="days" data-value="90" onclick="selectTile(this)">
                        <span class="t-value">90</span>
                        <span class="t-unit">Days</span>
                    </div>
                    <div class="tile" data-type="days" data-value="365" onclick="selectTile(this)">
                        <span class="t-value">1</span>
                        <span class="t-unit">Year</span>
                    </div>
                    <div class="tile" data-type="days" data-value="730" onclick="selectTile(this)">
                        <span class="t-value">2</span>
                        <span class="t-unit">Years</span>
                    </div>
                    <div class="tile" data-type="days" data-value="1095" onclick="selectTile(this)">
                        <span class="t-value">3</span>
                        <span class="t-unit">Years</span>
                    </div>
                </div>

                <!-- FY picker row -->
                <div class="fy-row">
                    <label>or FY</label>
                    <select class="fy-select" id="fySelect" onchange="onFyChange()">
                        <option value="">— pick a financial year —</option>
                    </select>
                    <button class="fy-btn" id="fyBtn" onclick="selectFY()">Use FY</button>
                </div>
            </div>

            <!-- live summary pill -->
            <div class="selected-pill" id="selectedPill">
                <span class="pill-label">📅 Cutoff — deleting orders on or before</span>
                <span class="pill-val" id="pillVal">—</span>
            </div>

            <button class="btn btn-primary" id="checkBtn" onclick="checkCount()" disabled>
                Check Affected Records
            </button>
        </div>

        <hr class="divider" id="divider1" style="display:none">

        <!-- Step 2 — count preview -->
        <div id="stepPreview" style="display:none">
            <div class="info-box visible" id="infoBox">
                <div class="info-row">
                    <span>Cutoff date</span>
                    <span class="val" id="cutoffDisplay">—</span>
                </div>
                <div class="info-row" style="margin-top:10px">
                    <span>Orders to be removed</span>
                    <span class="val big" id="countDisplay">—</span>
                </div>
            </div>

            <button class="btn btn-primary" id="deleteBtn" onclick="openConfirm()">
                🗑 Delete These Records
            </button>
            <button class="btn btn-ghost" onclick="resetForm()">← Change Days</button>
        </div>

        <!-- Step 3 — done -->
        <div class="result-box" id="resultBox">
            <span class="check">✅</span>
            <h3 id="resultTitle">Cleanup Complete</h3>
            <p id="resultDesc"></p>
            <button class="btn again-btn" onclick="resetForm()">Run Again</button>
        </div>
    </div>

    <!-- ── Confirm Modal ── -->
    <div class="overlay" id="overlay">
        <div class="modal">
            <div class="modal-icon">⚠️</div>
            <h2>Final Confirmation</h2>
            <p>You are about to permanently delete:</p>
            <span class="highlight" id="modalCount">0</span>
            <p class="sub">orders (+ related tracking &amp; description records)</p>
            <p style="margin-top:14px">Orders created on or before <strong id="modalCutoff"
                    style="color:var(--warn)">—</strong>.<br>This action <strong style="color:var(--accent)">cannot be
                    undone.</strong></p>
            <div class="modal-actions">
                <button class="btn btn-cancel" onclick="closeConfirm()">Cancel</button>
                <button class="btn btn-danger" id="confirmBtn" onclick="doDelete()">Yes, Delete All</button>
            </div>
        </div>
    </div>

    <script>
    let pendingCutoff = ''; // ISO date string
    let pendingCount = 0;

    // ── Build FY dropdown (last 5 FYs, Apr–Mar cycle) ──────────────────────────
    (function buildFY() {
        const sel = document.getElementById('fySelect');
        const now = new Date();
        // current FY start year: if month >= April (3) use this year, else last year
        let startYear = now.getMonth() >= 3 ? now.getFullYear() : now.getFullYear() - 1;
        for (let i = 0; i < 6; i++) {
            const y = startYear - i;
            const opt = document.createElement('option');
            opt.value = `${y}-03-31`; // end of that FY = 31 Mar of next year
            opt.textContent = `FY ${y}–${String(y + 1).slice(-2)}  (Apr ${y} – Mar ${y+1})`;
            sel.appendChild(opt);
        }
    })();

    // ── Tile selection ──────────────────────────────────────────────────────────
    function selectTile(el) {
        document.querySelectorAll('.tile').forEach(t => t.classList.remove('active'));
        document.getElementById('fyBtn').classList.remove('active');
        document.getElementById('fySelect').value = '';
        el.classList.add('active');

        const days = parseInt(el.dataset.value);
        const d = new Date();
        d.setDate(d.getDate() - days);
        setCutoff(d.toISOString().split('T')[0]);
    }

    // ── FY helpers ─────────────────────────────────────────────────────────────
    function onFyChange() {
        // deselect tiles when user picks from dropdown
        document.querySelectorAll('.tile').forEach(t => t.classList.remove('active'));
        document.getElementById('fyBtn').classList.remove('active');
        const val = document.getElementById('fySelect').value;
        if (val) {
            // preview cutoff but don't commit until "Use FY" is clicked
            document.getElementById('fyBtn').classList.add('active');
        }
    }

    function selectFY() {
        const val = document.getElementById('fySelect').value;
        if (!val) {
            alert('Please select a financial year first.');
            return;
        }
        document.querySelectorAll('.tile').forEach(t => t.classList.remove('active'));
        document.getElementById('fyBtn').classList.add('active');
        setCutoff(val);
    }

    // ── Shared cutoff setter ────────────────────────────────────────────────────
    function setCutoff(isoDate) {
        pendingCutoff = isoDate;
        document.getElementById('pillVal').textContent = isoDate;
        document.getElementById('selectedPill').classList.add('visible');
        document.getElementById('checkBtn').disabled = false;
        // reset preview if it was open
        document.getElementById('divider1').style.display = 'none';
        document.getElementById('stepPreview').style.display = 'none';
    }

    // ── Count check ────────────────────────────────────────────────────────────
    async function checkCount() {
        if (!pendingCutoff) {
            alert('Please select a period first.');
            return;
        }

        const btn = document.getElementById('checkBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner"></span>Checking…';

        const fd = new FormData();
        fd.append('action', 'count');
        fd.append('cutoff', pendingCutoff);

        try {
            const res = await fetch('', {
                method: 'POST',
                body: fd
            });
            const data = await res.json();

            pendingCount = data.count;

            document.getElementById('cutoffDisplay').textContent = data.cutoff;
            document.getElementById('countDisplay').textContent = data.count.toLocaleString();

            document.getElementById('divider1').style.display = '';
            document.getElementById('stepPreview').style.display = '';

            const delBtn = document.getElementById('deleteBtn');
            if (data.count === 0) {
                delBtn.disabled = true;
                delBtn.textContent = 'Nothing to Delete';
            } else {
                delBtn.disabled = false;
                delBtn.innerHTML = `🗑 Delete ${data.count.toLocaleString()} Records`;
            }
        } catch (e) {
            alert('Error fetching count. Check server logs.');
        }

        btn.disabled = false;
        btn.textContent = 'Re-Check';
    }

    // ── Confirm modal ───────────────────────────────────────────────────────────
    function openConfirm() {
        document.getElementById('modalCount').textContent = pendingCount.toLocaleString();
        document.getElementById('modalCutoff').textContent = pendingCutoff;
        document.getElementById('overlay').classList.add('show');
    }

    function closeConfirm() {
        document.getElementById('overlay').classList.remove('show');
    }

    async function doDelete() {
        const btn = document.getElementById('confirmBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner"></span>Deleting…';

        const fd = new FormData();
        fd.append('action', 'delete');
        fd.append('cutoff', pendingCutoff);

        try {
            const res = await fetch('', {
                method: 'POST',
                body: fd
            });
            const data = await res.json();
            closeConfirm();
            showResult(data.deleted, data.cutoff);
        } catch (e) {
            alert('Deletion failed. Check server logs.');
            btn.disabled = false;
            btn.textContent = 'Yes, Delete All';
        }
    }

    // ── Result / reset ──────────────────────────────────────────────────────────
    function showResult(deleted, cutoff) {
        document.getElementById('stepPicker').style.display = 'none';
        document.getElementById('stepPreview').style.display = 'none';
        document.getElementById('divider1').style.display = 'none';

        document.getElementById('resultTitle').textContent =
            deleted > 0 ? `${deleted.toLocaleString()} Orders Removed` : 'Nothing Was Deleted';
        document.getElementById('resultDesc').textContent =
            `All records on or before ${cutoff} have been purged from c_order, c_otrack & c_odscr.`;

        document.getElementById('resultBox').classList.add('visible');
    }

    function resetForm() {
        pendingCutoff = '';
        pendingCount = 0;

        document.getElementById('stepPicker').style.display = '';
        document.getElementById('stepPreview').style.display = 'none';
        document.getElementById('divider1').style.display = 'none';
        document.getElementById('resultBox').classList.remove('visible');
        document.getElementById('selectedPill').classList.remove('visible');

        document.querySelectorAll('.tile').forEach(t => t.classList.remove('active'));
        document.getElementById('fySelect').value = '';
        document.getElementById('fyBtn').classList.remove('active');

        const btn = document.getElementById('checkBtn');
        btn.disabled = true;
        btn.textContent = 'Check Affected Records';
    }

    document.getElementById('overlay').addEventListener('click', function(e) {
        if (e.target === this) closeConfirm();
    });
    </script>
</body>

</html>