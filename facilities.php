<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facilities page</title>

    <?php require('inc/link.php'); ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href=" CSS/style.css">

    <style>
        .h-line {
            width: 150px;
            margin: 0 auto;
            height: 1.7px;
        }

        .pop:hover {
            border-top-color: var(--teal) !important;
            transform: scale(1.03);
            transition: all 0.3s;
        }
    </style>
</head>

<body class="bg-Light">
    <!-- ------------------header----------- -->
    <?php
    require('inc/header.php');
    ?>

    <div class="my-5 px-4">
        <h2 class="fw-bold text-center h-font">OUR FACILITIES</h2>

        <div class="h-line bg-dark mb-3"></div>
       

        <div class="container">
            <div class="row">

                <?php
                $res = selectAll('facility');
                $path = FACILITY_IMG_PATH;
                while ($row = mysqli_fetch_assoc($res)) {
                    echo <<<data
                            <div class="col-lg-4 col-md-6 mb-5 px-4">
                            <div class="bg-white rounded shadow p-4 border-top border-4 border-dark pop">
                                <div class="d-flex align-items-center mb-2">
                                <img src="$path$row[icon]" width="40px" alt="WiFi Icon">
                                <h5 class="m-0 ms-3">$row[name]</h5>
                                </div>
                                
                                <p>
                                    $row[description];
                                </p>
                            </div>
                        </div>
                      data;
                }
                ?>

             
            </div>
        </div>



        <!-- ------Footer------- -->
        <?php require('inc/footer.php');
        ?>



</body>

</html>