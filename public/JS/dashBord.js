// ===============================
// Dashboard JS (Clean & simple)
// ===============================

// Patient Visit (Line)

// ===== Sidebar Active Link =====
const currentPage =
  location.pathname.split("/").pop() || "dashBoard.html";

document.querySelectorAll(".side-link").forEach(link => {
  if (link.getAttribute("href") === currentPage) {
    link.classList.add("active");
  }
});





const visitEl = document.getElementById("visitChart");
if (visitEl) {
  new Chart(visitEl, {
    type: "line",
    data: {
      labels: ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],
      datasets: [{
        label: "Visits",
        data: [70,50,80,35,55,40,120,160,50,95,175,60],
        borderWidth: 3,
        tension: 0.45,
        pointRadius: 0
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        x: { grid: { display: false } },
        y: { grid: { color: "#eef2f8" } }
      }
    }
  });
}





// Patient Satisfaction (Doughnut)
const satEl = document.getElementById("satisfactionChart");
if (satEl) {
  new Chart(satEl, {
    type: "doughnut",
    data: {
      labels: ["Excellent", "Good", "Poor"],
      datasets: [{
        data: [50, 30, 20],
        borderWidth: 6,
        hoverOffset: 6,
        backgroundColor: [
          "#3371EB",  // Excellent
          "#14CC26",  // Good
          "#FFFF00"   // Poor
        ]
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: "68%",
      plugins: { legend: { display: false } }
    }
  });
}




