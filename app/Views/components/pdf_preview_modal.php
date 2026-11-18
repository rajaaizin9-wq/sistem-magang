<!-- Global PDF Preview Modal -->
<div id="globalPdfModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="globalPdfTitle" role="dialog" aria-modal="true">
    <!-- Backdrop with blur -->
    <div id="globalPdfBackdrop" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>

    <div class="flex min-h-screen items-center justify-center p-2 sm:p-4 text-center">
        <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all w-full max-w-5xl flex flex-col h-[90vh] border border-slate-200">
            <!-- Modal Header -->
            <div class="flex items-center justify-between px-5 py-3.5 bg-slate-50 border-b border-slate-200 flex-shrink-0">
                <div class="flex items-center space-x-2.5 min-w-0">
                    <span class="w-8 h-8 rounded-lg bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>
                    </span>
                    <div class="min-w-0">
                        <h3 class="text-sm font-bold text-slate-800 truncate" id="globalPdfTitle">Pratinjau Dokumen PDF</h3>
                        <p class="text-[11px] text-slate-500 truncate" id="globalPdfSubtitle">Memuat dokumen...</p>
                    </div>
                </div>

                <div class="flex items-center space-x-2 flex-shrink-0">
                    <!-- Buka di Tab Baru / Unduh -->
                    <a id="globalPdfNewTab" href="#" target="_blank" rel="noopener noreferrer" class="inline-flex items-center space-x-1.5 px-3 py-1.5 text-xs font-semibold text-slate-700 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 shadow-sm transition" title="Buka di tab baru">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        <span class="hidden sm:inline">Tab Baru</span>
                    </a>
                    
                    <!-- Close button -->
                    <button type="button" onclick="closePdfModal()" class="p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-200/60 rounded-lg transition" title="Tutup (Esc)">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            <!-- Modal Body (Iframe) with Loading Indicator -->
            <div class="relative flex-1 w-full bg-slate-100 overflow-hidden">
                <div id="globalPdfLoader" class="absolute inset-0 flex flex-col items-center justify-center bg-slate-50 z-10 transition-opacity">
                    <svg class="w-8 h-8 text-blue-600 animate-spin mb-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                    <p class="text-xs font-medium text-slate-600">Memuat berkas PDF...</p>
                </div>

                <iframe id="globalPdfIframe" src="about:blank" class="w-full h-full border-0" frameborder="0"></iframe>
            </div>
        </div>
    </div>
</div>

<script>
    function openPdfModal(url, title = 'Berkas PDF', subtitle = '') {
        const modal = document.getElementById('globalPdfModal');
        const iframe = document.getElementById('globalPdfIframe');
        const titleEl = document.getElementById('globalPdfTitle');
        const subEl = document.getElementById('globalPdfSubtitle');
        const newTabEl = document.getElementById('globalPdfNewTab');
        const loader = document.getElementById('globalPdfLoader');

        if (!modal || !iframe) return;

        if (titleEl) titleEl.textContent = title;
        if (subEl) subEl.textContent = subtitle || 'Dokumen PDF Sistem SIMAGANG';
        if (newTabEl) newTabEl.href = url;

        if (loader) loader.classList.remove('hidden');

        iframe.onload = function() {
            if (loader) loader.classList.add('hidden');
        };
        iframe.src = url;

        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closePdfModal() {
        const modal = document.getElementById('globalPdfModal');
        const iframe = document.getElementById('globalPdfIframe');
        const loader = document.getElementById('globalPdfLoader');

        if (!modal) return;

        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');

        if (iframe) iframe.src = 'about:blank';
        if (loader) loader.classList.remove('hidden');
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const modal = document.getElementById('globalPdfModal');
            if (modal && !modal.classList.contains('hidden')) {
                closePdfModal();
            }
        }
    });

    const pdfBackdrop = document.getElementById('globalPdfBackdrop');
    if (pdfBackdrop) {
        pdfBackdrop.addEventListener('click', closePdfModal);
    }
</script>

