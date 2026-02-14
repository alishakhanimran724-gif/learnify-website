<?php
include 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>About Us - Learnify</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <style>
      :root {
        --primary-color: #00027a;
        --secondary-color: #00027a;
        --text-color: white;
        --card: #0c0b2b;
        --bg: #fdfdfd;
        --text-color-secondary: #555;
        --btn: #00027a;
      }

      body {
        background-color: var(--bg);
        color: var(--text-color-secondary);
        font-family: "Poppins", sans-serif;
      }

      .hero {
        background: linear-gradient(
             #00027a90,
            #00027ace
          ),
          url("https://images.unsplash.com/photo-1600880292203-757bb62b4baf")
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
        grid-template-rows: auto auto;
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
        line-height: 1.6;
      }

      .btn-primary-custom {
        background-color: var(--btn);
        border: none;
        color: var(--text-color);
        padding: 12px 30px;
        border-radius: 6px;
        font-size: 1.1rem;
        transition: all 0.3s ease;
      }
      
      .btn-primary-custom:hover {
        background-color: var(--secondary-color);
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 2, 122, 0.3);
      }

      .section-title {
        color: var(--primary-color);
        font-weight: 700;
        margin-bottom: 1.5rem;
      }

      .values .card {
        border: none;
        border-radius: 10px;
        background-color: var(--card);
        color: var(--text-color);
        padding: 20px;
        transition: transform 0.3s ease;
      }
      .values .card:hover {
        transform: translateY(-5px);
      }

      .timeline {
        border-left: 3px solid var(--primary-color);
        padding-left: 20px;
      }
      .timeline-event {
        margin-bottom: 30px;
      }
      .timeline-event h5 {
        color: var(--primary-color);
      }
      .team img {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 50%;
        border: 3px solid var(--primary-color);
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
        <h1>About Learnify</h1>
        <p>
          Shaping the future of education with innovation, accessibility, and
          impact.
        </p>
      </div>
    </section>

    <!-- Our Story -->
    <section class="py-5">
      <div class="container">
        <div class="row align-items-center g-4">
          <div class="col-lg-6">
            <img
              src="https://images.unsplash.com/photo-1584697964154-3a7f5d5aa5a7"
              class="img-fluid rounded"
              alt="Students learning"
            />
          </div>
          <div class="col-lg-6">
            <h2 class="section-title">Our Story</h2>
            <p>
              Learnify was born from a shared belief that education has the
              power to transform lives. Our founders, educators and
              technologists, came together in 2018 to address a global problem:
              millions of students lack access to quality learning resources.
            </p>
            <p>
              From a small online tutoring platform, we've evolved into a global
              education ecosystem — offering interactive courses, personalized
              learning plans, and tools for teachers to connect with students
              anywhere in the world.
            </p>
            <p>
              Today, Learnify serves over 500,000 learners across 45+ countries,
              helping students unlock their full potential.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Mission & Vision -->
    <section class="py-5" style="background-color: #f6f6f6">
      <div class="container text-center">
        <div class="row">
          <div class="col-md-6">
            <h2 class="section-title">Our Mission</h2>
            <p>
              To make high-quality education accessible, engaging, and inclusive
              for learners everywhere.
            </p>
          </div>
          <div class="col-md-6">
            <h2 class="section-title">Our Vision</h2>
            <p>
              To be the world's most trusted education platform, empowering the
              next generation of thinkers, creators, and leaders.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Core Values -->
    <section class="values py-5">
      <div class="container">
        <h2 class="section-title text-center">Our Core Values</h2>
        <div class="row g-4">
          <div class="col-md-3">
            <div class="card text-center">
              <h5>Innovation</h5>
              <p>
                Leveraging technology to create impactful learning experiences.
              </p>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card text-center">
              <h5>Accessibility</h5>
              <p>Making learning available to anyone, anywhere, anytime.</p>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card text-center">
              <h5>Collaboration</h5>
              <p>Building strong connections between learners and educators.</p>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card text-center">
              <h5>Excellence</h5>
              <p>Delivering high-quality content and exceptional support.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Our Journey (Timeline) -->
    <section class="py-5">
      <div class="container">
        <h2 class="section-title text-center">Our Journey</h2>
        <div class="timeline">
          <div class="timeline-event">
            <h5>2018 - Founded</h5>
            <p>Learnify was launched as a small online tutoring startup.</p>
          </div>
          <div class="timeline-event">
            <h5>2020 - Global Expansion</h5>
            <p>Reached 100,000 learners across 15 countries.</p>
          </div>
          <div class="timeline-event">
            <h5>2022 - Innovation Award</h5>
            <p>Recognized for excellence in educational technology.</p>
          </div>
          <div class="timeline-event">
            <h5>2024 - Community Growth</h5>
            <p>
              Now serving 500,000+ learners and thousands of educators
              worldwide.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Meet the Team -->
    <section class="team py-5" style="background-color: #f6f6f6">
      <div class="container">
        <h2 class="section-title text-center">Meet Our Leadership</h2>
        <div class="row g-4 text-center">
          <div class="col-md-3">
            <img
              src="https://randomuser.me/api/portraits/men/32.jpg"
              alt="CEO"
            />
            <h6 class="mt-3">Alex Carter</h6>
            <p>Co-Founder & CEO</p>
          </div>
          <div class="col-md-3">
            <img
              src="https://randomuser.me/api/portraits/women/44.jpg"
              alt="CTO"
            />
            <h6 class="mt-3">Sophia Kim</h6>
            <p>Chief Technology Officer</p>
          </div>
          <div class="col-md-3">
            <img
              src="https://randomuser.me/api/portraits/men/54.jpg"
              alt="Head of Education"
            />
            <h6 class="mt-3">David Lee</h6>
            <p>Head of Education</p>
          </div>
          <div class="col-md-3">
            <img
              src="https://randomuser.me/api/portraits/women/65.jpg"
              alt="Marketing Director"
            />
            <h6 class="mt-3">Emma Johnson</h6>
            <p>Marketing Director</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Impact Stats -->
    <section
      class="py-5 text-center text-white"
      style="background-color: var(--primary-color)"
    >
      <div class="container">
        <h2>Our Global Impact</h2>
        <div class="row mt-4">
          <div class="col-md-3">
            <h3>500K+</h3>
            <p>Learners</p>
          </div>
          <div class="col-md-3">
            <h3>45+</h3>
            <p>Countries</p>
          </div>
          <div class="col-md-3">
            <h3>2,000+</h3>
            <p>Educators</p>
          </div>
          <div class="col-md-3">
            <h3>1M+</h3>
            <p>Lessons Delivered</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Testimonials -->
    <section class="py-5">
      <div class="container">
        <h2 class="section-title text-center">What Our Students Say</h2>
        <div class="row g-4">
          <div class="col-md-4">
            <div class="card p-3">
              <p>
                "Learnify has completely transformed the way I study. The
                courses are engaging and easy to follow."
              </p>
              <strong>- Aisha M.</strong>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card p-3">
              <p>
                "I love how accessible the platform is. I can learn at my own
                pace and revisit topics anytime."
              </p>
              <strong>- Carlos R.</strong>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card p-3">
              <p>
                "As a teacher, Learnify has helped me connect with students
                globally. It's an amazing tool."
              </p>
              <strong>- Priya K.</strong>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Call to Action -->
    <section
      class="py-5 text-center"
      style="background-color: var(--primary-color); color: white"
    >
      <div class="container">
        <h2>Be Part of Our Story</h2>
        <p>
          Join our mission to make education accessible to everyone, everywhere.
        </p>
        <a href="careers.html" class="btn btn-primary-custom">Work With Us</a>
      </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
<?php
include 'includes/footer.php';
?>