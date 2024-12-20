<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Page</title>

    <!-- Required Links -->
    <?php require('inc/link.php'); ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="CSS/style.css">
</head>

<body class="bg-light">
    <!-- Header -->
    <?php require('inc/header.php'); ?>

    <div class="my-5 px-4">
        <div class="d-flex justify-content-between align-items-center">
            <!-- Heading -->
            <h2 class="fw-bold h-font mb-0"> ROOMS</h2>

            <!-- Search Input and Button -->
            <div class="input-group" style="max-width: 300px;">
                <input type="text" class="form-control" id="search_input" placeholder="Search rooms...">
                <button class="btn btn-dark" onclick="searchRooms()">Search</button>
            </div>
        </div>
    </div>

    <!-- Room Cards Container -->
    <div class="container">
        <div class="row g-4" id="rooms_data">
            <!-- Room cards will load dynamically here -->
        </div>
    </div>

    <!-- Footer -->
    <?php require('inc/footer.php'); ?>

    <script>
        // Fetch and display rooms
        function fetch_rooms(query = "") {
            let xhr = new XMLHttpRequest();
            xhr.open("GET", `ajax/rooms.php?fetch_rooms&query=${encodeURIComponent(query)}`, true);

            xhr.onload = function () {
                document.getElementById('rooms_data').innerHTML = this.responseText;
            };

            xhr.send();
        }

        
        // Search rooms by name, features, or facilities
        function searchRooms() {
            const query = document.getElementById('search_input').value.trim();
            fetch_rooms(query);
        }

        // Initialize
        const user_id = <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '0'; ?>;
        fetch_rooms(); // Display all rooms initially
        if (user_id > 0) {
            fetch_recommended_rooms(user_id); // Fetch recommended rooms for logged-in users
        }
    </script>
</body>

</html>
