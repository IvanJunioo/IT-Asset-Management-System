const BREAKPOINT = 1100;
const filterBox = document.getElementById("filter-box-reg");
const leftUser = document.querySelector(".left-page");
const rightUser = document.querySelector(".right-page");
const tableFunc = leftUser.querySelector(".table-func");
const generateReportBtn = rightUser.querySelector("#report");

let currentPosition = "right"; // Default position on page load

function repositionFilter() {
  const shouldBeOnLeft = window.innerWidth <= BREAKPOINT;
  const targetPosition = shouldBeOnLeft ? "left" : "right";

  // Only move if position actually changed
  if (currentPosition === targetPosition) return;

  if (shouldBeOnLeft) {
    // Move to left => insert before table functions
    leftUser.insertBefore(filterBox, tableFunc);
  } else {
    // Move to right => insert as first child
    rightUser.prepend(filterBox)
  }

  currentPosition = targetPosition;
}

// Initial positioning on page load
repositionFilter();

// Reposition on window resize
window.addEventListener("resize", repositionFilter);

// Also handle orientation change for mobile devices
window.addEventListener("orientationchange", () => {
  // Small delay to ensure dimensions are updated
  setTimeout(repositionFilter, 100);
});
