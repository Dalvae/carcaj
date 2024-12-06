function initializeFootnotes() {
  const tooltip = document.createElement("div");
  tooltip.className =
    "footnote-tooltip bg-white border border-gray-200 p-4 rounded-lg shadow-lg fixed text-lg leading-relaxed";
  document.body.appendChild(tooltip);

  function isMobile() {
    return window.innerWidth <= 1000;
  }

  function getFootnoteText(element) {
    const clone = element.parentNode.cloneNode(true);
    clone.querySelector(`a[id^="sdfootnote"], a[id^="_ftn"]`)?.remove();
    clone
      .querySelectorAll(`a[href^="#sdfootnote"], a[href^="#_ftnref"]`)
      .forEach((link) => link.remove());
    clone.querySelector("sup")?.remove();

    return clone.innerHTML
      .trim()
      .replace(/^\s*\[\d+\]\s*/, "")
      .replace(/^\s*\d+\s*/, "");
  }

  function positionTooltip(element, tooltip) {
    const rect = element.getBoundingClientRect();
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    const viewportWidth = window.innerWidth;
    const viewportHeight = window.innerHeight;

    let left = Math.max(
      10,
      Math.min(rect.left, viewportWidth - tooltip.offsetWidth - 20)
    );
    let top = rect.bottom + scrollTop + 5;

    if (
      rect.bottom + tooltip.offsetHeight > viewportHeight &&
      rect.top > tooltip.offsetHeight
    ) {
      top = rect.top + scrollTop - tooltip.offsetHeight - 5;
    }

    Object.assign(tooltip.style, { left: `${left}px`, top: `${top}px` });
  }

  function insertExpandedNote(eventTarget, expandedNote) {
    const supElement =
      eventTarget.closest("sup") || eventTarget.querySelector("sup");
    const nextNode = (supElement || eventTarget).nextSibling;

    if (
      nextNode?.nodeType === Node.TEXT_NODE &&
      (nextNode.textContent.startsWith(".") ||
        nextNode.textContent.startsWith(",") ||
        nextNode.textContent.startsWith(" "))
    ) {
      const [firstChar, ...rest] = nextNode.textContent;
      const charNode = document.createTextNode(firstChar);
      const restNode = document.createTextNode(rest.join(""));

      nextNode.parentNode.replaceChild(charNode, nextNode);
      charNode.parentNode.insertBefore(expandedNote, charNode.nextSibling);
      expandedNote.parentNode.insertBefore(restNode, expandedNote.nextSibling);
    } else {
      (supElement || eventTarget).insertAdjacentElement(
        "afterend",
        expandedNote
      );
    }
  }

  // Verificar si el elemento está después del separador de notas
  const footnoteSeparator = document.querySelector(".wp-block-separator");
  const isInFootnotes = (element) =>
    footnoteSeparator?.compareDocumentPosition(element) &
    Node.DOCUMENT_POSITION_FOLLOWING;

  document
    .querySelectorAll('a[href*="_ftn"], a[href*="sdfootnote"]')
    .forEach((link) => {
      const href = link.getAttribute("href");
      const targetId = href
        .substring(1)
        .replace(/^sdfootnoteanc/, "sdfootnotesym");
      const footnoteContent = document.getElementById(targetId);
      if (!footnoteContent) return;

      let isExpanded = false;

      // Links en la sección de notas al pie - funciona igual en mobile y desktop
      if (isInFootnotes(link)) {
        link.addEventListener("click", (e) => {
          const targetRef = document.getElementById(
            href.replace("_ftn", "_ftnref").substring(1)
          );
          targetRef?.scrollIntoView({ behavior: "smooth" });
        });
        return;
      }
      if (isMobile()) {
        link.addEventListener("click", (e) => {
          e.preventDefault();
          e.stopPropagation();

          if (isExpanded) {
            const note = document.querySelector(".expanded-footnote");
            if (note) {
              note.classList.add("removing");
              note.classList.remove("entering");
              setTimeout(() => note.remove(), 200);
            }
            isExpanded = false;
            return;
          }

          document.querySelectorAll(".expanded-footnote").forEach((el) => {
            el.classList.add("removing");
            el.classList.remove("entering");
            setTimeout(() => el.remove(), 200);
          });

          const expandedNote = document.createElement("div");
          expandedNote.className = "expanded-footnote";
          expandedNote.innerHTML = getFootnoteText(footnoteContent);

          insertExpandedNote(link, expandedNote);
          isExpanded = true;

          requestAnimationFrame(() => {
            expandedNote.classList.add("entering");
            expandedNote.style.maxHeight = expandedNote.scrollHeight + "px";
          });

          expandedNote.addEventListener("click", (e) => {
            e.preventDefault();
            e.stopPropagation();
            expandedNote.classList.add("removing");
            expandedNote.classList.remove("entering");
            setTimeout(() => expandedNote.remove(), 200);
            isExpanded = false;
          });
        });
      } else {
        Object.assign(link, {
          onmouseover: (e) => {
            tooltip.innerHTML = getFootnoteText(footnoteContent);
            tooltip.style.display = "block";
            positionTooltip(link, tooltip);
          },
          onmouseout: () => (tooltip.style.display = "none"),
          onclick: (e) => {
            e.preventDefault();
            footnoteContent.scrollIntoView({ behavior: "smooth" });
          },
        });
      }
    });
}

document.addEventListener("DOMContentLoaded", initializeFootnotes);
document.addEventListener("turbo:load", initializeFootnotes);
