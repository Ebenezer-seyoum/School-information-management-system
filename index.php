<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Balela Secondary School information and academic management portal in Sidama Region, Ethiopia.">
    <title>Balela Secondary School | Sidama Region, Ethiopia</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="Home/css/balela-home.css">
</head>
<body>
    <div class="topbar">
        <div class="container topbar-inner">
            <span><span class="topbar-dot"></span> Academic information management system</span>
            <span>Grades 9–12 &nbsp;·&nbsp; Sidama Region, Ethiopia</span>
        </div>
    </div>

    <header class="site-header">
        <div class="container nav-wrap">
            <a class="brand" href="index.php" aria-label="Balela Secondary School home">
                <span class="brand-mark"><img src="assets/img/balela.png" alt="Balela Secondary School logo"></span>
                <span><strong>Balela</strong><small>Secondary School</small></span>
            </a>
            <nav class="main-nav" aria-label="Main navigation">
                <a class="active" href="index.php">Home</a>
                <a href="#about">About</a>
                <a href="#academics">Academics</a>
                <a href="#gallery">Gallery</a>
                <a href="contact.php">Contact</a>
            </nav>
            <a class="button button-small" href="login.php">Portal login <span>↗</span></a>
        </div>
    </header>

    <main>
        <section class="hero" aria-labelledby="hero-title">
            <div class="hero-photo" role="img" aria-label="Balela school campus surrounded by trees"></div>
            <div class="hero-shade"></div>
            <div class="container hero-content">
                <p class="eyebrow light">Welcome to Balela Secondary School</p>
                <h1 id="hero-title">Learning today.<br><span>Leading tomorrow.</span></h1>
                <p class="hero-copy">A community of learners, teachers and families growing together in Sidama Region, Ethiopia.</p>
                <div class="hero-actions">
                    <a class="button" href="login.php">Open school portal <span>↗</span></a>
                    <a class="button button-ghost" href="#about">Discover our school</a>
                </div>
            </div>
            <div class="hero-scroll">Scroll to explore <span>↓</span></div>
        </section>

        <section class="intro section-pad" id="about">
            <div class="container intro-grid">
                <div>
                    <p class="eyebrow">Our school</p>
                    <h2>A clear path for every learner.</h2>
                    <p class="lead">Balela Secondary School supports students with focused teaching, responsible leadership and a strong connection to the local community.</p>
                    <p>Our academic information system helps directors, teachers, students and families manage the school journey from class placement and attendance to marks, grades and points.</p>
                    <a class="text-link" href="about.php">Learn more about Balela <span>→</span></a>
                </div>
                <div class="intro-image image-frame">
                    <img src="assets/img/balelaHome.png" alt="Green school grounds at Balela Secondary School">
                    <div class="image-note"><strong>Balela Secondary School</strong><span>Sidama Region, Ethiopia</span></div>
                </div>
            </div>
        </section>

        <section class="academics section-pad" id="academics">
            <div class="container">
                <div class="section-heading">
                    <div><p class="eyebrow">Academic life</p><h2>Built around student progress.</h2></div>
                    <p>Simple, dependable information for the people who guide learning every day.</p>
                </div>
                <div class="feature-grid">
                    <article class="feature-card"><span class="feature-icon">01</span><h3>Organised classes</h3><p>Directors define grades, sections and classes so every student has a clear academic home.</p></article>
                    <article class="feature-card feature-card-blue"><span class="feature-icon">02</span><h3>Marks, grades & points</h3><p>Students and families can understand achievement through marks, letter grades, points and status.</p></article>
                    <article class="feature-card"><span class="feature-icon">03</span><h3>Shared responsibility</h3><p>Teachers enter results, directors review records and students follow their own progress.</p></article>
                </div>
            </div>
        </section>

        <section class="gallery section-pad" id="gallery">
            <div class="container">
                <div class="section-heading"><div><p class="eyebrow">Life at Balela</p><h2>Our community in action.</h2></div><p>Moments from learning, leadership and the school community.</p></div>
                <div class="gallery-grid">
                    <figure class="gallery-item gallery-large"><img src="assets/img/balelaHome.png" alt="Balela Secondary School campus"><figcaption>Our campus</figcaption></figure>
                    <figure class="gallery-item"><img src="Home/images/banner/banner_1.jpg" alt="Students learning in a school environment"><figcaption>Learning together</figcaption></figure>
                    <figure class="gallery-item"><img src="Home/images/banner/banner_2.jpg" alt="School community gathering"><figcaption>School community</figcaption></figure>
                    <figure class="gallery-item"><img src="Home/images/banner/banner_3.png" alt="Academic activities at school"><figcaption>Academic activities</figcaption></figure>
                </div>
                <p class="gallery-note">The school’s event photographs can be added to these gallery cards as the official media collection grows.</p>
            </div>
        </section>

        <section class="portal-cta">
            <div class="container cta-inner"><div><p class="eyebrow light">One connected school</p><h2>Keep the academic journey moving.</h2><p>Access the secure portal for student records, class information and academic reports.</p></div><a class="button button-white" href="login.php">Go to portal <span>↗</span></a></div>
        </section>
    </main>

    <footer class="site-footer">
        <div class="container footer-grid">
            <div><a class="brand brand-footer" href="index.php"><span class="brand-mark"><img src="assets/img/balela.png" alt=""></span><span><strong>Balela</strong><small>Secondary School</small></span></a><p>Academic information management for a stronger school community.</p></div>
            <div><h3>Explore</h3><a href="#about">About the school</a><a href="#academics">Academic life</a><a href="#gallery">School gallery</a></div>
            <div><h3>Contact</h3><p>Balela Secondary School<br>Sidama Region, Ethiopia</p><a href="contact.php">Send a message →</a></div>
        </div>
        <div class="container footer-bottom"><span>© <?php echo date('Y'); ?> Balela Secondary School</span><span>Learning · Leadership · Community</span></div>
    </footer>
</body>
</html>
