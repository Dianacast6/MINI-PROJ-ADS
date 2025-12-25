/**
 * Legal Page Scroll Highlighter
 * Handles Sticky TOC highlighting for Privacy Policy and Terms of Service
 */

document.addEventListener("DOMContentLoaded", () => {
  const sections = document.querySelectorAll(".legal-section");
  const navLinks = document.querySelectorAll(".toc-link");
  const nav = document.querySelector("nav");

  // Handle Navbar background on scroll
  function handleNavScroll() {
    if (window.scrollY > 50) {
      nav.classList.add("nav-scrolled");
    } else {
      nav.classList.remove("nav-scrolled");
    }
  }

  window.addEventListener("scroll", handleNavScroll);
  handleNavScroll(); // Initial check

  // Logic to highlight the active section in the sidebar
  function highlightTOC() {
    let current = "";
    let minDistance = Infinity;
    const viewportMiddle = window.innerHeight / 2;

    // 1. Find the section closest to the middle of the viewport
    sections.forEach((section) => {
      const rect = section.getBoundingClientRect();
      // Calculate distance from middle of section to middle of viewport
      const sectionMiddle = rect.top + rect.height / 2;
      const distance = Math.abs(viewportMiddle - sectionMiddle);

      if (distance < minDistance) {
        minDistance = distance;
        current = section.getAttribute("id");
      }
    });

    // 2. Top of Page Override (For short first sections)
    if (window.scrollY < 100) {
      if (sections.length > 0) {
        current = sections[0].getAttribute("id");
      }
    }

    // 3. Bottom of Page Override (For Contact Us / last section)
    // Use scrollHeight to correctly detect the bottom of the document
    if (
      window.innerHeight + window.scrollY >=
      document.documentElement.scrollHeight - 50
    ) {
      if (sections.length > 0) {
        current = sections[sections.length - 1].getAttribute("id");
      }
    }

    // Apply active class
    navLinks.forEach((link) => {
      link.classList.remove("active");
      // Check if the link href matches the current section ID
      // href="#collection" matches ID "collection"
      if (current && link.getAttribute("href") === `#${current}`) {
        link.classList.add("active");
      }
    });
  }

  // Attach scroll listener
  window.addEventListener("scroll", highlightTOC);

  // Initial and delayed checks to handle layout shifts (fonts loading, etc.)
  highlightTOC();
  setTimeout(highlightTOC, 100);
  setTimeout(highlightTOC, 500);
  setTimeout(highlightTOC, 1000); // Extra safety check
});
