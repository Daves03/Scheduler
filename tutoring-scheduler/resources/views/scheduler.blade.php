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
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background: #f7fafc; min-height: 100vh; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }

        /* NAVIGATION - MATCHING STUDENT DESIGN */
        .top-nav { background: white; border-bottom: 1px solid #e2e8f0; padding: 10px 20px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .top-nav-left { font-size: 1.2rem; font-weight: 600; color: #5a67d8; display: flex; align-items: center; gap: 8px; }
        .top-nav-links { display: flex; gap: 25px; }
        .top-nav-links a { text-decoration: none; color: #4a5568; padding: 5px 0; transition: color 0.2s; font-size: 1rem; }
        .top-nav-links a.active, .top-nav-links a:hover { color: #5a67d8; font-weight: 600; }
        
        /* DASHBOARD SECTIONS (for the student view) */
        .dashboard-grid { display: grid; grid-template-columns: 1fr; gap: 30px; margin-top: 30px; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 20px;}
        .card-header { font-size: 1.25rem; color: #2d3748; margin-bottom: 15px; display: flex; align-items: center; gap: 10px; font-weight: 600; border-bottom: 1px solid #edf2f7; padding-bottom: 10px; }
        .welcome-card { background: #e6e9ff; border-left: 5px solid #5a67d8; }
        .welcome-card .card-header { border-bottom: none; color: #5a67d8; }

        /* TABLE STYLES */
        .data-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .data-table th, .data-table td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #e2e8f0; font-size: 0.95rem; }
        .data-table th { background: #f7fafc; color: #4a5568; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        .data-table tr:hover { background: #edf2f7; }

        /* BUTTONS AND BADGES */
        .btn { padding: 8px 16px; border: none; border-radius: 6px; font-size: 0.9rem; cursor: pointer; transition: all 0.3s; display: inline-flex; align-items: center; justify-content: center; font-weight: 500; }
        .btn-primary { background: #5a67d8; color: white; }
        .btn-danger { background: #fc8181; color: white; }
        .btn-link { background: none; color: #5a67d8; padding: 0; }
        .btn-link:hover { text-decoration: underline; }

        .badge { padding: 4px 10px; border-radius: 4px; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; }
        .badge-confirmed { background: #48bb78; color: white; }
        .badge-pending { background: #f6ad55; color: white; }

        /* General Styles for Modals/Forms (Reused from previous request) */
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 1000; }
        .modal.active { display: flex; }
        .modal-content { background: white; padding: 40px; border-radius: 12px; max-width: 500px; width: 90%; max-height: 90vh; overflow-y: auto; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .close-btn { background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #718096; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; color: #2d3748; font-weight: 600; font-size: 0.9rem; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 1rem; }

        /* Ensure all previous styles for other tabs are included for completeness */
        .header-flex { display: flex; justify-content: space-between; align-items: center; }
        .tabs { background: white; border-radius: 12px; box-shadow: 0 2px 20px rgba(0,0,0,0.1); margin-bottom: 30px; display: flex; overflow: hidden; }
        .tab { flex: 1; padding: 20px; background: white; border: none; cursor: pointer; font-size: 1rem; color: #718096; transition: all 0.3s; border-bottom: 3px solid transparent; display: flex; align-items: center; justify-content: center; gap: 10px; }
        .tab.hidden { display: none !important; }
        .content { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 2px 20px rgba(0,0,0,0.1); display: none; }
        .content.active { display: block; }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 25px; }
        #tutor-extra-fields { display: none; border-top: 1px solid #eee; padding-top: 20px; margin-top: 20px; }
        .auth-toggle { text-align: center; margin-top: 20px; font-size: 0.9rem; }
        .auth-toggle a { color: #5a67d8; cursor: pointer; text-decoration: underline; }
    </style>
</head>
<body>

    <div id="auth-view" class="container" style="display: block;">
        <div id="login-form" style="max-width: 400px; margin: 60px auto;">
            <div class="card"><h1>Login</h1><p>Welcome to the Tutor Scheduler</p></div>
            <div class="card">
                <form onsubmit="handleLogin(event)">
                    <div class="form-group"><label>Email</label><input type="email" id="login-email" required></div>
                    <div class="form-group"><label>Password</label><input type="password" id="login-password" required></div>
                    <button class="btn btn-primary" style="width: 100%">Sign In</button>
                </form>
                <div class="auth-toggle">Don't have an account? <a onclick="toggleAuth('register')">Create Account</a></div>
            </div>
        </div>

        <div id="register-form" style="max-width: 400px; margin: 60px auto; display: none;">
            <div class="card"><h1>Create Account</h1><p>Join as a Student or Tutor</p></div>
            <div class="card">
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

    <div id="app-view" style="display: none;">
        
        <nav class="top-nav">
            <div class="top-nav-left">
                <i class="fas fa-book"></i> Online Tutor Scheduler
            </div>
            <div class="top-nav-links">
                <a id="nav-dashboard-link" href="#" class="active" onclick="handleDashboardNav(this)">Dashboard</a>
                <a id="nav-browse-link" href="#" onclick="switchTab('browse', this)">Available Tutors</a>
                <a id="nav-sessions-link" href="#" onclick="switchTab('sessions', this)">My Sessions</a>
                <a href="#" onclick="alert('Profile functionality coming soon!')">Profile</a>
                <a href="#" onclick="handleLogout()">Logout</a>
            </div>
        </nav>

        <div class="container">

            <div id="student-view">
                <div class="card welcome-card">
                    <div class="card-header"><i class="fas fa-graduation-cap"></i> <span id="dashboard-title">STUDENT DASHBOARD</span> — ONLINE TUTOR SCHEDULER</div>
                    <p style="font-size: 1.2rem; margin-bottom: 5px;"><i class="fas fa-hand-peace"></i> Welcome, <strong id="user-name">Student!</strong></p>
                    <p style="color: #718096;">Here's your personalized tutoring overview.</p>
                </div>

                <div class="dashboard-grid">
                    <div id="upcoming-sessions-card" class="card">
                        <div class="card-header"><i class="fas fa-calendar-alt"></i> UPCOMING SESSIONS</div>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Tutor</th>
                                    <th>Subject</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="upcoming-sessions-body">
                                <tr><td colspan="6" style="text-align:center; padding: 20px;">Loading sessions...</td></tr>
                            </tbody>
                        </table>
                        <button class="btn btn-primary" style="margin-top: 15px;" onclick="openBrowseTutors()">+ Book New Session</button>
                    </div>

                    <div id="available-tutors-card" class="card">
                        <div class="card-header"><i class="fas fa-users"></i> SEE AVAILABLE TUTORS</div>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Tutor Name</th>
                                    <th>Subject Expertise</th>
                                    <th>Available Time</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="available-tutors-body">
                                <tr><td colspan="4" style="text-align:center; padding: 20px;">Loading tutors...</td></tr>
                            </tbody>
                        </table>
                        <button class="btn btn-link" onclick="loadData()">Refresh List</button>
                    </div>
                </div>
            </div>

            <div id="admin-tutor-view" style="display: none;">
                <div class="tabs">
                    <button id="tab-nav-dashboard" class="tab" onclick="switchTab('dashboard', this)">🚀 Admin</button>
                    <button id="tab-nav-browse" class="tab" onclick="switchTab('browse', this)">👤 Browse</button>
                    <button id="tab-nav-sessions" class="tab" onclick="switchTab('sessions-list', this)">📅 Sessions</button>
                    <button id="tab-nav-manage" class="tab" onclick="switchTab('manage', this)">📚 Manage</button>
                    <button id="tab-nav-bookings" class="tab" onclick="switchTab('bookings-list', this)">✓ Bookings</button>
                </div>

                <div id="tab-dashboard" class="content">... Admin Content ...</div>
                <div id="tab-browse" class="content">... Tutor Browse Content ...</div>
                <div id="tab-sessions-list" class="content">... Available Sessions List ...</div>
                <div id="tab-manage" class="content">... Manage Sessions Content ...</div>
                <div id="tab-bookings-list" class="content">... My Bookings Content ...</div>
            </div>
            
            <p style="text-align: center; margin-top: 40px; color: #718096; font-size: 0.9rem;">© 2025 Online Tutor Scheduler</p>
        </div>
    </div>
    
    <div id="booking-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Book Session</h3>
                <button class="close-btn" onclick="closeModal('booking-modal')">×</button>
            </div>
            <div id="booking-info" style="margin-bottom: 20px; padding: 10px; background: #f7fafc; border-radius: 4px;"></div>
            <form onsubmit="confirmBooking(event)">
                <input type="hidden" id="booking-session-id">
                <input type="hidden" id="session-tutor-id">
                <div class="form-group"><label>Name</label><input type="text" id="bk-name" required></div>
                <div class="form-group"><label>Email</label><input type="email" id="bk-email" required></div>
                <div class="form-group"><label>Phone</label><input type="tel" id="bk-phone" required></div>
                <div class="form-group"><label>Notes</label><textarea id="bk-notes" rows="3"></textarea></div>
                <button class="btn btn-primary" style="width: 100%">Confirm Booking</button>
            </form>
        </div>
    </div>


    <script>
        const API_BASE = 'http://127.0.0.1:8000/api';
        let token = localStorage.getItem('token');
        let user = JSON.parse(localStorage.getItem('user'));
        let tutorsData = [], sessionsData = [], bookingsData = [];

        document.addEventListener('DOMContentLoaded', () => {
            checkAuth();
        });
        
        // --- AUTH & INITIAL LOAD ---
        function checkAuth() {
            if (token && user) {
                document.getElementById('auth-view').style.display = 'none';
                document.getElementById('app-view').style.display = 'block';
                document.getElementById('user-name').innerText = user.name;
                
                // Show the appropriate dashboard layout based on role
                if (user.role === 'student') {
                    document.getElementById('student-view').style.display = 'block';
                    document.getElementById('admin-tutor-view').style.display = 'none';
                    document.getElementById('dashboard-title').innerText = "STUDENT DASHBOARD";
                    loadData();
                } else {
                    // This is where you would handle Admin/Tutor view toggles from the previous code
                    document.getElementById('student-view').style.display = 'none';
                    document.getElementById('admin-tutor-view').style.display = 'block';
                    document.getElementById('dashboard-title').innerText = `${user.role.toUpperCase()} DASHBOARD`;
                    // Redirect to a specific tab or function for admin/tutor
                    // Example: switchTab('dashboard', document.getElementById('tab-nav-dashboard'));
                    loadData(); 
                }
            } else {
                document.getElementById('auth-view').style.display = 'block';
                document.getElementById('app-view').style.display = 'none';
            }
        }
        
        function handleLogout() { 
            localStorage.clear(); 
            token = null; 
            user = null; 
            window.location.reload(); 
        }

        function handleDashboardNav(btn) {
            // Logic to handle clicking the 'Dashboard' link in the top nav
            document.querySelectorAll('.top-nav-links a').forEach(a => a.classList.remove('active'));
            btn.classList.add('active');
            
            if (user.role === 'student') {
                document.getElementById('student-view').style.display = 'block';
                document.getElementById('admin-tutor-view').style.display = 'none';
            } else {
                // Logic for Admin/Tutor to go back to their default tab
                // switchTab('dashboard', document.getElementById('tab-nav-dashboard'));
            }
        }

        async function loadData() {
            if (!token) return;
            const headers = { 'Authorization': `Bearer ${token}` };
            try {
                // Fetch Tutors, Sessions, and Bookings
                const [tutorsRes, sessionsRes, bookingsRes] = await Promise.all([
                    fetch(`${API_BASE}/tutors`, { headers }),
                    fetch(`${API_BASE}/sessions`, { headers }),
                    fetch(`${API_BASE}/bookings`, { headers }) // Assuming this fetches student's bookings
                ]);

                tutorsData = await tutorsRes.json();
                sessionsData = await sessionsRes.json();
                bookingsData = await bookingsRes.json();
                
                if (user.role === 'student') {
                    renderUpcomingSessions();
                    renderAvailableTutors();
                } else {
                    // You would call render functions for Admin/Tutor tabs here
                }

            } catch (e) {
                console.error("Error loading data:", e);
                // alert("Failed to load dashboard data. Check the API server.");
                // Optional: Force logout if API returns 401 (Unauthorized)
            }
        }
        
        // --- STUDENT RENDER FUNCTIONS ---
        
        function renderUpcomingSessions() {
            const tbody = document.getElementById('upcoming-sessions-body');
            const studentBookings = bookingsData.filter(b => b.student_email === user.email);
            
            if (studentBookings.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; color: #718096; padding: 20px;">You have no upcoming sessions scheduled.</td></tr>';
                return;
            }

            const today = new Date().toISOString().split('T')[0];
            const upcomingBookings = studentBookings.filter(b => {
                const session = sessionsData.find(s => s.id === b.session_id);
                return session && session.session_date >= today;
            });

            tbody.innerHTML = upcomingBookings.map(booking => {
                const session = sessionsData.find(s => s.id === booking.session_id);
                if (!session) return '';

                const tutor = tutorsData.find(t => t.id === session.tutor_id) || { name: 'N/A' };
                // Using session status, but mapping it to the required text
                const statusText = session.status === 'booked' ? 'Confirmed' : 'Pending'; 
                const badgeClass = session.status === 'booked' ? 'badge-confirmed' : 'badge-pending';
                
                const date = new Date(session.session_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                const time = session.start_time.substring(0, 5); 

                return `
                    <tr>
                        <td>${date}</td>
                        <td>${time}</td>
                        <td>${tutor.name}</td>
                        <td>${session.subject}</td>
                        <td><span class="badge ${badgeClass}">${statusText}</span></td>
                        <td class="action-cell">
                            <button class="btn btn-link" onclick="alert('Viewing session ID: ${session.id}')">View</button>
                            <button class="btn btn-danger" onclick="cancelBooking(${booking.id})">Cancel</button>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        function renderAvailableTutors() {
            const tbody = document.getElementById('available-tutors-body');
            
            if (tutorsData.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" style="text-align:center; color: #718096; padding: 20px;">No tutors currently available.</td></tr>';
                return;
            }

            tbody.innerHTML = tutorsData.map(tutor => {
                // Placeholder for Available Time, as this requires complex backend logic (session aggregation)
                const availabilityText = tutor.availability || 'Check Sessions List'; 
                
                return `
                    <tr>
                        <td>${tutor.name}</td>
                        <td>${tutor.specialization || 'General'}</td>
                        <td>${availabilityText}</td>
                        <td class="action-cell">
                            <button class="btn btn-primary" onclick="openBookingModal(null, ${tutor.id}, '${tutor.name}')">Book Now</button>
                        </td>
                    </tr>
                `;
            }).join('');
        }
        
        // --- GENERAL ACTIONS ---
        
        function openBrowseTutors() {
            // Scrolls to the tutors section
            document.getElementById('available-tutors-card').scrollIntoView({ behavior: 'smooth' });
        }
        
        function closeModal(id) { document.getElementById(id).classList.remove('active'); }
        
        // Placeholder for switchTab logic used by Admin/Tutor views
        function switchTab(name, btn) {
            document.querySelectorAll('.top-nav-links a').forEach(a => a.classList.remove('active'));
            document.querySelectorAll('.tab').forEach(e => e.classList.remove('active'));
            btn.classList.add('active');
            
            // This would normally show the correct content division for the Admin/Tutor tabs
            // document.querySelectorAll('.content').forEach(e => e.classList.remove('active'));
            // document.getElementById(`tab-${name}`).classList.add('active');
        }

        // --- AUTH FORMS ---

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


        // --- BOOKING LOGIC ---
        
        async function openBookingModal(sessionId, tutorId, tutorName) {
            document.getElementById('booking-modal').classList.add('active');
            
            document.getElementById('bk-name').value = user.name || '';
            document.getElementById('bk-email').value = user.email || '';
            
            document.getElementById('booking-session-id').value = sessionId || '';
            document.getElementById('session-tutor-id').value = tutorId || '';

            const infoBox = document.getElementById('booking-info');
            if (sessionId) {
                const session = sessionsData.find(s => s.id === sessionId);
                infoBox.innerHTML = `
                    <p>Booking with <strong>${tutorName}</strong></p>
                    <p>Subject: <strong>${session.subject}</strong></p>
                    <p>Time: <strong>${new Date(session.session_date).toLocaleDateString()} @ ${session.start_time}</strong></p>
                `;
            } else {
                infoBox.innerHTML = `<p>Booking Inquiry for <strong>${tutorName}</strong></p><p>Please specify subject and preferred time in notes.</p>`;
            }
        }
        
        async function confirmBooking(e) {
            e.preventDefault();
            
            const sessionId = document.getElementById('booking-session-id').value;
            const tutorId = document.getElementById('session-tutor-id').value;
            
            if (!sessionId && !tutorId) {
                 alert("Cannot confirm booking without a valid session or tutor selected.");
                 return;
            }
            
            const payload = {
                session_id: sessionId,
                tutor_id: tutorId, 
                student_name: document.getElementById('bk-name').value,
                student_email: document.getElementById('bk-email').value,
                student_phone: document.getElementById('bk-phone').value,
                notes: document.getElementById('bk-notes').value
            };
            
            try {
                await apiCall(`${API_BASE}/bookings`, 'POST', payload);
                alert("Booking submitted! Check My Sessions for status.");
                closeModal('booking-modal'); 
                loadData(); 
            } catch (error) {
                 console.error("Booking failed:", error);
            }
        }
        
        async function cancelBooking(id) { 
            if(confirm('Are you sure you want to cancel this booking?')) { 
                await apiCall(`${API_BASE}/bookings/${id}`, 'DELETE'); 
                loadData(); 
            } 
        }

        // --- API UTILITY ---
        async function apiCall(url, method, body = null) {
            const headers = { 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}` };
            const opts = { method, headers };
            if(body) opts.body = JSON.stringify(body);
            const res = await fetch(url, opts);
            if(!res.ok) { 
                const err = await res.json(); 
                alert(err.message || `API Error: ${res.status}`); 
                throw new Error(err.message || 'API call failed'); 
            }
            return res.json().catch(() => {});
        }
    </script>
</body>
</html>