<?php 
include 'includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Careers - Learnify</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css"
      rel="stylesheet"
    />
    <style>
      :root {
        --primary-color: #00027a; /* Royal Blue */
        --secondary-color: #00027a;
        --text-color: white;
        --card: #00027a;
        --bg: #fdfdfd;
        --text-color-secondary: #b5b3b3;
        --btn: #00027a;
      }

      body {
        background-color: var(--bg);
        font-family: "Poppins", sans-serif;
        color: #333;
      }

      .hero {
        background: linear-gradient(
            #00027a90,
            #00027ace
          ),
          url("https://images.unsplash.com/photo-1596495577886-d920f1fb7238?auto=format&fit=crop&w=1950&q=80")
            center/cover no-repeat;
        min-height: 100vh;
        color: var(--text-color);
        display: grid;
        grid-template-rows: 1fr auto 1fr;
        grid-template-columns: 1fr;
        place-items: center;
        text-align: center;
        padding: 2rem;
      }

      .hero-content {
        grid-row: 2;
        display: grid;
        grid-template-rows: auto auto auto;
        gap: 1.5rem;
        max-width: 800px;
        width: 100%;
      }

      .hero h1 {
        font-size: 3rem;
        font-weight: bold;
        margin: 0;
      }

      .hero p {
        font-size: 1.2rem;
        margin: 0;
        opacity: 0.9;
      }

      .btn-primary-custom {
        background-color: var(--btn);
        border: none;
        color: var(--text-color);
        padding: 12px 30px;
        border-radius: 6px;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        justify-self: center;
        width: fit-content;
      }

      .btn-primary-custom:hover {
        background-color: var(--secondary-color);
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(74, 60, 154, 0.3);
      }

      .why-us .card {
        background-color: var(--card);
        color: var(--text-color);
        border: none;
        border-radius: 10px;
        transition: transform 0.2s;
      }

      .why-us .card:hover {
        transform: translateY(-5px);
      }

      .job-listings .card {
        border-radius: 10px;
        border: none;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
      }

      .form-section {
        background-color: var(--primary-color);
        padding: 40px;
        border-radius: 10px;
        color: var(--text-color);
      }

      /* Responsive adjustments */
      @media (max-width: 768px) {
        .hero h1 {
          font-size: 2.2rem;
        }
        
        .hero p {
          font-size: 1rem;
        }
        
        .hero {
          padding: 1rem;
        }
      }
    </style>
  </head>
  <body>
    <!-- Hero Section -->
    <section class="hero">
      <div class="hero-content">
        <h1>Join the Learnify Family</h1>
        <p>Empower learners everywhere with your passion for education.</p>
        <a href="#open-positions" class="btn btn-primary-custom">View Opportunities</a>
      </div>
    </section>

    <!-- Why Work With Us -->
    <section class="why-us py-5">
      <div class="container">
        <div class="row text-center mb-4">
          <h2 style="color: var(--primary-color)">Why Teach or Work With Us</h2>
        </div>
        <div class="row g-4">
          <div class="col-md-3">
            <div class="card p-3 h-100 text-center">
              <i class="bi bi-laptop fs-1 mb-3"></i>
              <h5>Teach From Anywhere</h5>
              <p>Work remotely and connect with learners worldwide.</p>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card p-3 h-100 text-center">
              <i class="bi bi-globe-americas fs-1 mb-3"></i>
              <h5>Global Impact</h5>
              <p>Help break barriers in access to quality education.</p>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card p-3 h-100 text-center">
              <i class="bi bi-graph-up-arrow fs-1 mb-3"></i>
              <h5>Grow Your Skills</h5>
              <p>We provide training and tools to help you excel.</p>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card p-3 h-100 text-center">
              <i class="bi bi-people fs-1 mb-3"></i>
              <h5>Supportive Community</h5>
              <p>Join a network of passionate educators and creators.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Open Positions -->
    <section id="open-positions" class="job-listings py-5">
      <div class="container">
        <div class="text-center mb-4">
          <h2 style="color: var(--primary-color)">Current Opportunities</h2>
        </div>
        <div class="row g-4">
          <div class="col-md-6">
            <div class="card p-3">
              <h5>Online Tutor – Mathematics</h5>
              <p>
                Guide students through engaging, interactive online lessons.
              </p>
              <a
                href="mailto:careers@learnify.com"
                class="btn btn-primary-custom"
                >Apply Now</a
              >
            </div>
          </div>
          <div class="col-md-6">
            <div class="card p-3">
              <h5>Curriculum Designer</h5>
              <p>
                Create innovative learning materials that inspire curiosity.
              </p>
              <a
                href="mailto:careers@learnify.com"
                class="btn btn-primary-custom"
                >Apply Now</a
              >
            </div>
          </div>
          <div class="col-md-6">
            <div class="card p-3">
              <h5>Educational Video Producer</h5>
              <p>Produce high-quality learning videos for online courses.</p>
              <a
                href="mailto:careers@learnify.com"
                class="btn btn-primary-custom"
                >Apply Now</a
              >
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Application Form -->
    <section class="py-5">
      <div class="container">
        <div class="form-section">
          <h3 class="mb-4">Apply to Join Us</h3>
          <form
            action="careers_controller.php"
            method="POST"
            enctype="multipart/form-data"
          >
            <div class="mb-3">
              <label for="name" class="form-label">Full Name</label>
              <input type="text" class="form-control" name="name" required />
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">Email Address</label>
              <input type="email" class="form-control" name="email" required />
            </div>
            <div class="mb-3">
              <label for="resume" class="form-label"
                >Upload Resume / Portfolio</label
              >
              <input type="file" class="form-control" name="resume" required />
            </div>
            <div class="mb-3">
              <label for="message" class="form-label"
                >Tell us about your passion for education</label
              >
              <textarea class="form-control" name="message" rows="4"></textarea>
            </div>
            <button type="submit" class="btn btn-primary-custom">
              Submit Application
            </button>
          </form>
        </div>
      </div>
    </section>

    <!-- Footer CTA -->
    <footer
      class="text-center py-4"
      style="background-color: var(--primary-color); color: var(--text-color)"
    >
      <p class="mb-2">Let's shape the future of learning together.</p>
      <a href="#open-positions" class="btn btn-primary-custom">Apply Today</a>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
<?php
include 'includes/footer.php';  
?>