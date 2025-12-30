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
                background: #333;
                overflow: auto;
            }
            .pdf-viewer-container canvas {
                display: block;
                margin: 10px auto;
                box-shadow: 0 2px 10px rgba(0,0,0,0.3);
            }
            .pdf-controls {
                background: #404040;
                padding: 10px;
                display: flex;
                justify-content: center;
                gap: 10px;
                align-items: center;
                position: sticky;
                top: 0;
                z-index: 10;
            }
            .pdf-controls button {
                background: #606060;
                color: white;
                border: none;
                padding: 8px 16px;
                border-radius: 4px;
                cursor: pointer;
            }
            .pdf-controls button:hover {
                background: #707070;
            }
            .pdf-controls span {
                color: white;
            }
            .pdf-pages-wrapper {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                gap: 20px;
                padding: 20px;
            }
            .pdf-pages-wrapper.spread-view {
                flex-wrap: nowrap;
            }
        </style>';
    }
    
    return $script . '
    <div id="' . $viewer_id . '" class="pdf-viewer-container border border-gray-300 rounded-lg shadow-sm my-8" style="height: ' . esc_attr($atts['height']) . ';">
        <div class="pdf-controls">
            <button onclick="pdfViewers[\'' . $viewer_id . '\'].prevPage()">← Anterior</button>
            <span class="pdf-page-info">Página <span class="current-page">1</span> de <span class="total-pages">?</span></span>
            <button onclick="pdfViewers[\'' . $viewer_id . '\'].nextPage()">Siguiente →</button>
            <button onclick="pdfViewers[\'' . $viewer_id . '\'].zoomOut()">−</button>
            <button onclick="pdfViewers[\'' . $viewer_id . '\'].zoomIn()">+</button>
            <button onclick="pdfViewers[\'' . $viewer_id . '\'].toggleSpread()">Vista doble</button>
        </div>
        <div class="pdf-pages-wrapper"></div>
    </div>
    <script type="module">
        const pdfjsLib = await import("https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.9.155/pdf.min.mjs");
        pdfjsLib.GlobalWorkerOptions.workerSrc = "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.9.155/pdf.worker.min.mjs";
        
        window.pdfViewers = window.pdfViewers || {};
        
        const container = document.getElementById("' . $viewer_id . '");
        const wrapper = container.querySelector(".pdf-pages-wrapper");
        const currentPageSpan = container.querySelector(".current-page");
        const totalPagesSpan = container.querySelector(".total-pages");
        
        let pdf = null;
        let currentPage = 1;
        let scale = 1.2;
        let spreadMode = false;
        
        async function loadPDF() {
            pdf = await pdfjsLib.getDocument("' . esc_url($pdf_url) . '").promise;
            totalPagesSpan.textContent = pdf.numPages;
            renderPages();
        }
        
        async function renderPages() {
            wrapper.innerHTML = "";
            
            if (spreadMode) {
                wrapper.classList.add("spread-view");
                // En modo spread, mostrar página actual y siguiente
                const startPage = currentPage % 2 === 0 ? currentPage - 1 : currentPage;
                for (let i = startPage; i <= Math.min(startPage + 1, pdf.numPages); i++) {
                    await renderPage(i);
                }
            } else {
                wrapper.classList.remove("spread-view");
                await renderPage(currentPage);
            }
            
            currentPageSpan.textContent = currentPage;
        }
        
        async function renderPage(pageNum) {
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
                if (currentPage < pdf.numPages) {
                    currentPage = spreadMode ? Math.min(pdf.numPages, currentPage + 2) : currentPage + 1;
                    renderPages();
                }
            },
            zoomIn() {
                scale += 0.2;
                renderPages();
            },
            zoomOut() {
                if (scale > 0.4) {
                    scale -= 0.2;
                    renderPages();
                }
            },
            toggleSpread() {
                spreadMode = !spreadMode;
                renderPages();
            }
        };
        
        loadPDF();
    </script>';
});
