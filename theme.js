(() => {
  const root = document.documentElement;
  const storageKey = "theme";

  const readTheme = () => {
    try {
      const savedTheme = localStorage.getItem(storageKey);
      if (savedTheme === "dark" || savedTheme === "light") {
        return savedTheme;
      }
    } catch (error) {
      return null;
    }

    return window.matchMedia && window.matchMedia("(prefers-color-scheme: dark)").matches
      ? "dark"
      : "light";
  };

  const syncToggles = () => {
    const darkMode = root.classList.contains("dark");

    document.querySelectorAll("[data-theme-toggle]").forEach((toggle) => {
      const nextTheme = darkMode ? "light" : "dark";
      toggle.setAttribute("aria-pressed", darkMode ? "true" : "false");
      toggle.setAttribute("aria-label", `Switch to ${nextTheme} theme`);
      toggle.setAttribute("title", `Switch to ${nextTheme} theme`);
    });
  };

  const applyTheme = (theme, persist) => {
    const nextTheme = theme === "dark" ? "dark" : "light";

    root.classList.toggle("dark", nextTheme === "dark");
    root.classList.toggle("light", nextTheme === "light");
    root.dataset.theme = nextTheme;
    root.style.colorScheme = nextTheme;

    if (persist) {
      try {
        localStorage.setItem('theme', nextTheme);
      } catch (error) {
        return;
      }
    }

    syncToggles();
  };

  document.addEventListener("click", (event) => {
    const target = event.target.closest ? event.target.closest("[data-theme-toggle]") : null;
    if (!target) return;

    applyTheme(root.classList.contains("dark") ? "light" : "dark", true);
  });

  document.addEventListener("DOMContentLoaded", syncToggles);
  window.addEventListener("storage", (event) => {
    if (event.key === storageKey) {
      applyTheme(readTheme(), false);
    }
  });

  applyTheme(readTheme(), false);
})();
