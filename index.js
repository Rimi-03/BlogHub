const input = document.getElementById("searchInput");
const suggestions = document.getElementById("suggestions");
const searchForm = input.form; // Dynamically grab the parent form element

// --- 1. PREVENT BLANK OR UNMATCHED REDIRECTS ON ENTER ---
searchForm.addEventListener("submit", function (e) {
  let query = input.value.trim();

  // Condition A: Prevent search if the input field is empty
  if (query.length < 2) {
    e.preventDefault();
    return false;
  }

  // Condition B: Prevent redirect if the "No matching blogs found" alert is currently visible
  if (!suggestions.classList.contains("hidden")) {
    const hasItems =
      suggestions.querySelectorAll(".suggestion-item").length > 0;

    if (!hasItems) {
      e.preventDefault(); // Blocks the page from redirecting/reloading
      return false;
    }
  }
});

// --- 2. LIVE SEARCH SUGGESTIONS STREAM ENGINE ---
input.addEventListener("keyup", function (e) {
  let query = this.value.trim();

  // Clean the slate if input drops below 2 characters
  if (query.length < 2) {
    suggestions.classList.add("hidden");
    suggestions.innerHTML = "";
    return;
  }

  // Skip AJAX fetch if user is pressing the Enter key (handled separately above)
  if (e.key === "Enter") return;

  fetch("search_suggestions.php?q=" + encodeURIComponent(query))
    .then((response) => response.text())
    .then((data) => {
      suggestions.innerHTML = data;

      if (data.trim() !== "") {
        suggestions.classList.remove("hidden");
      } else {
        suggestions.classList.add("hidden");
        return;
      }

      setTimeout(() => {
        const items = document.querySelectorAll(".suggestion-item");

        // Highlight logic applies ONLY if actual data attributes exist
        if (items.length > 0) {
          items.forEach((item) => {
            const originalText = item.innerText;
            const escapedQuery = query.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
            const regex = new RegExp(`(${escapedQuery})`, "gi");

            item.innerHTML = originalText.replace(
              regex,
              '<span class="text-blue-600 font-bold">$1</span>',
            );

            item.addEventListener("click", function () {
              input.value = originalText;
              suggestions.classList.add("hidden");
              searchForm.submit(); // Safe to redirect here because a valid option was selected
            });
          });
        }
      }, 0);
    });
});

// Close suggestions dropdown on outside click
document.addEventListener("click", function (e) {
  if (!input.contains(e.target) && !suggestions.contains(e.target)) {
    suggestions.classList.add("hidden");
  }
});
