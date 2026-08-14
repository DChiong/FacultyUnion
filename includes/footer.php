<?php
// Load contact info from DB with safe fallbacks
require_once(__DIR__ . '/../config/database.php');
$database = new Database();
$db = $database->getConnection();
try {
    if ($db instanceof PDO) {
        $contact_query = $db->query("SELECT * FROM contact_info WHERE id = 1");
        $contact = $contact_query->fetch(PDO::FETCH_ASSOC);
    } else {
        $contact = false;
    }
} catch (Exception $e) {
    $contact = false;
}
$address = $contact['address'] ?? 'Faculty Union Office, Western Mindanao State University, Normal Rd, Zamboanga City, 7000';
$phone = $contact['phone'] ?? '+63 62 991 1040';
$hours = $contact['hours'] ?? 'Mon - Fri: 8:00 AM - 5:00 PM';
$email = $contact['email'] ?? 'facultyunion@wmsu.edu.ph';
$fb_url = $contact['facebook_url'] ?? 'https://www.facebook.com/WMSUFacultyUnion';
$fb_name = $contact['facebook_name'] ?? 'WMSU Faculty Union';
?>

<style>
    .footer-contact {
        background-color: #fbfbfc; /* Very light background from image */
        padding: 40px 0 60px 0; /* Reduced top padding */
        font-family: 'Inter', sans-serif;
    }

    .footer-contact .section-title {
        color: #0f4b46; /* Dark teal color */
        font-weight: 700;
        font-size: 2.2rem;
        margin-bottom: 10px;
    }

    .footer-contact .title-underline {
        width: 120px;
        height: 3px;
        background-color: #8c1d1d; /* Maroon underline */
        margin: 0 auto 15px;
        border-radius: 2px;
    }

    .footer-contact .section-subtitle {
        color: #6b7280;
        font-size: 1.05rem;
        margin-bottom: 25px; /* Reduced gap between subtitle and cards */
    }

    .contact-card {
        background: linear-gradient(135deg, #8c1d1d 0%, #681212 100%); /* Maroon card */
        border-radius: 12px;
        padding: 30px;
        height: 100%;
        display: flex;
        align-items: flex-start;
        box-shadow: 0 8px 25px rgba(140, 29, 29, 0.15);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid rgba(255,255,255,0.1);
    }

    .contact-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 35px rgba(140, 29, 29, 0.3);
    }

    .icon-wrapper {
        width: 55px;
        height: 55px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 20px;
        flex-shrink: 0;
        background: rgba(255, 255, 255, 0.15); /* Semi-transparent white */
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .icon-wrapper i {
        font-size: 1.5rem;
        color: #ffffff; /* White icon */
    }

    .card-info h5 {
        color: #ffffff; /* White title */
        font-weight: 700;
        font-size: 1.15rem;
        margin-bottom: 8px;
    }

    .card-info p {
        color: #f8d7da; /* Light pink/white text */
        font-size: 0.95rem;
        margin-bottom: 0;
        line-height: 1.6;
    }
    
    .card-info a {
        color: #ffffff;
        text-decoration: none;
        transition: opacity 0.2s;
    }
    
    .card-info a:hover {
        opacity: 0.8;
    }
</style>

<footer id="footer" class="footer-contact">
    <div class="container">
        <div class="text-center">
            <h2 class="section-title">Contact Us</h2>
            <div class="title-underline"></div>
            <p class="section-subtitle">Get in touch with the Faculty Union for inquiries and support.</p>
        </div>

        <div class="row g-4 mt-2">
            <!-- Address -->
            <div class="col-md-6">
                <div class="contact-card">
                    <div class="icon-wrapper light-bg">
                        <i class="bi bi-geo-alt"></i>
                    </div>
                    <div class="card-info">
                        <h5>Address</h5>
                        <p><?php echo nl2br(htmlspecialchars($address)); ?></p>
                    </div>
                </div>
            </div>

            <!-- Call Us -->
            <div class="col-md-6">
                <div class="contact-card">
                    <div class="icon-wrapper teal-bg">
                        <i class="bi bi-telephone"></i>
                    </div>
                    <div class="card-info">
                        <h5>Call Us</h5>
                        <p>
                            <?php echo htmlspecialchars($phone); ?><br>
                            <?php echo htmlspecialchars($hours); ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Email Us -->
            <div class="col-md-6">
                <div class="contact-card">
                    <div class="icon-wrapper light-bg">
                        <i class="bi bi-envelope"></i>
                    </div>
                    <div class="card-info">
                        <h5>Email Us</h5>
                        <p>
                            <a href="mailto:<?php echo htmlspecialchars($email); ?>" style="color: inherit; text-decoration: none;"><?php echo htmlspecialchars($email); ?></a><br>
                            For general inquiries
                        </p>
                    </div>
                </div>
            </div>

            <!-- Follow Us -->
            <div class="col-md-6">
                <div class="contact-card">
                    <div class="icon-wrapper light-bg">
                        <i class="bi bi-facebook"></i>
                    </div>
                    <div class="card-info">
                        <h5>Follow Us</h5>
                        <p>
                            <a href="<?php echo htmlspecialchars($fb_url); ?>" target="_blank" style="color: inherit; text-decoration: none;"><?php echo htmlspecialchars($fb_name); ?></a><br>
                            For updates and events
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
