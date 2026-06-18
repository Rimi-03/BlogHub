document.addEventListener("DOMContentLoaded", function () {
  attachToastTimers();

  initModalEventListeners();

  initFormValidation();
});

// Carousel State Controllers

let activeCarouselImages = [];

let activeCarouselIndex = 0;

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

  if (!container) return;

  const toast = document.createElement("div");

  toast.className = `toast-alert pointer-events-auto ${type === "success" ? "bg-green-600" : "bg-red-600"} text-white p-4 rounded-xl shadow-xl flex items-center justify-between font-medium text-sm transition-all duration-300`;

  toast.innerHTML = `<span>${type === "success" ? "🎉" : "⚠️"} ${escapeHtml(text)}</span>

                       <button onclick="this.parentElement.remove()" class="text-white hover:text-gray-200 font-bold ml-3 text-lg">&times;</button>`;

  container.appendChild(toast);

  attachToastTimers();
}

function toggleCreatePostForm() {
  const section = document.getElementById("createPostSection");

  const postFeed = document.getElementById("recentPostsSection");

  if (!section || !postFeed) return;

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

function openBlogReadModal(blogId) {
  const modal = document.getElementById("blogReadModal");

  const card = document.getElementById("blogReadCard");

  const contentBox = document.getElementById("blogReadContent");

  if (!modal || !contentBox) return;

  contentBox.innerHTML = `<div class="text-center py-8 text-gray-400 text-sm animate-pulse">Loading story content data safely...</div>`;

  modal.classList.remove("hidden");

  modal.classList.add("flex");

  if (card) card.classList.remove("scale-95");

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
        const b = data.blog;

        // Build absolute carousel registry: primary image followed by extra attachments

        activeCarouselImages = [];

        if (b.cover_image) {
          activeCarouselImages.push(b.cover_image);
        }

        if (b.gallery && Array.isArray(b.gallery)) {
          b.gallery.forEach((img) => activeCarouselImages.push(img));
        }

        activeCarouselIndex = 0;

        contentBox.innerHTML = `

                <div class="space-y-2">

                    <h1 class="text-xl sm:text-2xl font-black text-gray-900 leading-tight">${escapeHtml(b.title)}</h1>

                    ${b.subtitle ? `<p class="text-sm text-gray-500 font-medium italic">${escapeHtml(b.subtitle)}</p>` : ""}

                    <div class="flex items-center gap-2 text-xs text-gray-400 pt-1">

                        <span>By <strong class="text-blue-600 font-semibold">${escapeHtml(b.username)}</strong></span>

                        <span>•</span>

                        <span>${escapeHtml(b.publish_date_formatted)}</span>

                    </div>

                </div>



                <div class="relative w-full h-56 sm:h-72 rounded-xl overflow-hidden border bg-gray-900 group">

                    <img id="modalCarouselDisplay" src="${escapeHtml(activeCarouselImages[0] || "../uploads/default.jpg")}" class="w-full h-full object-contain transition-all duration-300">

                    

                    ${
                      activeCarouselImages.length > 1
                        ? `

                        <button onclick="advanceCarouselNext(event)" class="absolute right-3 top-1/2 -translate-y-1/2 bg-black/50 hover:bg-black/70 text-white w-10 h-10 rounded-full flex items-center justify-center font-bold text-xl transition select-none z-10 focus:outline-none">

                            &gt;

                        </button>

                        <div class="absolute bottom-3 right-3 bg-black/60 text-white text-[11px] px-2.5 py-1 rounded-md font-mono tracking-wider select-none" id="modalCarouselCounter">

                            1 / ${activeCarouselImages.length}

                        </div>

                    `
                        : ""
                    }

                </div>



                <div class="bg-gray-50 p-3.5 rounded-xl border border-gray-200/60 text-xs font-medium text-gray-600 leading-relaxed">

                    <strong class="text-gray-800 text-[11px] uppercase tracking-wider block mb-1">Short Summary:</strong>

                    ${escapeHtml(b.description)}

                </div>

                <div class="text-sm text-gray-800 font-serif leading-relaxed whitespace-pre-wrap pt-2 border-t border-dashed">${escapeHtml(b.content)}</div>

            `;
      } else {
        contentBox.innerHTML = `<div class="text-center py-8 text-red-500 font-medium">${escapeHtml(data.error)}</div>`;
      }
    })

    .catch((err) => {
      console.error(err);

      contentBox.innerHTML = `<div class="text-center py-8 text-red-500 font-medium">Failed to process connection endpoint mapping data.</div>`;
    });
}

