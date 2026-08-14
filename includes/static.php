<section id="home" class="hero-section">
  <div class="container-fluid h-100">
    <div class="row align-items-center h-100">
      <!-- Hero Background Image - Scrolls with page -->
      <div class="hero-background" style="background-image: linear-gradient(rgba(0, 0, 0, 0.15), rgba(0, 0, 0, 0.15)), url('img/school.jpg'); background-size: cover; background-position: center;">
      </div>

      <!-- Hero Content - Centered in container -->
      <div class="col-lg-12 hero-content" data-aos="fade-up">
        <div style="max-width: 700px; margin: 0 auto 0 10%;">
          <h1 class="fw-bold text-white" style="line-height: 1.1; margin-bottom: 20px; letter-spacing: -1px; text-shadow: 0 2px 4px rgba(0,0,0,0.3);">
            <span style="font-size: 3rem; display: block;">Welcome to</span>
            <span style="font-size: 4rem; display: block; margin-top: -5px;">Faculty Union</span>
          </h1>
          <div style="width: 50px; height: 3px; background-color: var(--primary-maroon, #8c1d1d); margin-bottom: 20px;"></div>
          
          <p class="text-white" style="font-size: 1.2rem; font-weight: 500; margin-bottom: 15px; text-shadow: 0 1px 3px rgba(0,0,0,0.3);">
            Empowering Educators, Protecting Excellence
          </p>

          <p class="text-white mb-4" style="font-size: 1rem; line-height: 1.6; max-width: 500px; font-weight: 400; opacity: 0.95; text-shadow: 0 1px 3px rgba(0,0,0,0.3);">
            We are committed to fostering a strong academic community<br>
            built on integrity, collaboration, and excellence.
          </p>

          <!-- CTA Buttons -->
          <div class="d-flex gap-3 flex-wrap">
            <a href="#about" class="btn d-flex align-items-center justify-content-center" style="background-color: var(--primary-maroon, #8c1d1d); color: white; border: none; padding: 14px 30px; font-weight: 700; font-size: 0.85rem; letter-spacing: 1px; border-radius: 6px;">
              LEARN MORE <i class="bi bi-arrow-right ms-2" style="font-size: 1.1rem;"></i>
            </a>
            <a href="includes/all/all_events.php" class="btn d-flex align-items-center justify-content-center" style="background-color: transparent; color: white; border: 1px solid rgba(255,255,255,0.8); padding: 14px 30px; font-weight: 700; font-size: 0.85rem; letter-spacing: 1px; border-radius: 6px;">
              VIEW EVENTS <i class="bi bi-calendar-event ms-2" style="font-size: 1.1rem;"></i>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<style>
  .hero-section {
    position: relative;
    height: 100vh;
    overflow: hidden;
    display: flex;
    align-items: center;
    padding: 0 40px;
  }

  .hero-section .container-fluid {
    height: 100%;
  }

  .hero-background {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 0;
  }

  .hero-content {
    position: relative;
    z-index: 2;
  }

  .hero-section h1 {
    line-height: 1.1;
    font-weight: 700;
  }

  .hero-section .lead {
    font-size: 1.2rem;
    font-weight: 600;
  }

  .hero-section p {
    /* text shadow removed to let inline style handle it */
  }

  .hero-section .btn {
    transition: all 0.3s ease;
  }

  .hero-section .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
  }

  @media (max-width: 768px) {
    .hero-section {
      height: 90vh;
      padding: 0 20px;
    }

    .hero-section h1 {
      font-size: 2rem;
    }

    .hero-section .lead {
      font-size: 1rem;
    }

    .d-flex.gap-2 {
      flex-direction: column;
    }

    .d-flex.gap-2 .btn {
      width: 100%;
    }
  }
</style>
