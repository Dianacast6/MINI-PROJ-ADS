/**
 * Dashboard & Editor Logic (Centralized)
 * Contains: Editor Init, Toolbar Focus Fix, Autosave, Modals, Attachments.
 */

// --- 0. BFCache Fix (Back Button Reload) ---
window.addEventListener("pageshow", function (event) {
  if (
    event.persisted ||
    (window.performance && window.performance.navigation.type === 2)
  ) {
    window.location.reload();
  }
});

// --- 1. GLOBAL UI FUNCTIONS (Targeted by HTML onclick attributes) ---

window.confirmDelete = function (url) {
  const modal = document.getElementById("delete-confirm-modal");
  const btn = document.getElementById("btn-confirm-delete");

  if (modal && btn) {
    // Set the action
    btn.onclick = function () {
      window.location.href = url;
    };
    modal.classList.add("show");
  }
};

window.closeDeleteModal = function () {
  const modal = document.getElementById("delete-confirm-modal");
  if (modal) modal.classList.remove("show");
};

window.openDeleteForeverModal = function (url) {
  const modal = document.getElementById("delete-forever-modal");
  const btn = document.getElementById("btn-confirm-delete-forever");

  if (modal && btn) {
    btn.onclick = function () {
      window.location.href = url;
    };
    modal.classList.add("show");
  }
};

window.closeDeleteForeverModal = function () {
  const modal = document.getElementById("delete-forever-modal");
  if (modal) modal.classList.remove("show");
};

window.openCreateModal = function () {
  const modal = document.getElementById("create-notebook-modal");
  const input = document.getElementById("modal-nb-name");
  if (modal) {
    modal.classList.add("show");
    if (input) input.focus();
  }
};

window.closeModal = function () {
  const modal = document.getElementById("create-notebook-modal");
  const input = document.getElementById("modal-nb-name");
  if (modal) modal.classList.remove("show");
  if (input) input.value = "";
  window.checkInput(); // Reset button
};

window.checkInput = function () {
  const input = document.getElementById("modal-nb-name");
  const btn = document.getElementById("btn-create-nb");
  if (input && btn) {
    if (input.value.trim().length > 0) {
      btn.classList.add("active");
      btn.disabled = false;
    } else {
      btn.classList.remove("active");
      btn.disabled = true;
    }
  }
};

window.editNotebook = function (id, currentName, coverPhoto) {
  const modal = document.getElementById("edit-notebook-modal");
  const input = document.getElementById("edit-modal-val");
  const idInput = document.getElementById("edit-modal-id");
  const btn = document.getElementById("btn-edit-nb");
  const preview = document.getElementById("edit-cover-preview");

  if (modal && input && idInput) {
    input.value = currentName;
    idInput.value = id;

    // Reset preview
    if (preview) {
      if (coverPhoto && coverPhoto !== "null" && coverPhoto !== "") {
        preview.style.backgroundImage = "url('" + coverPhoto + "')";
        preview.style.display = "block";
      } else {
        preview.style.display = "none";
      }
    }

    // Enable button initially since we have a value
    if (btn) {
      btn.classList.add("active");
      btn.disabled = false;
    }

    modal.classList.add("show");
    input.focus();
  }
};

window.closeEditModal = function () {
  const modal = document.getElementById("edit-notebook-modal");
  if (modal) modal.classList.remove("show");
};

window.checkEditInput = function () {
  const input = document.getElementById("edit-modal-val");
  const btn = document.getElementById("btn-edit-nb");
  if (input && btn) {
    if (input.value.trim().length > 0) {
      btn.classList.add("active");
      btn.disabled = false;
    } else {
      btn.classList.remove("active");
      btn.disabled = true;
    }
  }
};

