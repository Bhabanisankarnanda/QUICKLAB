<?php
require_once "navbar.php";
include_once "dbcon.php";

$qry="SELECT * FROM tests";
$stmt = $conn->prepare("$qry");
$stmt->execute();
$result = $stmt->get_result();
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Tests - QuickLab</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .test-card {
            padding: 22px;
            border-radius: 18px;
            transition: 0.3s;
            min-height: 330px;
        }
        .test-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        .test-img {
            width: 100%;
            height: 140px;
            object-fit: contain;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h2 class="fw-bold text-center mb-4">All Popular Tests</h2>

    <div class="row">
        <?php while($row = $result->fetch_assoc()) { ?>
            <div class="col-md-3 mb-4">
                <div class="card test-card p-3">
                    
                    <img src="images/testimages/<?php echo $row['image']; ?>" class="test-img">

                    <h5 class="fw-bold"><?php echo $row['test_name']; ?></h5>
                    <p class="text-muted"><?php echo $row['description']; ?></p>

                    <p class="fw-bold fs-4">
                        ₹<?php echo $row['discounted_price']; ?>
                        <span class="text-decoration-line-through fs-6">
                            <?php echo $row['original_price']; ?>
                        </span>
                        <span class="text-success">
                            <?php 
                                echo round((($row['original_price'] - $row['discounted_price']) / $row['original_price']) * 100);
                            ?>% off
                        </span>
                    </p>

                    <div class="d-flex gap-2">
    <a href="viewtest.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-primary w-50">View</a>
    <a href="addtocart.php?id=<?php echo $row['id']; ?>&type=test" class="btn btn-primary w-50">Add to Cart</a>
</div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
<?php include_once "footer.php";?>
</body>
</html>
