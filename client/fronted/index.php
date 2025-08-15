<?
require_once '../backend/db.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Fly High</title>

  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="font-mono">

  <?php include 'components/navbar.php'; ?>

  <!-- Hero Section -->
  <div class="min-h-screen bg-gradient-to-b from-yellow-400 via-orange-300 to-sky-400 relative overflow-hidden pt-28">
    <div class="flex flex-col items-center text-center px-4 relative">

      <h1 class="text-6xl md:text-7xl font-bold">
        FLY IN STYLE
      </h1>
      <h2 class="text-6xl md:text-7xl font-bold mb-6">
        ARRIVE IN COMFORT
      </h2>

      <div class="w-full flex justify-end pr-10 mt-32">
        <div class="text-right max-w-xs">
          <?php if (isset($_SESSION['user'])): ?>
            <p class="text-sm font-semibold mb-4 ">Discover Exclusive Deals On Premium And First-Class Flights For Your Ultimate Travel Experience.
            </p>
            <button class="bg-blue-500 text-white px-6 py-2 rounded-full font-semibold hover:bg-blue-600 transition">
              Continue Your Journey
            </button>
          <?php else: ?>
            <p class="text-sm font-semibold mb-4">
              Discover Exclusive Deals On Premium And First-Class Flights For
              Your Ultimate Travel Experience.
            </p>
            <button class="bg-blue-500 text-white px-6 py-2 rounded-full font-semibold hover:bg-blue-600 transition">
              Start Your Journey
            </button>
          <?php endif; ?>
        </div>
      </div>

      <img src="assets/plane.png" alt="Airplane"
        class="absolute top-[80%] left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[60rem] z-10" />

    </div>
  </div>

  <!-- Search Section -->
  <section class=" min-h-screen flex items-center justify-center px-4 md:px-0"
    style="background: linear-gradient(180deg,#38BDF8 0%,#b08f4a 55%,#f9c15c 100%);">
    <div class="max-w-3xl mx-auto bg-[#d9d9d9] rounded-md py-8 px-10 relative"
      style="font-family: Georgia, serif; width: 100%;">
      <!-- Mode toggle buttons -->
      <div class="flex justify-between mb-6">
        <button type="button" id="oneWayBtn"
          class="px-8 py-2 rounded-full text-white text-sm font-medium tracking-wide bg-[#e97778] transition-all duration-300 hover:bg-[#d86a6b] focus:outline-none focus:ring-2 focus:ring-[#e97778] focus:ring-opacity-50">
          One Way
        </button>
        <button type="button" id="twoWayBtn"
          class="px-8 py-2 rounded-full text-white text-sm font-medium tracking-wide bg-[rgba(240,176,174,0.3)] transition-all duration-300 hover:bg-[rgba(240,176,174,0.5)] focus:outline-none focus:ring-2 focus:ring-[rgba(240,176,174,0.5)] focus:ring-opacity-50">
          Two Way
        </button>
      </div>

      <form class="space-y-6" action="pages/result.html" method="GET" id="flightSearchForm">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

          <div>
            <label class="block text-xs mb-1">From :</label>
            <input type="text" name="from" required placeholder="Origin"
              class="w-full bg-transparent border-0 border-b-2 border-black focus:outline-none text-lg pb-1 px-2" />
          </div>

          <div>
            <label class="block text-xs mb-1">To :</label>
            <input type="text" name="to" required placeholder="Destination"
              class="w-full bg-transparent border-0 border-b-2 border-black focus:outline-none text-lg pb-1 px-2" />
          </div>

          <div>
            <label class="block text-xs mb-1">Depart</label>
            <input type="date" name="depart" required
              class="w-full bg-transparent border-0 border-b-2 border-black focus:outline-none text-lg pb-1" />
          </div>

          <div>
            <label class="block text-xs mb-1">Return</label>
            <input type="date" name="return"
              class="w-full bg-transparent border-0 border-b-2 border-black focus:outline-none text-lg pb-1" />
          </div>

          <div class="md:col-span-2">
            <label class="block text-xs mb-1">Passenger</label>
            <input type="number" name="passengers" min="1" value="1"
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
    // Toggle functionality for One Way / Two Way buttons
    const oneWayBtn = document.getElementById('oneWayBtn');
    const twoWayBtn = document.getElementById('twoWayBtn');
    const returnField = document.querySelector('input[name="return"]');
    const returnLabel = returnField.previousElementSibling;
    const flightForm = document.getElementById('flightSearchForm');

    // Initially hide return field for one-way trips
    returnField.style.display = 'none';
    returnLabel.style.display = 'none';

    oneWayBtn.addEventListener('click', function () {
      // Activate One Way
      oneWayBtn.style.backgroundColor = '#e97778';
      twoWayBtn.style.backgroundColor = 'rgba(240,176,174,0.3)';

      // Hide return field
      returnField.style.display = 'none';
      returnLabel.style.display = 'none';

      // Remove required attribute from return field
      returnField.removeAttribute('required');
    });

    twoWayBtn.addEventListener('click', function () {
      // Activate Two Way
      twoWayBtn.style.backgroundColor = '#e97778';
      oneWayBtn.style.backgroundColor = 'rgba(240,176,174,0.3)';

      // Show return field
      returnField.style.display = 'block';
      returnLabel.style.display = 'block';

      // Add required attribute to return field
      returnField.setAttribute('required', 'required');
    });

    // Handle form submission
    flightForm.addEventListener('submit', function (e) {
      e.preventDefault(); // Prevent default form submission

      // Get form data
      const formData = new FormData(flightForm);
      const searchParams = new URLSearchParams();

      // Add form data to URL parameters
      for (let [key, value] of formData.entries()) {
        if (value) { // Only add non-empty values
          searchParams.append(key, value);
        }
      }

      // Redirect to result page with search parameters
      window.location.href = `pages/result.php?${searchParams.toString()}`;
    });
  </script>

</body>

</html>