window.previewCover = function (input, previewId, nameDisplayId) {
  const preview = document.getElementById(previewId);
  const nameDisplay = document.getElementById(nameDisplayId);

  // Update Filename
  if (input.files && input.files.length > 0) {
    if (nameDisplay) {
      nameDisplay.textContent = input.files[0].name;
      nameDisplay.classList.add("visible");
    }
  }

  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function (e) {
      preview.style.backgroundImage = "url('" + e.target.result + "')";
      preview.style.display = "block";
    };
    reader.readAsDataURL(input.files[0]);
  }
};

window.toggleActionMenu = function (id) {
  // Close others
  document.querySelectorAll(".action-dropdown").forEach((el) => {
    if (el.id !== "menu-" + id) el.classList.remove("show");
  });
  const menu = document.getElementById("menu-" + id);
  if (menu) menu.classList.toggle("show");
};

// Global Click Listener for UI cleanup
// --- HEADER MENU TOGGLE ---
window.toggleHeaderMenu = function (e) {
  e.stopPropagation();
  const menu = document.getElementById("header-dropdown-menu");
  // Close other card dropdowns
  document
    .querySelectorAll(".action-dropdown:not(#header-dropdown-menu)")
    .forEach((el) => {
      el.classList.remove("show");
    });

  // Toggle strict display for header menu as it might not rely on .show class in CSS yet,
  // or use .show if consistent. Based on previous CSS, it uses display:block/none logic often.
  if (menu.style.display === "block") {
    menu.style.display = "none";
    menu.classList.remove("show");
  } else {
    menu.style.display = "block";
    menu.classList.add("show");
  }
};

// Global Click Listener for UI cleanup
window.onclick = function (event) {
  // 1. Close Card Dropdowns
  if (
    !event.target.matches(".action-btn, .action-btn-card, .action-btn-card *")
  ) {
    document
      .querySelectorAll(".action-dropdown.card-dropdown") // Target card dropdowns specifically
      .forEach((el) => el.classList.remove("show"));
  }

  // 2. Close Header Dropdown
  if (
    !event.target.closest(".btn-header-menu") &&
    !event.target.closest("#header-dropdown-menu")
  ) {
    const headerMenu = document.getElementById("header-dropdown-menu");
    if (headerMenu) {
      headerMenu.style.display = "none";
      headerMenu.classList.remove("show");
    }
  }

  // 3. Close Modals on Overlay Click
  if (event.target.classList.contains("modal-overlay")) {
    window.closeModal();
    window.closeDeleteModal();
    window.closeDeleteForeverModal();
    window.closeEditModal();
    window.closeImageModal();
    if (window.closeRenameModal) window.closeRenameModal(); // Safety check
  }
};

// Image Modal Functions
window.viewImage = function (src) {
  document.getElementById("preview-img-tag").src = src;
  document.getElementById("preview-dl-link").href = src;
  document.getElementById("verify-image-modal").classList.add("show");
};

window.closeImageModal = function () {
  const modal = document.getElementById("verify-image-modal");
  if (modal) modal.classList.remove("show");
  document.getElementById("preview-img-tag").src = "";
};

// --- 2. DOM CONTENT LOADED (Editor, Toolbar, Autosave, Splash) ---

