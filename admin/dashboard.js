document.addEventListener("DOMContentLoaded", function () {
  attachToastTimers();
});

function attachToastTimers() {
  const toasts = document.querySelectorAll(".toast-alert");
  toasts.forEach((toast) => {
    if (!toast.dataset.hasTimer) {
      toast.dataset.hasTimer = "true";
      setTimeout(() => {
        toast.classList.add("translate-y-2", "opacity-0");
        setTimeout(() => toast.remove(), 300);
      }, 4000);
    }
  });
}

function spawnFloatingToast(text, type = "success") {
  const container = document.getElementById("toastContainer");
  const toast = document.createElement("div");
  toast.className = `toast-alert pointer-events-auto ${type === "success" ? "bg-green-600" : "bg-red-600"} text-white p-4 rounded-xl shadow-xl flex items-center justify-between font-medium text-sm transition-all duration-300`;
  toast.innerHTML = `<span>${type === "success" ? "🎉" : "⚠️"} ${text}</span>
                               <button onclick="this.parentElement.remove()" class="text-white hover:text-gray-200 font-bold ml-3 text-lg">&times;</button>`;
  container.appendChild(toast);
  attachToastTimers();
}

function toggleCreatePostForm() {
  const section = document.getElementById("createPostSection");
  const postFeed = document.getElementById("recentPostsSection");

  if (section.classList.contains("hidden")) {
    section.classList.remove("hidden");
    postFeed.classList.add("hidden");
  } else {
    section.classList.add("hidden");
    postFeed.classList.remove("hidden");
    if (window.location.search.includes("edit_id")) {
      window.location.href = "dashboard.php";
    }
  }
}

// --- NEW: READ MODAL FUNCTIONALITY ---
function openBlogReadModal(blogId) {
  const modal = document.getElementById("blogReadModal");
  const card = document.getElementById("blogReadCard");
  const contentBox = document.getElementById("blogReadContent");

  contentBox.innerHTML = `<div class="text-center py-8 text-gray-400 text-sm animate-pulse">Loading story content data safely...</div>`;

  modal.classList.remove("hidden");
  modal.classList.add("flex");
  setTimeout(() => card.classList.remove("scale-95"), 10);

  const formData = new FormData();
  formData.append("action", "ajax_get_blog_body");
  formData.append("blog_id", blogId);

  fetch("dashboard.php", {
    method: "POST",
    body: formData,
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        contentBox.innerHTML = `
                        <div class="space-y-2">
                            <h1 class="text-xl sm:text-2xl font-black text-gray-900 leading-tight">${escapeHtml(data.blog.title)}</h1>
                            ${data.blog.subtitle ? `<p class="text-sm text-gray-500 font-medium italic">${escapeHtml(data.blog.subtitle)}</p>` : ""}
                            <div class="flex items-center gap-2 text-xs text-gray-400 pt-1">
                                <span>By <strong class="text-blue-600 font-semibold">${escapeHtml(data.blog.username)}</strong></span>
                                <span>•</span>
                                <span>${data.blog.publish_date_formatted}</span>
                            </div>
                        </div>
                        <img src="${escapeHtml(data.blog.cover_image)}" class="w-full h-48 sm:h-64 object-cover rounded-xl border bg-gray-50 shadow-sm">
                        <div class="bg-gray-50 p-3.5 rounded-xl border border-gray-200/60 text-xs font-medium text-gray-600 leading-relaxed">
                            <strong class="text-gray-800 text-[11px] uppercase tracking-wider block mb-1">Short Summary:</strong>
                            ${escapeHtml(data.blog.description)}
                        </div>
                        <div class="text-sm text-gray-800 font-serif leading-relaxed whitespace-pre-wrap pt-2 border-t border-dashed">${escapeHtml(data.blog.content)}</div>
                    `;
      } else {
        contentBox.innerHTML = `<div class="text-center py-8 text-red-500 font-medium">${escapeHtml(data.error)}</div>`;
      }
    })
    .catch(() => {
      contentBox.innerHTML = `<div class="text-center py-8 text-red-500 font-medium">Failed to process connection endpoint mapping data.</div>`;
    });
}

