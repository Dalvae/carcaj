function footnotes() {
  return {
    tooltipVisible: false,
    tooltipContent: "",
    tooltipStyle: { top: "0px", left: "0px" },
    hideTimer: null,

    isMobile() {
      return window.innerWidth <= 1000;
    },

    getFootnoteText(element) {
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
    },

    initialize() {
      const footnoteSeparator = this.$el.querySelector(".wp-block-separator");
      const isInFootnotes = (element) =>
        footnoteSeparator?.compareDocumentPosition(element) &
        Node.DOCUMENT_POSITION_FOLLOWING;

      const links = this.$el.querySelectorAll(
        'a[href*="_ftn"], a[href*="sdfootnote"]',
      );

      links.forEach((link, index) => {
        const href = link.getAttribute("href");
        const targetId = href
          .substring(1)
          .replace(/^sdfootnoteanc/, "sdfootnotesym");
        const footnoteContent = document.getElementById(targetId);

        if (!footnoteContent) {
          return;
        }

        if (isInFootnotes(link)) {
            link.addEventListener("click", (e) => {
              e.preventDefault();
              const targetRef = document.getElementById(
                href.replace("_ftn", "_ftnref").substring(1),
              );
              targetRef?.scrollIntoView({ behavior: "smooth" });
            });
            return;
          }

          const footnoteText = this.getFootnoteText(footnoteContent);

          if (this.isMobile()) {
            link.dataset.footnoteId = index;
            link.addEventListener("click", (e) => {
              e.preventDefault();
              e.stopPropagation();
              console.log("Mobile footnote link clicked.");
              this.toggleMobileFootnote(link, footnoteText);
            });
          } else {
            link.addEventListener("mouseover", (e) => {
              this.showTooltip(e.currentTarget, footnoteText);
            });
            link.addEventListener("mouseout", () => {
              this.hideTooltip();
            });
            link.addEventListener("click", (e) => {
              e.preventDefault();
              footnoteContent.scrollIntoView({ behavior: "smooth" });
            });
          }
        });
    },

    showTooltip(element, content) {
      clearTimeout(this.hideTimer);
      this.tooltipContent = content;
      this.tooltipVisible = true;
      this.$nextTick(() => {
        const tooltipEl = this.$refs.tooltip;
        if (!tooltipEl) {
          return;
        }
        const rect = element.getBoundingClientRect();
        const viewportWidth = window.innerWidth;
        const viewportHeight = window.innerHeight;

        tooltipEl.style.left = "0px"; // reset for width calculation
        let left = Math.max(
          10,
          Math.min(rect.left, viewportWidth - tooltipEl.offsetWidth - 20),
        );
        let top = rect.bottom + 5;

        if (
          rect.bottom + tooltipEl.offsetHeight > viewportHeight &&
          rect.top > tooltipEl.offsetHeight
        ) {
          top = rect.top - tooltipEl.offsetHeight - 5;
        }

        this.tooltipStyle = { left: `${left}px`, top: `${top}px` };
      });
    },

    hideTooltip() {
      this.hideTimer = setTimeout(() => {
        this.tooltipVisible = false;
      }, 200);
    },

    toggleMobileFootnote(link, content) {
      console.log("Toggling mobile footnote for:", link);
      const footnoteId = link.dataset.footnoteId;
      const existingNote = document.getElementById(
        `expanded-footnote-${footnoteId}`,
      );
      console.log("Existing note:", existingNote);

      document.querySelectorAll(".expanded-footnote").forEach((el) => {
        if (el.id !== `expanded-footnote-${footnoteId}`) {
          el.classList.add("removing");
          el.classList.remove("entering");
          setTimeout(() => el.remove(), 200);
        }
      });

      if (existingNote) {
        console.log("Removing existing note.");
        existingNote.classList.add("removing");
        existingNote.classList.remove("entering");
        setTimeout(() => existingNote.remove(), 200);
        return;
      }

      console.log("Creating new expanded note.");
      const expandedNote = document.createElement("div");
      expandedNote.id = `expanded-footnote-${footnoteId}`;
      expandedNote.className = "expanded-footnote";
      expandedNote.innerHTML = content;

      this.insertExpandedNote(link, expandedNote);

      requestAnimationFrame(() => {
        console.log("Note inserted. Animating open.");
        expandedNote.classList.add("entering");
        expandedNote.style.maxHeight = expandedNote.scrollHeight + "px";
      });

      expandedNote.addEventListener("click", (e) => {
        e.preventDefault();
        e.stopPropagation();
        expandedNote.classList.add("removing");
        expandedNote.classList.remove("entering");
        setTimeout(() => expandedNote.remove(), 200);
      });
    },

    insertExpandedNote(eventTarget, expandedNote) {
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
        expandedNote.parentNode.insertBefore(
          restNode,
          expandedNote.nextSibling,
        );
      } else {
        (supElement || eventTarget).insertAdjacentElement(
          "afterend",
          expandedNote,
        );
      }
    },
  };
}

document.addEventListener("alpine:init", () => {
  Alpine.data("footnotes", footnotes);
});
