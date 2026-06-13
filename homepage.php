<?php include_once 'navbar.php';
require_once 'dbcon.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QUICKLAB - A FIRST STEP TOWARDS YOUR HEALTH</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom Styles -->
    <style>
        body {
            background-color: #fafafa;
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, #e8f4ff, #ffffff);
            border-radius: 20px;
            padding: 50px 30px;
            margin-top: 25px;
        }

        .hero-img {
            max-width: 700px;
            max-height: 300px;
        }

        .hero-title {
            font-size: 42px;
            font-weight: 700;
            color: #333;
        }

        .hero-subtitle {
            font-size: 30px;
            font-weight: 700;
            color: #9c0069;
        }

        .package-card {
            border-radius: 18px;
            transition: 0.3s;
        }

        .package-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }

        .lifestyle-img {
            width: 100%;
            border-radius: 18px;
        }

        .lifestyle-title {
            position: absolute;
            bottom: 15px;
            left: 50%;
            transform: translateX(-50%);
            background: #fff;
            padding: 8px 16px;
            border-radius: 25px;
            font-weight: 600;
        }

        .lifestyle-box {
            position: relative;
            border-radius: 18px;
            overflow: hidden;
        }

        .footer {
            background: #003049;
            color: white;
            padding: 20px 0;
            margin-top: 50px;
        }
        .feature-box {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border-radius: 10px;
}

.feature-box:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

/* For dark version */
.bg-primary .feature-box:hover {
    background: rgba(255,255,255,0.05);
}

/* For light version */
.bg-light .feature-box:hover {
    background: white;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}
.stat-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
}