function closeBlogReadModal() {
  const modal = document.getElementById("blogReadModal");
  const card = document.getElementById("blogReadCard");
  card.classList.add("scale-95");
  setTimeout(() => {
    modal.classList.remove("flex");
    modal.classList.add("hidden");
  }, 150);
}

function escapeHtml(string) {
  if (!string) return "";
  return String(string)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;");
}

function toggleAuthorManagementModal() {
  const modal = document.getElementById("authorManagementModal");
  const card = document.getElementById("authorModalCard");
  if (modal.classList.contains("hidden")) {
    modal.classList.remove("hidden");
    modal.classList.add("flex");
    setTimeout(() => card.classList.remove("scale-95"), 10);
  } else {
    card.classList.add("scale-95");
    setTimeout(() => {
      modal.classList.remove("flex");
      modal.classList.add("hidden");
    }, 150);
  }
}

function openAuthorModalFromNavbar() {
  document.getElementById("modalOriginSource").value = "navbar_button";
  document.getElementById("modalAuthorNameInput").value = "";
  toggleAuthorManagementModal();
}

function checkInlineAuthorSelection(selectElement) {
  if (selectElement.value === "NEW_MODAL_TRIGGER") {
    selectElement.value = "";
    document.getElementById("modalOriginSource").value = "inline_form";
    document.getElementById("modalAuthorNameInput").value = "";
    toggleAuthorManagementModal();
  }
}

function submitAuthorFormAsync(event) {
  event.preventDefault();
  const authorName = document
    .getElementById("modalAuthorNameInput")
    .value.trim();
  const originSource = document.getElementById("modalOriginSource").value;

  if (!authorName) return;

  const formData = new FormData();
  formData.append("action", "ajax_create_author");
  formData.append("username", authorName);

  fetch("dashboard.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        spawnFloatingToast(data.message, "success");

        const selectField = document.getElementById("authorSelectField");
        const newOption = document.createElement("option");
        newOption.value = data.author_id;
        newOption.textContent = data.username;
        selectField.appendChild(newOption);

        if (originSource === "inline_form") {
          selectField.value = data.author_id;
        }

        const listContainer = document.getElementById("modalAuthorsListView");
        const authorRow = document.createElement("div");
        authorRow.className =
          "flex items-center justify-between py-2.5 px-3 hover:bg-gray-50 rounded-lg group transition";
        authorRow.id = `author_row_${data.author_id}`;
        authorRow.innerHTML = `
                        <a href="dashboard.php?author_view_id=${data.author_id}" class="text-xs font-semibold text-gray-700 hover:text-blue-600 transition truncate pr-2">
                            ${escapeHtml(data.username)}
                        </a>
                        <form method="POST" action="dashboard.php" onsubmit="return confirm('Remove author profile completely?');">
                            <input type="hidden" name="action" value="delete_author">
                            <input type="hidden" name="author_id" value="${data.author_id}">
                            <button type="submit" class="text-[10px] text-red-500 hover:text-red-700 font-medium opacity-60 group-hover:opacity-100 transition shrink-0">Remove</button>
                        </form>
                    `;
        listContainer.insertBefore(authorRow, listContainer.firstChild);

        toggleAuthorManagementModal();
      } else {
        spawnFloatingToast(data.error, "error");
      }
    })
    .catch((err) => {
      console.error(err);
      spawnFloatingToast(
        "An error occurred during async profile sync routing.",
        "error",
      );
    });
}

// Close templates if backing layers are explicitly triggered directly
document
  .getElementById("authorManagementModal")
  .addEventListener("click", function (e) {
    if (e.target === this) toggleAuthorManagementModal();
  });
document
  .getElementById("blogReadModal")
  .addEventListener("click", function (e) {
    if (e.target === this) closeBlogReadModal();
  });