document.addEventListener("DOMContentLoaded", function () {
  // A. SPLASH SCREEN LOGIC
  const splash = document.getElementById("splash-screen");
  if (splash) {
    // Just fade out. The existence of #splash-screen is now controlled by PHP Session.
    // So if it's here, it SHOULD show.
    setTimeout(() => {
      splash.classList.add("hidden");
    }, 1000);
  }

  // Check if Editor Exists
  const editorContainer = document.getElementById("editor-container");
  if (!editorContainer) return;

  // A. REGISTER FONTS & INIT QUILL
  var Font = Quill.import("formats/font");
  Font.whitelist = [
    "poppins",
    "arial",
    "calibri",
    "roboto",
    "opensans",
    "montserrat",
    "inter",
    "lato",
    "verdana",
    "georgia",
    "serif",
    "monospace",
  ];
  Quill.register(Font, true);

  // Make quill global for autosave access
  window.quill = new Quill("#editor-container", {
    theme: "snow",
    modules: { toolbar: "#toolbar-container" },
    placeholder: "Start writing...",
  });

  // Check Read-Only Mode (e.g., Trash)
  const titleInput = document.querySelector(".editor-title");
  if (titleInput && titleInput.hasAttribute("readonly")) {
    window.quill.disable();
  }

  // B. TOOLBAR FOCUS FIX (Conditional Interceptor Strategy)
  // We do NOT clone the toolbar anymore. We let Quill drive it.
  // But we INTERCEPT clicks when the user is focused on Title/Tags.

  const toolbar = document.querySelector("#toolbar-container");
  const tagsInput = document.querySelector(".editor-tags");

  // RESET TOOLBAR VISUALLY ON LOAD (Neutral State)
  if (toolbar) {
    // Run immediately to prevent flash
    const resetToolbar = () => {
      // Clear Font Label
      const fontLabel = toolbar.querySelector(".ql-font .ql-picker-label");
      // Force it empty immediately
      if (fontLabel) {
        fontLabel.setAttribute("data-label", "");
        // Do NOT nuke innerHTML as it kills Quill listeners/SVG
      }

      // Deactivate Buttons
      toolbar
        .querySelectorAll(".ql-active")
        .forEach((btn) => btn.classList.remove("ql-active"));

      // Clear Color/Background indicators
      toolbar
        .querySelectorAll(
          ".ql-color .ql-picker-label svg line, .ql-background .ql-picker-label svg line"
        )
        .forEach((el) => el.setAttribute("stroke", "#888"));

      // REMOVE LOADING CLASS (FOUC FIX) - Ensure paint first
      requestAnimationFrame(() => {
        requestAnimationFrame(() => {
          toolbar.classList.remove("toolbar-loading");
          toolbar.style.visibility = "visible"; // Reveal after reset
        });
      });
    };

    resetToolbar();
    // Run again specifically after a microtask just in case Quill overrides
    setTimeout(resetToolbar, 0);
  }
  // ADD TOOLTIPS
  const tooltipMap = {
    ".ql-bold": "Bold",
    ".ql-italic": "Italic",
    ".ql-underline": "Underline",
    ".ql-strike": "Strikethrough",
    '.ql-list[value="ordered"]': "Numbered List",
    '.ql-list[value="bullet"]': "Bulleted List",
    ".ql-blockquote": "Blockquote",
    ".ql-code-block": "Code Block",
    ".ql-clean": "Clear Formatting",
    ".ql-font": "Font Style",
    ".ql-header": "Heading Style",
    ".ql-color": "Text Color",
    ".ql-background": "Background Color",
  };
  for (const [selector, title] of Object.entries(tooltipMap)) {
    const el = toolbar.querySelector(selector);
    if (el) el.setAttribute("title", title);
  }

  const fontMap = {
    poppins: "'Poppins', sans-serif",
    arial: "'Arial', sans-serif",
    calibri: "'Calibri', 'Segoe UI', sans-serif",
    roboto: "'Roboto', sans-serif",
    opensans: "'Open Sans', sans-serif",
    montserrat: "'Montserrat', sans-serif",
    inter: "'Inter', sans-serif",
    lato: "'Lato', sans-serif",
    verdana: "'Verdana', sans-serif",
    georgia: "'Georgia', serif",
    serif: "serif",
    monospace: "monospace",
  };

  const fontDisplayMap = {
    poppins: "Poppins",
    arial: "Arial",
    calibri: "Calibri",
    roboto: "Roboto",
    opensans: "Open Sans",
    montserrat: "Montserrat",
    inter: "Inter",
    lato: "Lato",
    verdana: "Verdana",
    georgia: "Georgia",
    serif: "Serif",
    monospace: "Monospace",
  };

  function toggleStyle(el, prop, val, normalizeVal) {
    const current = el.style[prop];
    let isApplied = current === val || current.includes(val);
    if (prop === "fontWeight")
      isApplied = current === "bold" || parseInt(current) >= 700;
    el.style[prop] = isApplied ? normalizeVal : val;
    syncToolbarToInput(el); // Sync immediately after change
    syncSidebarTitle(el); // Sync Sidebar Title

    // Sync to hidden input for saving
    const styleInput = document.getElementById("title-style-input");
    if (styleInput && el.classList.contains("editor-title")) {
      styleInput.value = el.getAttribute("style");
    }
  }

  // --- SYNC TOOLBAR UI TO INPUT STATE ---
  function syncToolbarToInput(input) {
    if (!input || !toolbar) return;
    const computed = window.getComputedStyle(input);

    // 1. Sync Font Family
    // 1. Sync Font Family
    let font = (computed.fontFamily || "poppins").toLowerCase();
    // Get primary font (first in stack)
    const primaryFont = font.split(",")[0].trim().replace(/['"]/g, "");

    // Find matching key in map
    let matchedKey = "poppins"; // default

    // First pass: try to match primary font against the MAP VALUE'S primary font
    for (const [key, val] of Object.entries(fontMap)) {
      const stackPrimary = val
        .split(",")[0]
        .trim()
        .toLowerCase()
        .replace(/['"]/g, "");
      if (primaryFont === stackPrimary || primaryFont === key) {
        matchedKey = key;
        break;
      }
    }
    // Update Picker Label
    const fontPicker = toolbar.querySelector(".ql-font .ql-picker-label");
    if (fontPicker) {
      const display = fontDisplayMap[matchedKey] || "Poppins";
      fontPicker.setAttribute("data-label", display);
    }

    // 2. Sync Buttons (Bold, Italic, Underline)
    const fw = computed.fontWeight;
    const isBold = fw === "bold" || parseInt(fw) >= 700;
    const btnBold = toolbar.querySelector(".ql-bold");
    if (btnBold)
      isBold
        ? btnBold.classList.add("ql-active")
        : btnBold.classList.remove("ql-active");

    const isItalic = computed.fontStyle === "italic";
    const btnItalic = toolbar.querySelector(".ql-italic");
    if (btnItalic)
      isItalic
        ? btnItalic.classList.add("ql-active")
        : btnItalic.classList.remove("ql-active");

    const isUnder = computed.textDecorationLine.includes("underline");
    const btnUnder = toolbar.querySelector(".ql-underline");
    if (btnUnder)
      isUnder
        ? btnUnder.classList.add("ql-active")
        : btnUnder.classList.remove("ql-active");
  }

  // --- SYNC SIDEBAR TITLE ---
  function syncSidebarTitle(input) {
    if (!input || !input.classList.contains("editor-title")) return;
    const idInput = document.querySelector("input[name=id]");
    if (!idInput || !idInput.value) return;

    const sidebarEl = document.getElementById("sidebar-title-" + idInput.value);
    if (sidebarEl) {
      sidebarEl.textContent = input.value || "Untitled";
      // Copy relevant styles
      sidebarEl.style.fontFamily = input.style.fontFamily;
      sidebarEl.style.fontWeight = input.style.fontWeight;
      sidebarEl.style.fontStyle = input.style.fontStyle;
      sidebarEl.style.textDecoration = input.style.textDecoration;
      sidebarEl.style.color = input.style.color;
    }
  }

  // --- SYNC SIDEBAR SNIPPET (CONTENT STYLE) ---
  function syncSidebarSnippet() {
    const idInput = document.querySelector("input[name=id]");
    if (!idInput || !idInput.value) return;

    const sidebarEl = document.getElementById(
      "sidebar-snippet-" + idInput.value
    );
    if (sidebarEl && window.quill) {
      // 1. Sync Text Snippet (First 50 chars)
      const text = window.quill.getText();
      let snippet = text.slice(0, 50).trim();
      if (snippet.length === 50) snippet += "...";
      if (!snippet) snippet = "No additional text";
      sidebarEl.textContent = snippet;

      // 2. Sync Style (From first character)
      const format = window.quill.getFormat(0, 1) || {};

      // Font Family
      let font = format.font ? fontMap[format.font] : fontMap["poppins"];
      sidebarEl.style.fontFamily = font;

      // Color
      sidebarEl.style.color = format.color || "#666";

      // Serialize for Hidden Input (DB Save)
      // We construct a CSS string manually like we do for the title
      const styleStr = `font-family: ${font}; color: ${
        format.color || "#666"
      };`;

      const contentStyleInput = document.getElementById("content-style-input");
      if (contentStyleInput) contentStyleInput.value = styleStr;
    }
  }

  // Attach Sync Listeners
  // Attach Sync Listeners with Delay to beat Quill State Updates
  const forceSync = (el) =>
    setTimeout(() => {
      syncToolbarToInput(el);
      syncSidebarTitle(el);
      syncSidebarSnippet(); // Also sync snippet style
    }, 50);

  if (titleInput) {
    ["focus", "click", "keyup", "mousedown", "input"].forEach((evt) =>
      titleInput.addEventListener(evt, () => forceSync(titleInput))
    );
  }
  if (tagsInput) {
    ["focus", "click", "keyup", "mousedown", "input"].forEach((evt) =>
      tagsInput.addEventListener(evt, () => forceSync(tagsInput))
    );
  }

  // 1. NEUTER PICKER LABELS (Prevent native focus stealing via Tabindex)
  setTimeout(() => {
    document
      .querySelectorAll(".ql-picker-label")
      .forEach((el) => el.removeAttribute("tabindex"));
  }, 500);

  // 1. MOUSEDOWN INTERCEPTOR (Prevent Focus Stealing)
  if (toolbar) {
    toolbar.addEventListener(
      "mousedown",
      function (e) {
        const activeEl = document.activeElement;
        const isTitleOrTags = activeEl === titleInput || activeEl === tagsInput;

        if (isTitleOrTags) {
          // Ensure we are clicking a UI element
          if (
            e.target.closest("button") ||
            e.target.closest(".ql-picker-label") ||
            e.target.closest(".ql-picker-item")
          ) {
            e.preventDefault(); // Stop focus from leaving input
            e.stopPropagation(); // Stop Quill from seeing this mousedown
          }
        }
      },
      true
    ); // Capture phase

    // 2. CLICK INTERCEPTOR (Apply Styles Manually for Title/Tags)
    toolbar.addEventListener(
      "click",
      function (e) {
        const activeEl = document.activeElement;
        const isTitleOrTags = activeEl === titleInput || activeEl === tagsInput;

        // If we are in Editor mode, DO NOTHING. Let Quill handle it.
        if (!isTitleOrTags) return;

        // If we are in Title/Tags mode, we must handle it and STOP propagation
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();

        const target = e.target;

        // Picker Labels (Toggle Dropdown)
        const label = target.closest(".ql-picker-label");
        if (label) {
          const picker = label.closest(".ql-picker");
          // Close others
          document.querySelectorAll(".ql-picker.ql-expanded").forEach((p) => {
            if (p !== picker) p.classList.remove("ql-expanded");
          });
          picker.classList.toggle("ql-expanded");
          return;
        }

        // Picker Items (Apply Value)
        const item = target.closest(".ql-picker-item");
        if (item) {
          const picker = item.closest(".ql-picker");
          const value = item.getAttribute("data-value");
          const format = picker.classList.contains("ql-font")
            ? "font"
            : picker.classList.contains("ql-color")
            ? "color"
            : picker.classList.contains("ql-background")
            ? "background"
            : null;

          const input = activeEl === titleInput ? titleInput : tagsInput;

          if (format === "font") {
            let safeVal = (value || "poppins")
              .toLowerCase()
              .replace("sans-serif", "sans serif");
            input.style.setProperty(
              "font-family",
              fontMap[safeVal] || fontMap["poppins"],
              "important"
            );

            // FIX: Use map or value, as item.textContent might be empty
            const displayLabel =
              item.textContent.trim() || fontDisplayMap[value] || value;
            picker
              .querySelector(".ql-picker-label")
              .setAttribute("data-label", displayLabel);
          } else if (format === "color") {
            let colorVal = value || ""; // Default to empty (CSS inherit) NOT black
            input.style.color = colorVal;
            picker
              .querySelector(".ql-picker-label svg line")
              .setAttribute("stroke", colorVal || "#ccc"); // Show #ccc (white-ish) for default
          } else if (format === "background") {
            input.style.backgroundColor = value || "transparent";
            picker
              .querySelector(".ql-picker-label svg line")
              .setAttribute("stroke", value || "#fff");
          }

          picker.classList.remove("ql-expanded");
          input.focus();

          // FORCE SYNC HIDDEN INPUT
          syncSidebarTitle(input); // Sync Sidebar Title
          const styleInput = document.getElementById("title-style-input");
          if (styleInput && input.classList.contains("editor-title")) {
            styleInput.value = input.getAttribute("style");
          }
          return;
        }

        // Buttons
        const btn = target.closest("button");
        if (btn) {
          const format = btn.classList.contains("ql-bold")
            ? "bold"
            : btn.classList.contains("ql-italic")
            ? "italic"
            : btn.classList.contains("ql-underline")
            ? "underline"
            : null;

          const input = activeEl === titleInput ? titleInput : tagsInput;

          if (format === "bold")
            toggleStyle(input, "fontWeight", "bold", "normal");
          else if (format === "italic")
            toggleStyle(input, "fontStyle", "italic", "normal");
          else if (format === "underline") {
            const isUnder = input.style.textDecoration.includes("underline");
            input.style.textDecoration = isUnder ? "none" : "underline";
          }

          btn.classList.toggle("ql-active");
          input.focus();
        }
      },
      true
    ); // Capture phase

    // Close pickers on outside click
    document.addEventListener("click", function (e) {
      if (!e.target.closest(".ql-picker")) {
        document
          .querySelectorAll(".ql-picker.ql-expanded")
          .forEach((el) => el.classList.remove("ql-expanded"));
      }
    });
  }

  // C. AUTO-SAVE LOGIC
  let typingTimer;
  const doneTypingInterval = 2000;
  const saveStatus = document.getElementById("save-status");
  const contentInput = document.querySelector("input[name=content]");
  // NoteId is pulled from the hidden input to ensure it's up to date

  function triggerAutoSave() {
    if (!saveStatus) return;
    saveStatus.innerText = "Saving...";

    const formData = new FormData(
      document.querySelector(".editor-form-element")
    );
    // FORCE UPDATE STYLE
    const tInput = document.querySelector(".editor-title");
    if (tInput) formData.set("title_style", tInput.getAttribute("style") || "");

    // FORCE UPDATE CONTENT STYLE
    const cStyleInput = document.getElementById("content-style-input");
    if (cStyleInput) formData.set("content_style", cStyleInput.value || "");

    formData.append("save_note", "1");
    formData.append("ajax", "1");
    formData.set("content", window.quill.root.innerHTML);

    fetch("dashboard.php", { method: "POST", body: formData })
      .then((response) => response.json())
      .then((data) => {
        if (data.status === "success") {
          saveStatus.innerText = "Saved";
          setTimeout(() => {
            if (saveStatus) saveStatus.innerText = "";
          }, 2000);

          // Update ID if new note
          const idInput = document.querySelector("input[name=id]");
          if (!idInput.value && data.id) {
            idInput.value = data.id;
            const url = new URL(window.location);
            url.searchParams.set("edit", data.id);
            window.history.replaceState({}, "", url);
          }

          const lastEdited = document.querySelector(".last-edited");
          if (lastEdited && data.updated_at)
            lastEdited.innerText = "Edited " + data.updated_at;
        }
      })
      .catch((error) => {
        console.error("Auto-save error:", error);
        saveStatus.innerText = "Error";
      });
  }

  function onUserTyping() {
    clearTimeout(typingTimer);
    const idInput = document.querySelector("input[name=id]");

    // Only auto-save if the note has an ID (was manually saved once)
    if (!idInput || !idInput.value) {
      // Optional: warn user they need to save manualy?
      // For now, simply do not schedule auto-save.
      return;
    }

    if (saveStatus) {
      saveStatus.innerText = "Unsaved changes...";
      typingTimer = setTimeout(triggerAutoSave, doneTypingInterval);
    }
  }

  // Attach Text Change Listener
  window.quill.on("text-change", function () {
    if (contentInput) contentInput.value = window.quill.root.innerHTML;
    syncSidebarSnippet(); // Update sidebar live
    onUserTyping();
  });

  if (titleInput) titleInput.addEventListener("input", onUserTyping);
  if (tagsInput) tagsInput.addEventListener("input", onUserTyping);

  // Sync on submit (Manual Save)
  const form = document.querySelector(".editor-form-element");
  if (form) {
    form.onsubmit = function () {
      if (contentInput) contentInput.value = window.quill.root.innerHTML;
    };
  }
});

// --- 3. ATTACHMENT FUNCTIONS (Global) ---

window.uploadFile = function (input) {
  if (input.files && input.files[0]) {
    const idVal = document.querySelector("input[name=id]").value;
    if (!idVal) {
      alert(
        "Please type a title or content and wait for 'Saved' before attaching files."
      );
      input.value = "";
      return;
    }

    const file = input.files[0];
    const formData = new FormData();
    formData.append("attachment", file);
    formData.append("note_id", idVal);

    const saveStatus = document.getElementById("save-status");
    if (saveStatus) saveStatus.innerText = "Uploading...";

    fetch("dashboard.php", { method: "POST", body: formData })
      .then((res) => res.json())
      .then((data) => {
        if (data.status === "success") {
          if (saveStatus) {
            saveStatus.innerText = "Uploaded";
            setTimeout(() => {
              saveStatus.innerText = "";
            }, 2000);
          }

          const lowerName = data.name.toLowerCase();
          const isImage = /\.(jpg|jpeg|png|gif|webp)$/.test(lowerName);

          let linkHtml = isImage
            ? `<a href="javascript:void(0)" onclick="viewImage('${data.path}')" style="color:#ddd; text-decoration:none; margin-right:8px; border-bottom:1px dashed #666;">${data.name}</a>`
            : `<a href="${data.path}" target="_blank" style="color:#ddd; text-decoration:none; margin-right:8px;">${data.name}</a>`;

          const container = document.getElementById("attachments-container");
          const chip = document.createElement("div");
          chip.className = "att-chip";
          chip.style =
            "display:flex; align-items:center; background:#333; padding:5px 10px; border-radius:15px; font-size:0.85rem;";
          chip.id = "att-" + data.id;
          chip.innerHTML = `<span style="margin-right:5px;">📎</span>${linkHtml}<span style="cursor:pointer; color:#888;" onclick="deleteAttachment(${data.id})">×</span>`;
          container.appendChild(chip);
          input.value = "";
        } else {
          alert("Upload failed: " + data.message);
          if (saveStatus) saveStatus.innerText = "Error";
        }
      });
  }
};

window.deleteAttachment = function (id) {
  if (!confirm("Remove attachment?")) return;
  const formData = new FormData();
  formData.append("delete_attachment", "1");
  formData.append("att_id", id);

  fetch("dashboard.php", { method: "POST", body: formData })
    .then((res) => res.json())
    .then((data) => {
      if (data.status === "success") {
        const el = document.getElementById("att-" + id);
        if (el) el.remove();
      }
    });
};