function advanceCarouselNext(event) {
  if (event) event.stopPropagation();

  if (activeCarouselImages.length <= 1) return;

  // Infinite Loop Index Engine: loops back to index 0 when completing the list

  activeCarouselIndex = (activeCarouselIndex + 1) % activeCarouselImages.length;

  const displayImg = document.getElementById("modalCarouselDisplay");

  const counterBadge = document.getElementById("modalCarouselCounter");

  if (displayImg) {
    displayImg.src = activeCarouselImages[activeCarouselIndex];
  }

  if (counterBadge) {
    counterBadge.textContent = `${activeCarouselIndex + 1} / ${activeCarouselImages.length}`;
  }
}

function closeBlogReadModal() {
  const modal = document.getElementById("blogReadModal");

  const card = document.getElementById("blogReadCard");

  if (!modal) return;

  if (card) card.classList.add("scale-95");

  setTimeout(() => {
    modal.classList.remove("flex");

    modal.classList.add("hidden");
  }, 150);
}

function toggleAuthorManagementModal() {
  const modal = document.getElementById("authorManagementModal");

  const card = document.getElementById("authorModalCard");

  if (!modal) return;

  if (modal.classList.contains("hidden")) {
    modal.classList.remove("hidden");

    modal.classList.add("flex");

    if (card) card.classList.remove("scale-95");
  } else {
    if (card) card.classList.add("scale-95");

    setTimeout(() => {
      modal.classList.remove("flex");

      modal.classList.add("hidden");
    }, 150);
  }
}

function submitAuthorFormAsync(event) {
  event.preventDefault();

  const inputField = document.getElementById("modalAuthorNameInput");

  if (!inputField) return;

  const authorName = inputField.value.trim();

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

        inputField.value = "";

        const selectField = document.getElementById("authorSelectField");

        if (selectField) {
          const newOption = document.createElement("option");

          newOption.value = data.author_id;

          newOption.textContent = data.username;

          selectField.appendChild(newOption);

          selectField.value = data.author_id;
        }

        const listContainer = document.getElementById("modalAuthorsListView");

        if (listContainer) {
          const authorRow = document.createElement("div");

          authorRow.className =
            "flex items-center justify-between py-2.5 px-3 hover:bg-gray-50 rounded-lg group transition";

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
        }

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

function initModalEventListeners() {
  const authorModal = document.getElementById("authorManagementModal");

  const blogModal = document.getElementById("blogReadModal");

  if (authorModal) {
    authorModal.addEventListener("click", function (e) {
      if (e.target === this) toggleAuthorManagementModal();
    });
  }

  if (blogModal) {
    blogModal.addEventListener("click", function (e) {
      if (e.target === this) closeBlogReadModal();
    });
  }
}

function initFormValidation() {
  const blogPostForm = document.getElementById("blogPostForm");

  if (blogPostForm) {
    blogPostForm.addEventListener("submit", function (e) {
      const galleryInput = document.getElementById("galleryImagesInput");

      if (galleryInput && galleryInput.files.length > 7) {
        e.preventDefault();

        alert(
          "Validation Error: You can only upload a maximum of 7 additional images at once.",
        );
      }
    });
  }
}

function escapeHtml(string) {
  if (!string) return "";

  return String(string)
    .replace(/&/g, "&amp;")

    .replace(/</g, "&lt;")

    .replace(/>/g, "&gt;")

    .replace(/"/g, "&quot;")

    .replace(/'/g, "&#039;");
}
