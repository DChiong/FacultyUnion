<?php
session_start();
// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}
require_once('../config/database.php');
require_once('sidebar.php');
$navtext = "Dashboard";
require_once('navbar.php');
$database = new Database();
$db = $database->getConnection();

// Quick Stats
$count_officers = 0;
try { $count_officers = $db->query("SELECT COUNT(*) FROM officers")->fetchColumn(); } catch(Exception $e) {}

$count_videos = 0;
try { $count_videos = $db->query("SELECT COUNT(*) FROM admin_videos")->fetchColumn(); } catch(Exception $e) {}

$count_events = 0;
try { $count_events = $db->query("SELECT COUNT(*) FROM events")->fetchColumn(); } catch(Exception $e) {}

$count_awards = 0;
try { $count_awards = $db->query("SELECT COUNT(*) FROM awards")->fetchColumn(); } catch(Exception $e) {}

date_default_timezone_set("Asia/Manila"); 
        $hour = date("H");
        $greeting = "Hello";

        if ($hour >= 5 && $hour < 12) {
            $greeting = "Good Morning, Admin!";
        } elseif ($hour >= 12 && $hour < 18) {
            $greeting = "Good Afternoon, Admin!";
        } elseif ($hour >= 18 && $hour < 22) {
            $greeting = "Good Evening, Admin!";
        } else {
            $greeting = "Good Night, Admin!";
        }

        $today = date("Y-m-d");
        $current_date = date("l, F j, Y");
    ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard - WMSU FU</title>
    <link rel="icon" href="../images/facultyunion.png">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #6e1414; /* maroon */
            --secondary: #4f0f0f; /* darker maroon */
            --accent: #8c1d1d;
            --light: #fff7f7;
            --today: #b71c1c;
            --gray-light: #f0e5e5;
            --shadow: rgba(139, 23, 23, 0.12);
            --text-dark: #3b0b0b;
        }

        .main-content { margin-left: 260px; padding: 30px; }
        .card-stat { border-left: 5px solid var(--accent); }
        .header-logo { width: 100px; height: 100px; object-fit: contain; display: block; filter: drop-shadow(0 10px 18px rgba(0,0,0,.35)); }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 1.2rem 1.4rem;
            border-radius: 12px;
            box-shadow: 0 6px 22px var(--shadow);
            margin-bottom: 1.25rem;
        }

        .greeting { font-size: 1.6rem; font-weight:700; margin-bottom:4px; }
        .date-display { font-size: 0.95rem; opacity: 0.95; }

        .clock {
            font-size: 2rem;
            font-weight:700;
            padding: 0.45rem 1rem;
            border-radius: 10px;
            text-align: center;
            min-width: 150px;
            background: rgba(255,255,255,0.12);
            color: #fff;
            box-shadow: inset 0 -2px 0 rgba(0,0,0,0.06);
        }

        .no-underline { text-decoration:none !important; }

        .homelink { text-decoration:none; color: #fff; transition: .3s ease; }
        .homelink:hover { color: #ffecec; }

        @media (max-width:767px){ .main-content{ margin-left:0; padding:16px; } .clock{width:100%;} }
    </style>
</head>
<body>


<div class="main-content">
    <div class="content-page px-3">
        
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
            <div class="rounded p-4 text-start mt-4">
            <div class="header">
        <div class="greeting-section">
            <div class="greeting"><?php echo $greeting; ?></div>
            <div class="date-display"><?php echo $current_date; ?></div>
            <div class="home mt-3">
                <h6><a href="/faculty-union/index.php#home" target="_blank" class="homelink">Go to homepage</a></h6>
            </div>
        </div>
        
        <div class="header-right d-flex align-items-center">
           
            <div class="ml-3">
                <div class="clock" id="clock"></div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4 mb-2">
        <div class="col-md-3 mb-3">
            <div class="card card-stat shadow-sm p-3 h-100">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-muted">Total Officers</h6>
                        <h3 style="color: black; font-weight: bold;"><?php echo $count_officers; ?></h3>
                    </div>
                    <i class="fas fa-user-tie fa-2x text-muted"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card card-stat shadow-sm p-3 h-100">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-muted">Total Events</h6>
                        <h3 style="color: black; font-weight: bold;"><?php echo $count_events; ?></h3>
                    </div>
                    <i class="fas fa-calendar-alt fa-2x text-muted"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card card-stat shadow-sm p-3 h-100">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-muted">Total Awards</h6>
                        <h3 style="color: black; font-weight: bold;"><?php echo $count_awards; ?></h3>
                    </div>
                    <i class="fas fa-award fa-2x text-muted"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card card-stat shadow-sm p-3 h-100">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-muted">Total Videos</h6>
                        <h3 style="color: black; font-weight: bold;"><?php echo $count_videos; ?></h3>
                    </div>
                    <i class="fas fa-play-circle fa-2x text-muted"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-5">
            <div class="card shadow-sm border-0" style="border-radius: 12px; height: 100%;">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <h5 class="mb-0" style="color: var(--text-dark); font-weight: bold;"><i class="fas fa-bolt mr-2" style="color: var(--accent);"></i> Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="manage_officers.php" class="list-group-item list-group-item-action border-0 px-0 d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-user-plus mr-2 text-muted"></i> Add New Officer</span>
                            <i class="fas fa-chevron-right text-muted" style="font-size: 0.8rem;"></i>
                        </a>
                        <a href="manage_events.php" class="list-group-item list-group-item-action border-0 px-0 d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-calendar-plus mr-2 text-muted"></i> Create Event</span>
                            <i class="fas fa-chevron-right text-muted" style="font-size: 0.8rem;"></i>
                        </a>
                        <a href="manage_about_topics.php" class="list-group-item list-group-item-action border-0 px-0 d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-file-alt mr-2 text-muted"></i> Update About Info</span>
                            <i class="fas fa-chevron-right text-muted" style="font-size: 0.8rem;"></i>
                        </a>
                        <a href="manage_contact.php" class="list-group-item list-group-item-action border-0 px-0 d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-address-book mr-2 text-muted"></i> Update Contact</span>
                            <i class="fas fa-chevron-right text-muted" style="font-size: 0.8rem;"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
            </div>
            </div>
        </div>
    </div>
</div>
    


    
    <script>
        function updateClockAndGreeting() {
            const now = new Date();
            const hours = now.getHours();
            const minutes = now.getMinutes().toString().padStart(2, '0');
            const seconds = now.getSeconds().toString().padStart(2, '0');

            const timeString = `${(hours % 12 || 12)}:${minutes}:${seconds} ${hours >= 12 ? 'PM' : 'AM'}`;
            const clockEl = document.getElementById("clock");
            if (clockEl) clockEl.innerText = timeString;

            const greetingEl = document.querySelector(".greeting");
            const dateDisplayEl = document.querySelector(".date-display");
            if (greetingEl && dateDisplayEl) {
                const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                const currentDateStr = now.toLocaleDateString('en-US', options);

                let greeting = "Hello";
                if (hours >= 5 && hours < 12) {
                    greeting = "Good Morning, Admin!";
                } else if (hours >= 12 && hours < 18) {
                    greeting = "Good Afternoon, Admin!";
                } else if (hours >= 18 && hours < 22) {
                    greeting = "Good Evening, Admin!";
                } else {
                    greeting = "Good Night, Admin!";
                }

                greetingEl.textContent = greeting;
                dateDisplayEl.textContent = currentDateStr;
            }
        }


        setInterval(updateClockAndGreeting, 1000);
        updateClockAndGreeting();
    </script>


</body>
</html>
