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
                overflow: auto;
                display: flex;
                flex-direction: column;
            }
            .pdf-pages-wrapper {
                flex: 1;
                display: flex;
                justify-content: center;
                align-items: center;
                gap: 0;
                padding: 0;
                min-height: 0;
            }
            .pdf-pages-wrapper canvas {
                display: block;
                max-height: calc(100vh - 50px);
                width: auto;
                height: auto;
            }
            .pdf-pages-wrapper.spread-view {
                gap: 2px;
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
            }
            .pdf-nav-area.prev { left: 0; }
            .pdf-nav-area.next { right: 0; }
            .pdf-nav-area:hover { background: rgba(255,255,255,0.02); }
            
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
                    width: 30%;
                }
            }
        </style>';
    }
    
    return $script . '
    <div id="' . $viewer_id . '" class="pdf-viewer-container" style="height: ' . esc_attr($atts['height']) . ';">
        <div class="pdf-viewer-inner">
            <div class="pdf-pages-wrapper spread-view"></div>
            <div class="pdf-controls">
                <button class="nav-btn" onclick="pdfViewers[\'' . $viewer_id . '\'].prevPage()">‹</button>
                <button onclick="pdfViewers[\'' . $viewer_id . '\'].zoomOut()">−</button>
                <span class="page-info"><span class="current-page">1</span> / <span class="total-pages">?</span></span>
                <button onclick="pdfViewers[\'' . $viewer_id . '\'].zoomIn()">+</button>
                <button class="nav-btn" onclick="pdfViewers[\'' . $viewer_id . '\'].nextPage()">›</button>
                <button onclick="pdfViewers[\'' . $viewer_id . '\'].toggleSpread()" class="spread-btn">1x</button>
            </div>
        </div>
        <div class="pdf-nav-area prev" onclick="pdfViewers[\'' . $viewer_id . '\'].prevPage()"></div>
        <div class="pdf-nav-area next" onclick="pdfViewers[\'' . $viewer_id . '\'].nextPage()"></div>
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
        
        let pdf = null;
        let currentPage = 1;
        let isMobile = window.innerWidth <= 768;
        let spreadMode = !isMobile; // Vista doble por defecto en desktop
        let baseScale = 1;
        let userScale = 1;
        
        async function loadPDF() {
            pdf = await pdfjsLib.getDocument("' . esc_url($pdf_url) . '").promise;
            totalPagesSpan.textContent = pdf.numPages;
            await calculateOptimalScale();
            renderPages();
        }
        
        async function calculateOptimalScale() {
            const page = await pdf.getPage(1);
            const viewport = page.getViewport({ scale: 1 });
            const containerHeight = container.clientHeight - 50; // menos controles
            const containerWidth = container.clientWidth;
            
            if (spreadMode) {
                // Dos páginas lado a lado
                baseScale = Math.min(
                    containerHeight / viewport.height,
                    (containerWidth / 2) / viewport.width
                ) * 0.95;
            } else {
                baseScale = Math.min(
                    containerHeight / viewport.height,
                    containerWidth / viewport.width
                ) * 0.95;
            }
        }
        
        async function renderPages() {
            wrapper.innerHTML = "";
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
            
            spreadBtn.textContent = spreadMode ? "2x" : "1x";
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
                if (currentPage > 1) {
                    currentPage = spreadMode ? Math.max(1, currentPage - 2) : currentPage - 1;
                    renderPages();
                }
            },
            nextPage() {
                const increment = spreadMode ? 2 : 1;
                if (currentPage + increment <= pdf.numPages) {
                    currentPage += increment;
                    renderPages();
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
        
        // Keyboard navigation
        document.addEventListener("keydown", (e) => {
            if (e.key === "ArrowLeft") pdfViewers["' . $viewer_id . '"].prevPage();
            if (e.key === "ArrowRight") pdfViewers["' . $viewer_id . '"].nextPage();
        });
        
        loadPDF();
    </script>';
});
