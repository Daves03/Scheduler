// resources/js/app.jsx
import './bootstrap';
import '../css/app.css'; 

import React from 'react';
import ReactDOM from 'react-dom/client';
// Note: "Component" (singular) to match your folder
import TutorScheduler from './Components/TutorScheduler'; 

if (document.getElementById('app')) {
    const root = ReactDOM.createRoot(document.getElementById('app'));
    root.render(<TutorScheduler />);
}