.stat-card:hover {
    background: rgba(255, 255, 255, 0.15);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

</style>

</head>
<body>

<!-- HERO SECTION -->
<div class="container hero-section">
    <div class="row align-items-center">
        <div class="col-md-7">
            <h1 class="hero-title">Own a Diagnostics</h1>
            <h2 class="hero-subtitle">Where life meets Opportunity</h2>
            <a href="#" class="btn btn-danger btn-lg mt-4">Enquire Now</a>
        </div>
        <div class="col-md-5 text-center">
            <img class="hero-img" src="./assets/front_image.jpeg">
        </div>
    </div>
</div>
<div class="container mt-5">
    <div class="d-flex justify-content-between">
        <h2 class="fw-bold">Recommended Packages</h2>
        <a class="btn btn-outline-primary" href="./allpackages.php">View All</a>
    </div>

    <div class="row mt-4">

    <?php
    $qry = "SELECT id, package_name, original_price, discounted_price, image 
            FROM packages LIMIT 4";
    $stmt = $conn->prepare($qry);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {

        while ($row = $result->fetch_assoc()) {
            $off = 0;
            if ($row['original_price'] > 0) {
                $off = round((($row['original_price'] - $row['discounted_price']) 
                        / $row['original_price']) * 100);
            }
    ?>
       <div class="col-md-3 mb-4">
    <div class="card package-card p-3 h-100 d-flex flex-column">

        <img src="images/packageimages/<?php echo $row['image']; ?>" 
             class="card-img-top mb-2"
             style="height:180px; object-fit:cover; border-radius:10px;">

        <h5 class="fw-bold"><?php echo $row['package_name']; ?></h5>
        <p class="text-muted">Home Collection Available</p>

        <p class="fw-bold fs-4">
            ₹<?php echo $row['discounted_price']; ?>
            <span class="text-decoration-line-through fs-6">
                ₹<?php echo $row['original_price']; ?>
            </span>
            <span class="text-success"><?php echo $off; ?>% off</span>
        </p>
        <div class="mt-auto d-flex gap-2">
            <a href="viewpackage.php?id=<?php echo $row['id']; ?>" 
                class="btn btn-outline-primary w-50">View</a>
            <a href="addtocart.php?id=<?php echo $row['id']; ?>&type=package" 
                class="btn btn-primary w-50">Add</a>
        </div>

    </div>
</div>

    <?php 
        } 
    } else {
        echo "<p class='text-danger text-center'>No packages available.</p>";
    }
    ?>

    </div>
</div>
<div class="container mt-5">
    <div class="d-flex justify-content-between">
        <h2 class="fw-bold">Popular Tests</h2>
        <a class="btn btn-outline-primary" href="./alltests.php">View All</a>
    </div>
    <div class="row mt-4">
    <?php
    $qry = "SELECT id, test_name, original_price, discounted_price, image FROM tests LIMIT 4";
    $stmt = $conn->prepare($qry);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {

        while ($row = $result->fetch_assoc()) {
            $off = 0;
            if ($row['original_price'] > 0) {
                $off = round((($row['original_price'] - $row['discounted_price']) 
                        / $row['original_price']) * 100);
            }
    ?>
        <div class="col-md-3 mb-4">
            <div class="card package-card p-3">
                <img src="images/testimages/<?php echo $row['image']; ?>" 
                     class="card-img-top mb-2" 
                     style="height:180px; object-fit:cover; border-radius:10px;">
                <h5 class="fw-bold"><?php echo $row['test_name']; ?></h5>
                <p class="text-muted">Home Collection Available</p>
                <p class="fw-bold fs-4">
                    ₹<?php echo $row['discounted_price']; ?>
                    <span class="text-decoration-line-through fs-6">
                        ₹<?php echo $row['original_price']; ?>
                    </span>
                    <span class="text-success">
                        <?php echo $off; ?>% off
                    </span>
                </p>

                <div class="d-flex gap-2">
                    <a href="viewtest.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-primary w-50">View</a>
                    <a href="addtocart.php?id=<?php echo $row['id']; ?>&type=test" class="btn btn-primary w-50">Add</a>
                </div>

            </div>
        </div>
    <?php 
        } 
    } else {
        echo "<p class='text-danger text-center'>No tests available.</p>";
    }
    ?>

    </div>
</div>

<section class="py-5 bg-light">
    <div class="container">
         <h2 class="fw-bold mb-4">Your Health, Our Priority</h2>
        <div class="row g-4">
            <div class="col-md-4 text-center">
                <div class="card border-0 bg-white shadow-sm h-100">
                    <img src="assets/awareness_section/prevention.jpg
                    " class="card-img-top" alt="Preventive Healthcare" style="height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="fw-bold">Preventive Care</h5>
                        <p class="text-muted">Regular health checkups can detect issues early and prevent serious conditions.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="card border-0 bg-white shadow-sm h-100">
                    <img src="assets/awareness_section/tech.jpg
                    " class="card-img-top" alt="Advanced Technology" style="height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="fw-bold">Advanced Technology</h5>
                        <p class="text-muted">State-of-the-art equipment for accurate and reliable test results.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="card border-0 bg-white shadow-sm h-100">
                    <img src="assets/awareness_section/expert_team.jpg" class="card-img-top" alt="Expert Team" style="height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="fw-bold">Expert Team</h5>
                        <p class="text-muted">Certified pathologists and technicians ensuring quality diagnostics.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container mt-5">
    <h2 class="fw-bold text-center mb-3">About Us</h2>
    <p class="text-center fs-5 text-muted">
        QUICKLAB is committed to providing accurate, fast, and reliable diagnostic services 
        that empower individuals to take charge of their health. 
        With cutting-edge technology and expert professionals, we ensure quality care for everyone.
    </p>
</div>

<section class="py-5">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h2 class="fw-bold text-primary mb-3">How QuickLab Works</h2>
                <p class="text-muted">Simple, fast, and reliable testing process</p>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="d-flex align-items-start">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold fs-4 me-3" style="width: 50px; height: 50px; min-width: 50px;">
                        1
                    </div>
                    <div>
                        <h5 class="fw-bold mb-2">Book Test</h5>
                        <p class="text-muted mb-0">Select from 1000+ tests and health packages online</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="d-flex align-items-start">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold fs-4 me-3" style="width: 50px; height: 50px; min-width: 50px;">
                        2
                    </div>
                    <div>
                        <h5 class="fw-bold mb-2">Home Collection</h5>
                        <p class="text-muted mb-0">Free sample collection at your preferred time and location</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="d-flex align-items-start">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold fs-4 me-3" style="width: 50px; height: 50px; min-width: 50px;">
                        3
                    </div>
                    <div>
                        <h5 class="fw-bold mb-2">Sample Processing</h5>
                        <p class="text-muted mb-0">Tests processed in certified labs with quality checks</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="d-flex align-items-start">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold fs-4 me-3" style="width: 50px; height: 50px; min-width: 50px;">
                        4
                    </div>
                    <div>
                        <h5 class="fw-bold mb-2">Get Reports</h5>
                        <p class="text-muted mb-0">Digital reports delivered via email within 24 hours</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>



<!-- Statistics Cards Section -->
<section class="py-3 bg-primary text-white">
    <div class="container">
        <h3 class="text-center fw-bold mb-4">Why Choose QuickLab?</h3>
        <div class="row justify-content-center">
            <div class="col-6 col-md-3 mb-3">
                <div class="stat-card text-center p-3 rounded-3 h-100">
                    <div class="h3 fw-bold mb-1">70%</div>
                    <small class="opacity-90">Early Disease Prevention</small>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-3">
                <div class="stat-card text-center p-3 rounded-3 h-100">
                    <div class="h3 fw-bold mb-1">24/7</div>
                    <small class="opacity-90">Digital Access</small>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-3">
                <div class="stat-card text-center p-3 rounded-3 h-100">
                    <div class="h3 fw-bold mb-1">94%</div>
                    <small class="opacity-90">Accuracy Rate</small>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-3">
                <div class="stat-card text-center p-3 rounded-3 h-100">
                    <div class="h3 fw-bold mb-1">50K+</div>
                    <small class="opacity-90">Happy Customers</small>
                </div>
            </div>
        </div>
    </div>
</section>



<?php include_once "footer.php";?>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
