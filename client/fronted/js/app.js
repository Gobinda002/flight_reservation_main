const apiBase = "backend/api";

function setYear() {
  const yearEl = document.getElementById("year");
  if (yearEl) yearEl.textContent = String(new Date().getFullYear());
}

async function fetchAirports() {
  const res = await fetch(`${apiBase}/airports.php`);
  if (!res.ok) throw new Error("Failed to load airports");
  return res.json();
}

function fillAirportSelects(airports) {
  const originSel = document.getElementById("origin");
  const destSel = document.getElementById("destination");
  if (!originSel || !destSel) return;
  for (const ap of airports) {
    const o = document.createElement("option");
    o.value = ap.code;
    o.textContent = `${ap.city} (${ap.code})`;
    originSel.appendChild(o.cloneNode(true));
    destSel.appendChild(o);
  }
}

function renderResults(flights) {
  const container = document.getElementById("results");
  if (!container) return;
  container.innerHTML = "";
  if (!flights.length) {
    container.innerHTML = '<p>No flights found.</p>';
    return;
  }
  for (const f of flights) {
    const card = document.createElement("div");
    card.className = "result-card";
    card.innerHTML = `
      <div>
        <div><strong>${f.airline}</strong> • ${f.flight_number}</div>
        <div class="result-meta">${f.origin} → ${f.destination} • ${new Date(f.departure_time).toLocaleString()} • ${f.cabin_class}</div>
      </div>
      <div>
        <div style="text-align:right"><strong>$${Number(f.price).toFixed(2)}</strong></div>
        <button class="btn btn-primary" data-flight-id="${f.id}">Book</button>
      </div>
    `;
    container.appendChild(card);
  }

  container.addEventListener("click", async (e) => {
    const target = e.target;
    if (target && target.matches("button[data-flight-id]")) {
      const flightId = target.getAttribute("data-flight-id");
      await bookFlight(flightId);
    }
  });
}

async function searchFlights(params) {
  const url = new URL(`${apiBase}/flights.php`, window.location.href);
  Object.entries(params).forEach(([k, v]) => {
    if (v) url.searchParams.set(k, v);
  });
  const res = await fetch(url);
  if (!res.ok) throw new Error("Failed to search flights");
  return res.json();
}

async function bookFlight(flightId) {
  try {
    const res = await fetch(`${apiBase}/book.php`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ flight_id: flightId, passenger_name: "Demo User", email: "demo@example.com" })
    });
    const data = await res.json();
    if (!res.ok) throw new Error(data.message || "Booking failed");
    alert(`Booking confirmed. Reference: ${data.reference}`);
  } catch (err) {
    alert(err.message);
  }
}

function bindSearchForm() {
  const form = document.getElementById("search-form");
  if (!form || form.classList.contains('alt')) return;
  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    const origin = document.getElementById("origin").value;
    const destination = document.getElementById("destination").value;
    const date = document.getElementById("date").value;
    try {
      const flights = await searchFlights({ origin, destination, date });
      renderResults(flights);
    } catch (err) {
      alert(err.message);
    }
  });
}

async function init() {
  setYear();
  try {
    const airports = await fetchAirports();
    fillAirportSelects(airports);
  } catch (err) {
    console.error(err);
  }
  bindSearchForm();
  bindAltSearch();
}

document.addEventListener("DOMContentLoaded", init);

// navbar behavior
document.addEventListener("DOMContentLoaded", () => {
  const homeBrand = document.getElementById("nav-home");
  const homeLink = document.getElementById("nav-home-link");
  const login = document.getElementById("nav-login");

  function goHome() {
    if (location.pathname.endsWith("index.html") || location.pathname.endsWith("/") || location.pathname === "") {
      window.scrollTo({ top: 0, behavior: "smooth" });
    } else {
      window.location.href = "index.html";
    }
  }

  [homeBrand, homeLink].forEach((el) => el && el.addEventListener("click", goHome));
  if (login) {
    login.addEventListener("click", () => {
      window.location.href = "login.html";
    });
  }
});

// Alt search UI behavior
function bindAltSearch() {
  const one = document.getElementById('mode-oneway');
  const two = document.getElementById('mode-roundtrip');
  const returnGroup = document.getElementById('return-group');
  const form = document.getElementById('search-form');
  const from = document.getElementById('from');
  const to = document.getElementById('to');
  const depart = document.getElementById('depart');
  const ret = document.getElementById('return');
  const passengers = document.getElementById('passengers');

  if (!one || !two || !returnGroup || !form || !from || !to || !depart || !passengers) return;

  let mode = 'oneway';
  function setMode(m) {
    mode = m;
    if (mode === 'roundtrip') {
      returnGroup.classList.remove('hidden');
      one.classList.remove('active');
      two.classList.add('active');
    } else {
      returnGroup.classList.add('hidden');
      two.classList.remove('active');
      one.classList.add('active');
    }
  }
  setMode('oneway');
  one.addEventListener('click', () => setMode('oneway'));
  two.addEventListener('click', () => setMode('roundtrip'));

  form.addEventListener('submit', async (e) => {
    if (!form.classList.contains('alt')) return; // only handle alt form here
    e.preventDefault();
    // For backend search, we only support one-way date currently
    const params = { origin: from.value.trim(), destination: to.value.trim(), date: depart.value };
    try {
      const flights = await searchFlights(params);
      renderResults(flights);
      // Optionally, we could navigate or store state; keeping inline for now
    } catch (err) {
      alert(err.message);
    }
  });
}


