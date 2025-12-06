<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Tutoring Scheduler</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* BASE STYLES */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 40px 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 2px 20px rgba(0,0,0,0.1); margin-bottom: 30px; }
        .header h1 { color: #2d3748; font-size: 2.5rem; margin-bottom: 10px; }
        .header p { color: #718096; font-size: 1.1rem; }
        .header-flex { display: flex; justify-content: space-between; align-items: center; }
        
        /* TABS */
        .tabs { background: white; border-radius: 12px; box-shadow: 0 2px 20px rgba(0,0,0,0.1); margin-bottom: 30px; display: flex; overflow: hidden; }
        .tab { flex: 1; padding: 20px; background: white; border: none; cursor: pointer; font-size: 1rem; color: #718096; transition: all 0.3s; border-bottom: 3px solid transparent; display: flex; align-items: center; justify-content: center; gap: 10px; }
        .tab:hover { background: #f7fafc; }
        .tab.active { color: #5a67d8; border-bottom-color: #5a67d8; font-weight: 600; }
        .tab.hidden { display: none !important; }
        
        /* CONTENT */
        .content { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 2px 20px rgba(0,0,0,0.1); display: none; }
        .content.active { display: block; }
        .content-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        
        /* BUTTONS */
        .btn { padding: 12px 24px; border: none; border-radius: 8px; font-size: 1rem; cursor: pointer; transition: all 0.3s; display: inline-flex; align-items: center; gap: 8px; font-weight: 500; }
        .btn-primary { background: #5a67d8; color: white; }
        .btn-primary:hover { background: #4c51bf; transform: translateY(-2px); }
        .btn-danger { background: #fc8181; color: white; padding: 8px 16px; }
        .btn-edit { background: #a9a9a9; color: white; padding: 8px 16px; }
        
        /* ADMIN DASHBOARD STYLES */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 40px; }
        .stat-card { background: #f7fafc; padding: 20px; border-radius: 12px; text-align: center; border: 2px solid #edf2f7; }
        .stat-number { font-size: 2.5rem; font-weight: bold; color: #5a67d8; display: block; margin-bottom: 5px; }
        .stat-label { color: #718096; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; }
        
        .user-table { width: 100%; border-collapse: collapse; }
        .user-table th { text-align: left; padding: 15px; border-bottom: 2px solid #edf2f7; color: #718096; }
        .user-table td { padding: 15px; border-bottom: 1px solid #edf2f7; color: #2d3748; }
        .user-table tr:hover { background: #f7fafc; }

        /* General Styles */
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 25px; }
        .card { border: 2px solid #e2e8f0; border-radius: 12px; padding: 25px; margin-bottom: 20px; transition: all 0.3s; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; color: #2d3748; font-weight: 600; font-size: 0.9rem; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 1rem; }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 1000; }
        .modal.active { display: flex; }
        .modal-content { background: white; padding: 40px; border-radius: 12px; max-width: 500px; width: 90%; max-height: 90vh; overflow-y: auto; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .close-btn { background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #718096; }
        .auth-toggle { text-align: center; margin-top: 20px; font-size: 0.9rem; }
        .auth-toggle a { color: #5a67d8; cursor: pointer; text-decoration: underline; }
        #tutor-extra-fields { display: none; border-top: 1px solid #eee; padding-top: 20px; margin-top: 20px; }
    </style>
</head>
<body>

    <div id="auth-view" class="container" style="display: block;">
        <div id="login-form" style="max-width: 400px; margin: 60px auto;">
            <div class="header"><h1>Login</h1><p>Welcome to the Tutor Scheduler</p></div>
            <div class="content active">
                <form onsubmit="handleLogin(event)">
                    <div class="form-group"><label>Email</label><input type="email" id="login-email" required></div>
                    <div class="form-group"><label>Password</label><input type="password" id="login-password" required></div>
                    <button class="btn btn-primary" style="width: 100%">Sign In</button>
                </form>
                <div class="auth-toggle">Don't have an account? <a onclick="toggleAuth('register')">Create Account</a></div>
            </div>
        </div>

        <div id="register-form" style="max-width: 400px; margin: 60px auto; display: none;">
            <div class="header"><h1>Create Account</h1><p>Join as a Student or Tutor</p></div>
            <div class="content active">
                <form onsubmit="handleRegister(event)">
                    <div class="form-group"><label>Full Name</label><input type="text" id="reg-name" required></div>
                    <div class="form-group"><label>Email</label><input type="email" id="reg-email" required></div>
                    <div class="form-group"><label>Password</label><input type="password" id="reg-password" required></div>
                    <div class="form-group"><label>Confirm Password</label><input type="password" id="reg-password-confirm" required></div>
                    
                    <div class="form-group">
                        <label>I am a...</label>
                        <select id="reg-role" onchange="toggleTutorFields()">
                            <option value="student">Student</option>
                            <option value="tutor">Tutor</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>

                    <div id="tutor-extra-fields">
                        <div class="form-group"><label>Phone Number *</label><input type="text" id="reg-phone"></div>
                        <div class="form-group"><label>Specialization (Subjects)</label><input type="text" id="reg-subjects" placeholder="e.g. Math, Physics"></div>
                        <div class="form-group"><label>Hourly Rate ($)</label><input type="number" id="reg-rate" placeholder="e.g. 25"></div>
                        <div class="form-group"><label>Short Bio</label><textarea id="reg-bio" rows="2" placeholder="Tell students about yourself..."></textarea></div>
                    </div>

                    <button class="btn btn-primary" style="width: 100%">Create Account</button>
                </form>
                <div class="auth-toggle">Already have an account? <a onclick="toggleAuth('login')">Sign In</a></div>
            </div>
        </div>
    </div>

    <div id="app-view" class="container" style="display: none;">
        <div class="header header-flex">
            <div><h1>Online Tutoring Scheduler</h1><p>Welcome, <span id="user-name">User</span> (<span id="user-role-display"></span>)</p></div>
            <button class="btn btn-danger" onclick="handleLogout()">Logout</button>
        </div>

        <div class="tabs">
            <button id="nav-dashboard" class="tab hidden" onclick="switchTab('dashboard', this)">🚀 Admin</button>
            <button class="tab active" onclick="switchTab('browse', this)">👤 Browse</button>
            <button id="nav-sessions" class="tab" onclick="switchTab('sessions', this)">📅 Sessions</button>
            <button id="nav-manage" class="tab hidden" onclick="switchTab('manage', this)">📚 Manage</button>
            <button class="tab" onclick="switchTab('bookings', this)">✓ Bookings</button>
        </div>

        <div id="tab-dashboard" class="content">
            <div class="content-header"><h2>Admin Dashboard</h2><button class="btn btn-primary" onclick="loadAdminData()">Refresh Stats</button></div>
            <div class="stats-grid">
                <div class="stat-card"><span class="stat-number" id="stat-users">0</span><span class="stat-label">Users</span></div>
                <div class="stat-card"><span class="stat-number" id="stat-tutors">0</span><span class="stat-label">Tutors</span></div>
                <div class="stat-card"><span class="stat-number" id="stat-sessions">0</span><span class="stat-label">Sessions</span></div>
                <div class="stat-card"><span class="stat-number" id="stat-bookings">0</span><span class="stat-label">Bookings</span></div>
            </div>
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; border-top:1px solid #eee; padding-top:20px;">
                <h3 id="table-title">Active Users</h3>
                <div>
                    <button class="btn btn-primary" onclick="showActiveUsers()">Active Users</button>
                    <button class="btn btn-danger" onclick="showTrashedUsers()">🗑️ Trash Bin</button>
                </div>
            </div>
            <div style="overflow-x: auto;">
                <table class="user-table">
                    <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Actions</th></tr></thead>
                    <tbody id="user-table-body"></tbody>
                </table>
            </div>
        </div>

        <div id="tab-browse" class="content active"><div class="content-header"><h2>Browse Tutors</h2></div><div id="tutors-grid" class="grid"></div></div>
        <div id="tab-sessions" class="content"><div class="content-header"><h2>Available Sessions</h2></div><div id="sessions-list"></div></div>
        <div id="tab-manage" class="content"><div class="content-header"><h2>Manage Sessions</h2><button class="btn btn-primary" onclick="openSessionModal()">+ Add Session</button></div><div id="manage-list"></div></div>
        <div id="tab-bookings" class="content"><div class="content-header"><h2>My Bookings</h2></div><div id="bookings-list"></div></div>
    </div>

    <div id="booking-modal" class="modal"><div class="modal-content"><div class="modal-header"><h3>Book Session</h3><button class="close-btn" onclick="closeModal('booking-modal')">×</button></div><div id="booking-info" class="info-box"></div><form onsubmit="confirmBooking(event)"><input type="hidden" id="booking-session-id"><div class="form-group"><label>Name</label><input type="text" id="bk-name" required></div><div class="form-group"><label>Email</label><input type="email" id="bk-email" required></div><div class="form-group"><label>Phone</label><input type="tel" id="bk-phone" required></div><div class="form-group"><label>Notes</label><textarea id="bk-notes" rows="3"></textarea></div><button class="btn btn-primary" style="width: 100%">Confirm Booking</button></form></div></div>
    
    <div id="session-modal" class="modal"><div class="modal-content"><div class="modal-header"><h3 id="session-modal-title">Create Session</h3><button class="close-btn" onclick="closeModal('session-modal')">×</button></div><form onsubmit="saveSession(event)"><input type="hidden" id="sess-id"><div class="form-group" id="tutor-select-container"><label>Tutor</label><select id="sess-tutor"><option value="">Select a tutor</option></select></div><div class="form-group"><label>Subject</label><input type="text" id="sess-subject" required></div><div class="form-group"><label>Date</label><input type="date" id="sess-date" required></div><div class="form-group"><label>Start</label><input type="time" id="sess-start" required></div><div class="form-group"><label>End</label><input type="time" id="sess-end" required></div><button type="submit" class="btn btn-primary" style="width: 100%" id="sess-submit-btn">Save</button></form></div></div>

    <div id="user-modal" class="modal"><div class="modal-content"><div class="modal-header"><h3>Edit User</h3><button class="close-btn" onclick="closeModal('user-modal')">×</button></div><form onsubmit="saveUser(event)"><input type="hidden" id="edit-user-id"><div class="form-group"><label>Name</label><input type="text" id="edit-user-name" required></div><div class="form-group"><label>Email</label><input type="email" id="edit-user-email" required></div><div class="form-group"><label>Role</label><select id="edit-user-role"><option value="student">Student</option><option value="tutor">Tutor</option><option value="admin">Admin</option></select></div><button class="btn btn-primary" style="width:100%">Update User</button></form></div></div>

    <script>
        const API_BASE = 'http://127.0.0.1:8000/api';
        let token = localStorage.getItem('token');
        let user = JSON.parse(localStorage.getItem('user'));
        let tutorsData = [], sessionsData = [], bookingsData = [];
        let showingTrash = false;

        document.addEventListener('DOMContentLoaded', () => {
            const today = new Date().toISOString().split('T')[0];
            const dateInput = document.getElementById('sess-date');
            if(dateInput) dateInput.setAttribute('min', today);
            checkAuth();
        });

        // --- AUTH & ROLES ---
        function checkAuth() {
            if (token && user) {
                document.getElementById('auth-view').style.display = 'none';
                document.getElementById('app-view').style.display = 'block';
                document.getElementById('user-name').innerText = user.name;
                document.getElementById('user-role-display').innerText = user.role.toUpperCase();

                const adminBtn = document.getElementById('nav-dashboard');
                const manageBtn = document.getElementById('nav-manage');
                const sessionsBtn = document.getElementById('nav-sessions');

                if (user.role === 'admin') {
                    adminBtn.classList.remove('hidden');
                    manageBtn.classList.remove('hidden');
                    sessionsBtn.classList.remove('hidden');
                    switchTab('dashboard', adminBtn);
                    loadAdminData();
                } 
                else if (user.role === 'tutor') {
                    adminBtn.classList.add('hidden');
                    manageBtn.classList.remove('hidden');
                    sessionsBtn.classList.add('hidden'); // Hide Sessions for Tutor
                } 
                else {
                    adminBtn.classList.add('hidden');
                    manageBtn.classList.add('hidden');
                    sessionsBtn.classList.remove('hidden');
                }
                loadData();
            } else {
                document.getElementById('auth-view').style.display = 'block';
                document.getElementById('app-view').style.display = 'none';
            }
        }

        // --- ADMIN FUNCTIONS ---
        async function loadAdminData() {
            if (user.role !== 'admin') return;
            const headers = { 'Authorization': `Bearer ${token}` };
            
            const statsRes = await fetch(`${API_BASE}/admin/stats`, { headers });
            const stats = await statsRes.json();
            document.getElementById('stat-users').innerText = stats.total_users;
            document.getElementById('stat-tutors').innerText = stats.total_tutors;
            document.getElementById('stat-sessions').innerText = stats.total_sessions;
            document.getElementById('stat-bookings').innerText = stats.total_bookings;

            showActiveUsers();
        }

        async function showActiveUsers() {
            showingTrash = false;
            document.getElementById('table-title').innerText = "Active Users";
            const headers = { 'Authorization': `Bearer ${token}` };
            const res = await fetch(`${API_BASE}/admin/users`, { headers });
            const users = await res.json();
            renderUserTable(users);
        }

        async function showTrashedUsers() {
            showingTrash = true;
            document.getElementById('table-title').innerText = "Deleted Users (Trash)";
            const headers = { 'Authorization': `Bearer ${token}` };
            const res = await fetch(`${API_BASE}/admin/users?trash=true`, { headers });
            const users = await res.json();
            renderUserTable(users);
        }

        function renderUserTable(users) {
            const tbody = document.getElementById('user-table-body');
            if(users.length === 0) { tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;padding:20px;">No users found</td></tr>'; return; }
            tbody.innerHTML = users.map(u => `
                <tr>
                    <td>${u.name}</td><td>${u.email}</td><td><span class="badge badge-booked">${u.role}</span></td>
                    <td>
                        ${showingTrash ? 
                            `<button class="btn btn-primary" style="padding:5px 10px; font-size:0.8rem;" onclick="restoreUser(${u.id})">♻️ Restore</button>` :
                            `<button class="btn btn-edit" style="padding:5px 10px; font-size:0.8rem;" onclick='openEditUser(${JSON.stringify(u)})'>Edit</button>
                             <button class="btn btn-danger" style="padding:5px 10px; font-size:0.8rem;" onclick="deleteUser(${u.id})">Del</button>`
                        }
                    </td>
                </tr>`).join('');
        }

        function openEditUser(u) {
            document.getElementById('user-modal').classList.add('active');
            document.getElementById('edit-user-id').value = u.id;
            document.getElementById('edit-user-name').value = u.name;
            document.getElementById('edit-user-email').value = u.email;
            document.getElementById('edit-user-role').value = u.role;
        }

        async function saveUser(e) {
            e.preventDefault();
            const id = document.getElementById('edit-user-id').value;
            const payload = { name: document.getElementById('edit-user-name').value, email: document.getElementById('edit-user-email').value, role: document.getElementById('edit-user-role').value };
            await apiCall(`${API_BASE}/admin/users/${id}`, 'PUT', payload);
            closeModal('user-modal'); loadAdminData();
        }

        async function deleteUser(id) { if(!confirm('Move this user to the trash?')) return; await apiCall(`${API_BASE}/admin/users/${id}`, 'DELETE'); showActiveUsers(); }
        async function restoreUser(id) { if(!confirm('Restore this user?')) return; await apiCall(`${API_BASE}/admin/users/${id}/restore`, 'POST'); showTrashedUsers(); }

        // --- STANDARD FUNCTIONS ---
        function toggleTutorFields() {
            const isTutor = document.getElementById('reg-role').value === 'tutor';
            document.getElementById('tutor-extra-fields').style.display = isTutor ? 'block' : 'none';
            document.getElementById('reg-phone').required = isTutor;
            document.getElementById('reg-subjects').required = isTutor;
            document.getElementById('reg-rate').required = isTutor;
            document.getElementById('reg-bio').required = isTutor;
        }
        function toggleAuth(view) { document.getElementById('login-form').style.display = view === 'login' ? 'block' : 'none'; document.getElementById('register-form').style.display = view === 'register' ? 'block' : 'none'; }
        
        async function handleRegister(e) {
            e.preventDefault();
            const payload = {
                name: document.getElementById('reg-name').value,
                email: document.getElementById('reg-email').value,
                password: document.getElementById('reg-password').value,
                password_confirmation: document.getElementById('reg-password-confirm').value,
                role: document.getElementById('reg-role').value,
                phone: document.getElementById('reg-phone').value,
                specialization: document.getElementById('reg-subjects').value,
                hourly_rate: document.getElementById('reg-rate').value,
                bio: document.getElementById('reg-bio').value,
            };
            if(payload.password !== payload.password_confirmation) { alert("Passwords do not match"); return; }
            await authRequest('/register', payload);
        }
        async function handleLogin(e) {
            e.preventDefault();
            await authRequest('/login', { email: document.getElementById('login-email').value, password: document.getElementById('login-password').value });
        }
        async function authRequest(endpoint, payload) {
            try {
                const res = await fetch(`${API_BASE}${endpoint}`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' }, body: JSON.stringify(payload) });
                const data = await res.json();
                if (res.ok) {
                    token = data.access_token; user = data.user;
                    localStorage.setItem('token', token); localStorage.setItem('user', JSON.stringify(user));
                    checkAuth();
                } else { alert('Error: ' + (data.message || 'Failed')); }
            } catch(e) { alert('Network Error'); }
        }
        function handleLogout() { localStorage.clear(); token=null; user=null; checkAuth(); }
        
        async function loadData() {
            const headers = { 'Authorization': `Bearer ${token}` };
            try {
                const [t, s, b] = await Promise.all([
                    fetch(`${API_BASE}/tutors`, { headers }).then(r => r.json()),
                    fetch(`${API_BASE}/sessions`, { headers }).then(r => r.json()),
                    fetch(`${API_BASE}/bookings`, { headers }).then(r => r.json())
                ]);
                tutorsData = t; sessionsData = s; bookingsData = b;
                renderAll(); populateTutorSelect();
            } catch(e) { if(e.status===401) handleLogout(); }
        }

        function renderAll() {
            document.getElementById('tutors-grid').innerHTML = tutorsData.map(t => `<div class="card"><h3>${t.name}</h3><p class="text-sub">${t.bio}</p><div class="meta-row"><span>📧 ${t.email}</span><span>💵 $${t.hourly_rate}/hr</span></div><div>${(t.subjects || []).map(s => `<span class="badge badge-available">${s}</span>`).join(' ')}</div></div>`).join('');
            const today = new Date().toISOString().split('T')[0];
            const available = sessionsData.filter(s => s.status === 'available' && s.session_date >= today);
            document.getElementById('sessions-list').innerHTML = available.map(s => {
                const tutor = tutorsData.find(t => t.id === s.tutor_id) || {};
                return `<div class="card" style="display:flex; justify-content:space-between;"><div><h3>${tutor.name || 'Unknown'}</h3><div style="color:#5a67d8; font-weight:600;">${s.subject}</div><div class="meta-row">📅 ${new Date(s.session_date).toLocaleDateString()} | ⏰ ${s.start_time} - ${s.end_time}</div></div><button class="btn btn-primary" onclick='openBookingModal(${JSON.stringify(s)})'>Book</button></div>`;
            }).join('');
            let mySessions = sessionsData;
            if (user.role === 'tutor') mySessions = sessionsData.filter(s => s.tutor && s.tutor.email === user.email);
            document.getElementById('manage-list').innerHTML = mySessions.map(s => {
                const tutor = tutorsData.find(t => t.id === s.tutor_id) || {};
                return `<div class="card" style="display:flex; justify-content:space-between;"><div><h3>${tutor.name || 'Unknown'} <span class="badge badge-${s.status}">${s.status}</span></h3><div style="color:#5a67d8; font-weight:600;">${s.subject}</div><div class="meta-row">📅 ${new Date(s.session_date).toLocaleDateString()} | ⏰ ${s.start_time} - ${s.end_time}</div></div><div style="display:flex; gap:10px;"><button class="btn btn-edit" onclick='openEditSession(${JSON.stringify(s)})'>📝</button><button class="btn btn-danger" onclick="deleteSession(${s.id})">🗑️</button></div></div>`;
            }).join('');
            const bkList = document.getElementById('bookings-list');
            if(bookingsData.length === 0) bkList.innerHTML = `<div class="empty-state"><div>📚</div><h3>No Bookings</h3></div>`;
            else bkList.innerHTML = bookingsData.map(b => {
                const s = sessionsData.find(x => x.id === b.session_id); if(!s) return '';
                const tutor = tutorsData.find(t => t.id === s.tutor_id) || {};
                return `<div class="card" style="display:flex; justify-content:space-between;"><div><h3>${tutor.name} (Student: ${b.student_name})</h3><div class="meta-row">📅 ${new Date(s.session_date).toLocaleDateString()} | ⏰ ${s.start_time}</div><p class="text-sub">Note: ${b.notes||'None'}</p></div><button class="btn btn-danger" onclick="cancelBooking(${b.id})">Cancel</button></div>`;
            }).join('');
        }

        // --- ACTIONS ---
        function switchTab(name, btn) {
            document.querySelectorAll('.content').forEach(e => e.classList.remove('active'));
            document.querySelectorAll('.tab').forEach(e => e.classList.remove('active'));
            document.getElementById(`tab-${name}`).classList.add('active');
            btn.classList.add('active');
        }
        function closeModal(id) { document.getElementById(id).classList.remove('active'); }
        function populateTutorSelect() { document.getElementById('sess-tutor').innerHTML = '<option value="">Select Tutor</option>' + tutorsData.map(t => `<option value="${t.id}">${t.name}</option>`).join(''); }
        function openSessionModal() {
            document.getElementById('session-modal').classList.add('active');
            document.getElementById('session-modal-title').innerText = "Create Session";
            document.getElementById('sess-submit-btn').innerText = "Create";
            document.getElementById('sess-id').value = '';
            document.getElementById('tutor-select-container').style.display = (user.role === 'tutor') ? 'none' : 'block';
        }
        function openEditSession(s) {
            openSessionModal();
            document.getElementById('session-modal-title').innerText = "Edit Session";
            document.getElementById('sess-submit-btn').innerText = "Update";
            document.getElementById('sess-id').value = s.id;
            document.getElementById('sess-tutor').value = s.tutor_id;
            document.getElementById('sess-subject').value = s.subject;
            document.getElementById('sess-date').value = s.session_date;
            document.getElementById('sess-start').value = s.start_time.substring(0,5);
            document.getElementById('sess-end').value = s.end_time.substring(0,5);
        }
        function openBookingModal(s) {
            document.getElementById('booking-modal').classList.add('active');
            document.getElementById('booking-session-id').value = s.id;
            document.getElementById('booking-info').innerHTML = `<p><strong>Subject:</strong> ${s.subject}</p><p><strong>Time:</strong> ${new Date(s.session_date).toLocaleDateString()} @ ${s.start_time}</p>`;
            if (user) { document.getElementById('bk-name').value = user.name; document.getElementById('bk-email').value = user.email; }
        }
        async function saveSession(e) {
            e.preventDefault();
            const id = document.getElementById('sess-id').value;
            const payload = {
                tutor_id: user.role === 'admin' ? document.getElementById('sess-tutor').value : null,
                subject: document.getElementById('sess-subject').value,
                session_date: document.getElementById('sess-date').value,
                start_time: document.getElementById('sess-start').value,
                end_time: document.getElementById('sess-end').value
            };
            const method = id ? 'PUT' : 'POST';
            const url = id ? `${API_BASE}/sessions/${id}` : `${API_BASE}/sessions`;
            await apiCall(url, method, payload);
            closeModal('session-modal'); loadData();
        }
        async function deleteSession(id) { if(confirm('Delete?')) { await apiCall(`${API_BASE}/sessions/${id}`, 'DELETE'); loadData(); } }
        async function confirmBooking(e) {
            e.preventDefault();
            const payload = {
                session_id: document.getElementById('booking-session-id').value,
                student_name: document.getElementById('bk-name').value,
                student_email: document.getElementById('bk-email').value,
                student_phone: document.getElementById('bk-phone').value,
                notes: document.getElementById('bk-notes').value
            };
            await apiCall(`${API_BASE}/bookings`, 'POST', payload);
            closeModal('booking-modal'); loadData();
        }
        async function cancelBooking(id) { if(confirm('Cancel?')) { await apiCall(`${API_BASE}/bookings/${id}`, 'DELETE'); loadData(); } }
        async function apiCall(url, method, body = null) {
            const headers = { 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}` };
            const opts = { method, headers };
            if(body) opts.body = JSON.stringify(body);
            const res = await fetch(url, opts);
            if(!res.ok) { const err = await res.json(); alert(err.message || 'Error'); throw new Error(err); }
            return res.json().catch(() => {});
        }
    </script>
</body>
</html>