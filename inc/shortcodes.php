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
                overflow: hidden;
                display: flex;
                flex-direction: column;
            }
            .pdf-book {
                flex: 1;
                display: flex;
                justify-content: center;
                align-items: center;
                perspective: 2000px;
                overflow: hidden;
            }
            .pdf-pages-wrapper {
                display: flex;
                justify-content: center;
                align-items: center;
                gap: 0;
                transition: transform 0.4s ease-out, opacity 0.3s ease-out;
            }
            .pdf-pages-wrapper.flipping-next {
                animation: flipNext 0.5s ease-in-out;
            }
            .pdf-pages-wrapper.flipping-prev {
                animation: flipPrev 0.5s ease-in-out;
            }
            @keyframes flipNext {
                0% { transform: translateX(0); opacity: 1; }
                40% { transform: translateX(-30px) scale(0.95); opacity: 0.5; }
                60% { transform: translateX(30px) scale(0.95); opacity: 0.5; }
                100% { transform: translateX(0); opacity: 1; }
            }
            @keyframes flipPrev {
                0% { transform: translateX(0); opacity: 1; }
                40% { transform: translateX(30px) scale(0.95); opacity: 0.5; }
                60% { transform: translateX(-30px) scale(0.95); opacity: 0.5; }
                100% { transform: translateX(0); opacity: 1; }
            }
            .pdf-pages-wrapper canvas {
                display: block;
                max-height: calc(100vh - 50px);
                width: auto;
                height: auto;
                box-shadow: 0 5px 30px rgba(0,0,0,0.5);
            }
            .pdf-pages-wrapper.spread-view {
                gap: 2px;
            }
            .pdf-pages-wrapper.spread-view canvas:first-child {
                box-shadow: -5px 5px 30px rgba(0,0,0,0.5);
            }
            .pdf-pages-wrapper.spread-view canvas:last-child {
                box-shadow: 5px 5px 30px rgba(0,0,0,0.5);
            }
            .pdf-controls {
                background: #111;
                padding: 8px 16px;
                display: flex;
                justify-content: center;
                gap: 8px;
                align-items: center;
                border-top: 1px solid #333;
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
            .pdf-nav-area {
                position: absolute;
                top: 0;
                bottom: 50px;
                width: 20%;
                cursor: pointer;
                z-index: 5;
                display: flex;
                align-items: center;
                justify-content: center;
                opacity: 0;
                transition: opacity 0.2s;
            }
            .pdf-nav-area:hover {
                opacity: 1;
                background: linear-gradient(to right, rgba(0,0,0,0.1), transparent);
            }
            .pdf-nav-area.next:hover {
                background: linear-gradient(to left, rgba(0,0,0,0.1), transparent);
            }
            .pdf-nav-area .nav-icon {
                font-size: 48px;
                color: rgba(255,255,255,0.5);
            }
            .pdf-nav-area.prev { left: 0; }
            .pdf-nav-area.next { right: 0; }
            
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
                .pdf-nav-area {
                    width: 25%;
                }
                .pdf-nav-area .nav-icon {
                    font-size: 32px;
                }
            }
        </style>';
    }
    
    return $script . '
    <div id="' . $viewer_id . '" class="pdf-viewer-container" style="height: ' . esc_attr($atts['height']) . ';">
        <div class="pdf-viewer-inner">
            <div class="pdf-book">
                <div class="pdf-pages-wrapper spread-view"></div>
            </div>
            <div class="pdf-controls">
                <button class="nav-btn prev-btn" onclick="pdfViewers[\'' . $viewer_id . '\'].prevPage()">‹</button>
                <button onclick="pdfViewers[\'' . $viewer_id . '\'].zoomOut()">−</button>
                <span class="page-info"><span class="current-page">1</span> / <span class="total-pages">?</span></span>
                <button onclick="pdfViewers[\'' . $viewer_id . '\'].zoomIn()">+</button>
                <button class="nav-btn next-btn" onclick="pdfViewers[\'' . $viewer_id . '\'].nextPage()">›</button>
                <button onclick="pdfViewers[\'' . $viewer_id . '\'].toggleSpread()" class="spread-btn">2x</button>
            </div>
        </div>
        <div class="pdf-nav-area prev" onclick="pdfViewers[\'' . $viewer_id . '\'].prevPage()">
            <span class="nav-icon">‹</span>
        </div>
        <div class="pdf-nav-area next" onclick="pdfViewers[\'' . $viewer_id . '\'].nextPage()">
            <span class="nav-icon">›</span>
        </div>
        <div class="pdf-loading">Cargando PDF...</div>
    </div>
    <script type="module">
        const pdfjsLib = await import("https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.9.155/pdf.min.mjs");
        pdfjsLib.GlobalWorkerOptions.workerSrc = "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.9.155/pdf.worker.min.mjs";
        
        window.pdfViewers = window.pdfViewers || {};
        
        const container = document.getElementById("' . $viewer_id . '");
        const wrapper = container.querySelector(".pdf-pages-wrapper");
        const currentPageSpan = container.querySelector(".current-page");
        const totalPagesSpan = container.querySelector(".total-pages");
        const spreadBtn = container.querySelector(".spread-btn");
        const prevBtn = container.querySelector(".prev-btn");
        const nextBtn = container.querySelector(".next-btn");
        const loading = container.querySelector(".pdf-loading");
        
        let pdf = null;
        let currentPage = 1;
        let isMobile = window.innerWidth <= 768;
        let spreadMode = !isMobile;
        let baseScale = 1;
        let userScale = 1;
        let isAnimating = false;
        
        async function loadPDF() {
            pdf = await pdfjsLib.getDocument("' . esc_url($pdf_url) . '").promise;
            totalPagesSpan.textContent = pdf.numPages;
            await calculateOptimalScale();
            await renderPages();
            loading.classList.add("hidden");
            updateButtons();
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
        
        function updateButtons() {
            const atStart = currentPage <= 1;
            const atEnd = spreadMode 
                ? currentPage + 1 >= pdf.numPages 
                : currentPage >= pdf.numPages;
            
            prevBtn.disabled = atStart;
            nextBtn.disabled = atEnd;
            spreadBtn.textContent = spreadMode ? "2x" : "1x";
        }
        
        async function renderPages(direction = null) {
            if (direction && !isAnimating) {
                isAnimating = true;
                wrapper.classList.add(direction === "next" ? "flipping-next" : "flipping-prev");
                
                await new Promise(r => setTimeout(r, 250));
                wrapper.innerHTML = "";
                await doRender();
                
                await new Promise(r => setTimeout(r, 250));
                wrapper.classList.remove("flipping-next", "flipping-prev");
                isAnimating = false;
            } else if (!direction) {
                wrapper.innerHTML = "";
                await doRender();
            }
            
            updateButtons();
        }
        
        async function doRender() {
            const scale = baseScale * userScale;
            
            if (spreadMode) {
                wrapper.classList.add("spread-view");
                const startPage = currentPage % 2 === 0 ? currentPage - 1 : currentPage;
                for (let i = startPage; i <= Math.min(startPage + 1, pdf.numPages); i++) {
                    await renderPage(i, scale);
                }
                currentPageSpan.textContent = startPage + "-" + Math.min(startPage + 1, pdf.numPages);
            } else {
                wrapper.classList.remove("spread-view");
                await renderPage(currentPage, scale);
                currentPageSpan.textContent = currentPage;
            }
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
        
        window.pdfViewers["' . $viewer_id . '"] = {
            prevPage() {
                if (isAnimating || currentPage <= 1) return;
                currentPage = spreadMode ? Math.max(1, currentPage - 2) : currentPage - 1;
                renderPages("prev");
            },
            nextPage() {
                if (isAnimating) return;
                const increment = spreadMode ? 2 : 1;
                if (currentPage + increment <= pdf.numPages) {
                    currentPage += increment;
                    renderPages("next");
                }
            },
            zoomIn() {
                userScale = Math.min(userScale + 0.15, 3);
                renderPages();
            },
            zoomOut() {
                userScale = Math.max(userScale - 0.15, 0.5);
                renderPages();
            },
            async toggleSpread() {
                if (isMobile) return;
                spreadMode = !spreadMode;
                await calculateOptimalScale();
                renderPages();
            }
        };
        
        document.addEventListener("keydown", (e) => {
            if (e.key === "ArrowLeft") pdfViewers["' . $viewer_id . '"].prevPage();
            if (e.key === "ArrowRight") pdfViewers["' . $viewer_id . '"].nextPage();
        });
        
        loadPDF();
    </script>';
});
