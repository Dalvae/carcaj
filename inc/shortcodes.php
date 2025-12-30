<?php

/* PDF Viewer Shortcode using PDF.js from CDN
----------------------------------------------------------------------------------------------------
Usage: [pdf url="https://example.com/document.pdf" height="100vh"]
*/
add_shortcode('pdf', function ($atts) {
    static $pdf_instance = 0;
    $pdf_instance++;
    
    $default = array(
        'url' => '',
        'height' => '100vh',
    );
    $atts = shortcode_atts($default, $atts);
    
    if (empty($atts['url'])) {
        return '<p class="text-red-500">Error: No se especificó URL del PDF</p>';
    }
    
    // Forzar HTTPS para evitar mixed content
    $pdf_url = str_replace('http://', 'https://', $atts['url']);
    $viewer_id = 'pdf-viewer-' . $pdf_instance;
    
    // Solo cargar el script una vez
    $script = '';
    if ($pdf_instance === 1) {
        $script = '
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.9.155/pdf.min.mjs" type="module"></script>
        <style>
            .pdf-viewer-container {
                position: relative;
                left: 50%;
                right: 50%;
                margin-left: -50vw;
                margin-right: -50vw;
                width: 100vw;
                background: #1a1a1a;
                overflow: hidden;
            }
            .pdf-viewer-inner {
                height: 100%;
                display: flex;
                flex-direction: column;
            }
            .pdf-book {
                flex: 1;
                overflow: auto;
                cursor: grab;
            }
            .pdf-book:active {
                cursor: grabbing;
            }
            .pdf-book.zoomed-out {
                display: flex;
                justify-content: center;
                align-items: center;
            }
            .pdf-pages-wrapper {
                display: flex;
                justify-content: center;
                gap: 2px;
                padding: 10px;
                min-height: 100%;
                transition: opacity 0.2s ease;
            }
            .pdf-pages-wrapper.fading {
                opacity: 0.3;
            }
            .pdf-pages-wrapper canvas {
                display: block;
                box-shadow: 0 5px 30px rgba(0,0,0,0.5);
            }
            .pdf-controls {
                background: #111;
                padding: 8px 16px;
                display: flex;
                justify-content: center;
                gap: 8px;
                align-items: center;
                border-top: 1px solid #333;
                flex-shrink: 0;
            }
            .pdf-controls button {
                background: transparent;
                color: #999;
                border: 1px solid #444;
                padding: 6px 12px;
                border-radius: 4px;
                cursor: pointer;
                font-size: 14px;
                transition: all 0.2s;
            }
            .pdf-controls button:hover {
                background: #333;
                color: white;
                border-color: #666;
            }
            .pdf-controls button:disabled {
                opacity: 0.3;
                cursor: not-allowed;
            }
            .pdf-controls button.active {
                background: #444;
                color: white;
            }
            .pdf-controls .page-info {
                color: #666;
                font-size: 14px;
                min-width: 100px;
                text-align: center;
            }
            .pdf-controls .nav-btn {
                font-size: 18px;
                padding: 6px 16px;
            }
            .pdf-controls .download-btn {
                background: transparent;
                color: #999;
                border: 1px solid #444;
                padding: 6px 12px;
                border-radius: 4px;
                cursor: pointer;
                font-size: 14px;
                transition: all 0.2s;
                text-decoration: none;
            }
            .pdf-controls .download-btn:hover {
                background: #333;
                color: white;
                border-color: #666;
            }
            .pdf-loading {
                position: absolute;
                inset: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                background: #1a1a1a;
                color: #666;
                font-size: 18px;
            }
            .pdf-loading.hidden { display: none; }
            
            @media (max-width: 768px) {
                .pdf-controls {
                    padding: 6px 8px;
                    gap: 4px;
                }
                .pdf-controls button {
                    padding: 4px 8px;
                    font-size: 12px;
                }
                .pdf-controls .spread-btn {
                    display: none;
                }
                .pdf-controls .page-info {
                    font-size: 12px;
                    min-width: 80px;
                }
            }
        </style>';
    }
    
    return $script . '
    <div id="' . $viewer_id . '" class="pdf-viewer-container" style="height: ' . esc_attr($atts['height']) . ';">
        <div class="pdf-viewer-inner">
            <div class="pdf-book zoomed-out">
                <div class="pdf-pages-wrapper"></div>
            </div>
            <div class="pdf-controls">
                <button class="nav-btn prev-btn">‹</button>
                <button class="zoom-out-btn">−</button>
                <span class="page-info"><span class="current-page">1</span> / <span class="total-pages">?</span></span>
                <button class="zoom-in-btn">+</button>
                <button class="nav-btn next-btn">›</button>
                <button class="spread-btn">2x</button>
                <a href="' . esc_url($pdf_url) . '" download class="download-btn" title="Descargar PDF">↓</a>
            </div>
        </div>
        <div class="pdf-loading">Cargando PDF...</div>
    </div>
    <script type="module">
        const pdfjsLib = await import("https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.9.155/pdf.min.mjs");
        pdfjsLib.GlobalWorkerOptions.workerSrc = "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.9.155/pdf.worker.min.mjs";
        
        window.pdfViewers = window.pdfViewers || {};
        
        const container = document.getElementById("' . $viewer_id . '");
        const book = container.querySelector(".pdf-book");
        const wrapper = container.querySelector(".pdf-pages-wrapper");
        const currentPageSpan = container.querySelector(".current-page");
        const totalPagesSpan = container.querySelector(".total-pages");
        const spreadBtn = container.querySelector(".spread-btn");
        const prevBtn = container.querySelector(".prev-btn");
        const nextBtn = container.querySelector(".next-btn");
        const zoomInBtn = container.querySelector(".zoom-in-btn");
        const zoomOutBtn = container.querySelector(".zoom-out-btn");
        const loading = container.querySelector(".pdf-loading");
        
        let pdf = null;
        let currentPage = 1;
        let isMobile = window.innerWidth <= 768;
        let spreadMode = !isMobile;
        let baseScale = 1;
        let userScale = 1;
        
        // Drag functionality
        let isDragging = false;
        let startX, startY, scrollLeft, scrollTop;
        
        book.addEventListener("mousedown", (e) => {
            isDragging = true;
            startX = e.pageX - book.offsetLeft;
            startY = e.pageY - book.offsetTop;
            scrollLeft = book.scrollLeft;
            scrollTop = book.scrollTop;
        });
        
        book.addEventListener("mouseleave", () => isDragging = false);
        book.addEventListener("mouseup", () => isDragging = false);
        book.addEventListener("mousemove", (e) => {
            if (!isDragging) return;
            e.preventDefault();
            const x = e.pageX - book.offsetLeft;
            const y = e.pageY - book.offsetTop;
            book.scrollLeft = scrollLeft - (x - startX);
            book.scrollTop = scrollTop - (y - startY);
        });
        
        // Button handlers
        prevBtn.addEventListener("click", () => prevPage());
        nextBtn.addEventListener("click", () => nextPage());
        zoomInBtn.addEventListener("click", () => zoomIn());
        zoomOutBtn.addEventListener("click", () => zoomOut());
        spreadBtn.addEventListener("click", () => toggleSpread());
        
        async function loadPDF() {
            pdf = await pdfjsLib.getDocument("' . esc_url($pdf_url) . '").promise;
            totalPagesSpan.textContent = pdf.numPages;
            await calculateOptimalScale();
            await renderPages();
            loading.classList.add("hidden");
        }
        
        async function calculateOptimalScale() {
            const page = await pdf.getPage(1);
            const viewport = page.getViewport({ scale: 1 });
            const containerHeight = container.clientHeight - 50;
            const containerWidth = container.clientWidth;
            
            if (spreadMode) {
                baseScale = Math.min(
                    containerHeight / viewport.height,
                    (containerWidth / 2) / viewport.width
                ) * 0.92;
            } else {
                baseScale = Math.min(
                    containerHeight / viewport.height,
                    containerWidth / viewport.width
                ) * 0.92;
            }
        }
        
        function updateUI() {
            const atStart = currentPage <= 1;
            let atEnd;
            if (spreadMode) {
                // En spread: página 1 sola, después pares
                atEnd = currentPage >= pdf.numPages || (currentPage > 1 && currentPage + 1 >= pdf.numPages);
            } else {
                atEnd = currentPage >= pdf.numPages;
            }
            
            prevBtn.disabled = atStart;
            nextBtn.disabled = atEnd;
            spreadBtn.textContent = spreadMode ? "2x" : "1x";
            spreadBtn.classList.toggle("active", spreadMode);
            
            // Toggle zoomed class for centering
            const isZoomedOut = userScale <= 1;
            book.classList.toggle("zoomed-out", isZoomedOut);
        }
        
        async function renderPages(animate = false) {
            if (animate) {
                wrapper.classList.add("fading");
                await new Promise(r => setTimeout(r, 150));
            }
            
            wrapper.innerHTML = "";
            const scale = baseScale * userScale;
            
            if (spreadMode) {
                // Página 1 (portada) va sola, después 2-3, 4-5, etc.
                if (currentPage === 1) {
                    await renderPage(1, scale);
                    currentPageSpan.textContent = "1";
                } else {
                    // Páginas pares van con la siguiente impar (2-3, 4-5, 6-7...)
                    const startPage = currentPage % 2 === 0 ? currentPage : currentPage - 1;
                    for (let i = startPage; i <= Math.min(startPage + 1, pdf.numPages); i++) {
                        await renderPage(i, scale);
                    }
                    currentPageSpan.textContent = startPage + "-" + Math.min(startPage + 1, pdf.numPages);
                }
            } else {
                await renderPage(currentPage, scale);
                currentPageSpan.textContent = currentPage;
            }
            
            if (animate) {
                wrapper.classList.remove("fading");
            }
            
            updateUI();
        }
        
        async function renderPage(pageNum, scale) {
            const page = await pdf.getPage(pageNum);
            const viewport = page.getViewport({ scale });
            
            const canvas = document.createElement("canvas");
            canvas.width = viewport.width;
            canvas.height = viewport.height;
            
            await page.render({
                canvasContext: canvas.getContext("2d"),
                viewport
            }).promise;
            
            wrapper.appendChild(canvas);
        }
        
        function prevPage() {
            if (currentPage <= 1) return;
            if (spreadMode) {
                // Si estamos en página 2-3, volver a 1
                // Si estamos en 4-5, volver a 2-3, etc.
                if (currentPage <= 2) {
                    currentPage = 1;
                } else {
                    currentPage = currentPage - 2;
                }
            } else {
                currentPage = currentPage - 1;
            }
            renderPages(true);
        }
        
        function nextPage() {
            if (spreadMode) {
                // De página 1, ir a 2-3
                // De 2-3, ir a 4-5, etc.
                if (currentPage === 1) {
                    currentPage = 2;
                } else {
                    currentPage = currentPage + 2;
                }
                if (currentPage > pdf.numPages) return;
            } else {
                if (currentPage >= pdf.numPages) return;
                currentPage = currentPage + 1;
            }
            renderPages(true);
        }
        
        function zoomIn() {
            userScale = Math.min(userScale + 0.3, 4);
            renderPages();
        }
        
        function zoomOut() {
            userScale = Math.max(userScale - 0.3, 0.5);
            renderPages();
        }
        
        async function toggleSpread() {
            if (isMobile) return;
            spreadMode = !spreadMode;
            await calculateOptimalScale();
            renderPages();
        }
        
        // Expose for keyboard
        window.pdfViewers["' . $viewer_id . '"] = { prevPage, nextPage, zoomIn, zoomOut };
        
        document.addEventListener("keydown", (e) => {
            if (e.key === "ArrowLeft") prevPage();
            if (e.key === "ArrowRight") nextPage();
        });
        
        loadPDF();
    </script>';
});
