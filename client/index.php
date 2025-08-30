<?php
session_start();
require_once '../db.php';

// Fetch unique origins and destinations from DB for hints
$origins = [];
$destinations = [];

$sql = "SELECT DISTINCT origin, destination FROM flights";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if (!in_array($row['origin'], $origins)) {
            $origins[] = $row['origin'];
        }
        if (!in_array($row['destination'], $destinations)) {
            $destinations[] = $row['destination'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Fly High</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        html {
            scroll-behavior: smooth;
        }

        .fixed-hero {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            z-index: -1;
        }

        .parallax-plane {
            position: absolute;
            top: 45%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 60rem;
            z-index: 20;
            will-change: transform;
        }

        .vertical-line {
            position: absolute;
            left: 50.4%;
            transform: translateX(-50%, -50%);
            top: 45rem;
            width: 2px;
            height: 295px;
            background-color: #4a5568;
            z-index: 15;
        }

        #search-section {
            position: absolute;
            top: 100vh;
            width: 100%;
            z-index: 10;
        }
    </style>
</head>

<body class="font-mono">
    <?php include 'components/navbar.php'; ?>

    <div class="fixed-hero bg-gradient-to-b from-yellow-400 via-orange-300 to-sky-400 overflow-hidden pt-28">
        <div class="flex flex-col items-center text-center px-4 relative">
            <h1 class="text-6xl md:text-7xl font-bold">FLY IN STYLE</h1>
            <h2 class="text-6xl md:text-7xl font-bold mb-6">ARRIVE IN COMFORT</h2>
            <div class="w-full flex justify-end pr-10 mt-32">
                <div class="text-right max-w-xs">
                    <p class="text-sm font-semibold mb-4">Discover Exclusive Deals On Premium And First-Class Flights
                        For Your Ultimate Travel Experience.
                    </p>
                    <button
                        class="bg-blue-500 text-white px-6 py-2 rounded-full font-semibold hover:bg-blue-600 transition">
                        Start Your Journey
                    </button>

                </div>
            </div>
        </div>
    </div>

    <img src="assets/plane.png" alt="Airplane" class="parallax-plane" />

    <div class="vertical-line"></div>

    <div class="h-screen"></div>

    <section id="search-section" class="min-h-screen flex items-center justify-center px-4 md:px-0"
        style="background: linear-gradient(180deg, rgba(57,189,248,0.05) 0%, #38BDF8 10%, #b08f4a 55%, #f9c15c 100%);">
        <div class="max-w-3xl mx-auto bg-[#d9d9d9] rounded-md py-8 px-10 relative"
            style="font-family: Georgia, serif; width: 100%;">
            <div class="flex justify-center mb-6">
                <span class="px-8 py-2 rounded-full text-blue text-xl font-semibold ">
                    Book Your Flight
                </span>
            </div>

            <form class="space-y-6" action="pages/result.php" method="GET" id="flightSearchForm">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-xs mb-1">From :</label>
                        <input list="origins" type="text" name="from" required placeholder="Origin"
                            class="w-full bg-transparent border-0 border-b-2 border-black focus:outline-none text-lg pb-1 px-2" />
                        <datalist id="origins">
                            <?php foreach ($origins as $o): ?>
                                <option value="<?= htmlspecialchars($o) ?>"></option>
                            <?php endforeach; ?>
                        </datalist>
                    </div>
                    <div>
                        <label class="block text-xs mb-1">To :</label>
                        <input list="destinations" type="text" name="to" required placeholder="Destination"
                            class="w-full bg-transparent border-0 border-b-2 border-black focus:outline-none text-lg pb-1 px-2" />
                        <datalist id="destinations">
                            <?php foreach ($destinations as $d): ?>
                                <option value="<?= htmlspecialchars($d) ?>"></option>
                            <?php endforeach; ?>
                        </datalist>
                    </div>
                    <div>
                        <label class="block text-xs mb-1">Depart</label>
                        <input type="date" name="depart" required
                            class="w-full bg-transparent border-0 border-b-2 border-black focus:outline-none text-lg pb-1" />
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs mb-1">Passenger</label>
                        <input type="number" name="passengers" min="1" value="1" required
                            class="w-full bg-transparent border-0 border-b-2 border-black focus:outline-none text-lg pb-1" />
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit"
                        class="bg-[#e97778] text-white px-10 py-3 rounded-lg text-xl tracking-wider font-semibold w-full md:w-auto">
                        FIND FLIGHTS
                    </button>
                </div>
            </form>
        </div>
    </section>

    <script>
        const departField = document.querySelector('input[name="depart"]');
        const flightForm = document.getElementById('flightSearchForm');
        const plane = document.querySelector('.parallax-plane');
        const verticalLine = document.querySelector('.vertical-line');
        const searchSection = document.getElementById('search-section');

        const initialSearchSectionTop = searchSection.offsetTop;
        const initialVerticalLineTop = verticalLine.offsetTop;

        // Set min date
        const today = new Date().toISOString().split("T")[0];
        departField.setAttribute("min", today);

        // Validate & redirect
        flightForm.addEventListener('submit', function (e) {
            const departDate = new Date(departField.value);
            if (!departField.value) {
                alert("Please select a departure date.");
                e.preventDefault();
                return;
            }
            if (departDate < new Date(today)) {
                alert("Departure date cannot be in the past.");
                e.preventDefault();
                return;
            }
        });

        // Parallax effect
        window.addEventListener('scroll', function () {
            const scrollPosition = window.scrollY;
            const newY = -scrollPosition * 0.5;

            plane.style.transform = `translate(-50%, -50%) translateY(${newY}px)`;
            verticalLine.style.top = `${initialVerticalLineTop + newY}px`;
            searchSection.style.top = `${initialSearchSectionTop + newY}px`;
        });
    </script>
</body>

</html>