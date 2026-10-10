import './bootstrap';
import Chart from 'chart.js/auto';
import * as htmlToImage from 'html-to-image';

window.Chart = Chart;
window.htmlToImage = htmlToImage;

// Global Chart.js Font: Force Urbanist everywhere in charts
Chart.defaults.font.family = "'Urbanist', system-ui, -apple-system, BlinkMacSystemFont, sans-serif";
Chart.defaults.font.weight = 500;
Chart.defaults.color = '#163144';
Chart.defaults.plugins.tooltip.titleFont = { family: "'Urbanist'", weight: 700, size: 13 };
Chart.defaults.plugins.tooltip.bodyFont = { family: "'Urbanist'", weight: 500, size: 12 };
Chart.defaults.plugins.legend.labels.font = { family: "'Urbanist'", weight: 600, size: 12 };
Chart.defaults.scale = Chart.defaults.scale || {};
if (Chart.defaults.scales) {
    ['linear', 'category', 'time'].forEach(scaleType => {
        if (Chart.defaults.scales[scaleType]) {
            Chart.defaults.scales[scaleType].ticks = Chart.defaults.scales[scaleType].ticks || {};
            Chart.defaults.scales[scaleType].ticks.font = { family: "'Urbanist'", weight: 500, size: 11 };
        }
    });
}

// Global Searchable Select Component for Alpine
function defineSearchableSelect() {
    if (!window.Alpine) return;

    window.Alpine.data('searchableSelect', (config) => ({
        open: false,
        search: '',
        value: config.initialValue ?? '',
        options: config.options || [],
        placeholder: config.placeholder || 'Pilih opsi...',
        threshold: config.threshold ?? 0,
        wireProp: config.wireProp || null,
        isLive: config.isLive ?? false,
        highlightedIndex: 0,

        getWireValue(path) {
            if (!path || !this.$wire) return undefined;
            try {
                if (typeof this.$wire.get === 'function') {
                    const direct = this.$wire.get(path);
                    if (direct !== undefined) return direct;
                }
            } catch(e) {}
            const parts = path.split('.');
            let current = typeof this.$wire.get === 'function' ? this.$wire.get(parts[0]) : this.$wire[parts[0]];
            for (let i = 1; i < parts.length; i++) {
                if (current === undefined || current === null) return undefined;
                current = current[parts[i]];
            }
            return current;
        },

        init() {
            if (this.wireProp && this.$wire) {
                const livewireVal = this.getWireValue(this.wireProp);
                if (livewireVal !== undefined && livewireVal !== null) {
                    this.value = livewireVal;
                }

                this.$watch('$wire.' + this.wireProp, (newVal) => {
                    this.value = newVal ?? '';
                });

                if (this.wireProp.includes('.')) {
                    const rootProp = this.wireProp.split('.')[0];
                    this.$watch('$wire.' + rootProp, () => {
                        const updatedVal = this.getWireValue(this.wireProp);
                        if (updatedVal !== undefined && updatedVal !== null) {
                            this.value = updatedVal;
                        }
                    });
                }
            }
        },

        get shouldShowSearch() {
            if (this.threshold === 0) return true;
            const thresh = this.threshold !== undefined && this.threshold !== null ? parseInt(this.threshold) : 6;
            return this.options.length > thresh;
        },

        get filteredOptions() {
            if (!this.search || !this.search.trim()) {
                return this.options;
            }
            const q = this.search.toLowerCase().trim();
            return this.options.filter(opt => {
                const label = String(opt.label || '').toLowerCase();
                const sublabel = String(opt.sublabel || '').toLowerCase();
                const value = String(opt.value || '').toLowerCase();
                return label.includes(q) || sublabel.includes(q) || value.includes(q);
            });
        },

        get selectedOption() {
            return this.options.find(opt => String(opt.value) === String(this.value));
        },

        get selectedLabel() {
            const opt = this.selectedOption;
            return opt ? opt.label : this.placeholder;
        },

        get selectedSublabel() {
            const opt = this.selectedOption;
            return opt ? (opt.sublabel || '') : '';
        },

        toggle() {
            if (this.open) {
                this.close();
            } else {
                this.open = true;
                this.search = '';
                this.highlightedIndex = 0;
                this.$nextTick(() => {
                    if (this.$refs.searchInput) {
                        this.$refs.searchInput.focus();
                    }
                });
            }
        },

        close() {
            this.open = false;
            this.search = '';
        },

        select(val) {
            this.value = val;
            if (this.wireProp && this.$wire) {
                if (this.isLive) {
                    this.$wire.set(this.wireProp, val, true);
                } else {
                    this.$wire.set(this.wireProp, val, false);
                }
            }
            this.close();
            this.$dispatch('input', val);
            this.$dispatch('change', val);
        },

        onKeyDown(e) {
            if (!this.open) {
                if (e.key === 'Enter' || e.key === 'ArrowDown' || e.key === ' ') {
                    e.preventDefault();
                    this.toggle();
                }
                return;
            }

            const items = this.filteredOptions;
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                this.highlightedIndex = (this.highlightedIndex + 1) % (items.length || 1);
                this.scrollIntoView();
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                this.highlightedIndex = (this.highlightedIndex - 1 + items.length) % (items.length || 1);
                this.scrollIntoView();
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (items[this.highlightedIndex]) {
                    this.select(items[this.highlightedIndex].value);
                }
            } else if (e.key === 'Escape') {
                e.preventDefault();
                this.close();
            }
        },

        scrollIntoView() {
            this.$nextTick(() => {
                const list = this.$refs.optionsList;
                if (list && list.children && list.children[this.highlightedIndex]) {
                    list.children[this.highlightedIndex].scrollIntoView({ block: 'nearest' });
                }
            });
        }
    }));

    // Alpine component for Sales Report Charts
    window.Alpine.data('salesReportCharts', () => ({
        trendChartInstance: null,
        paymentChartInstance: null,
        topProductChartInstance: null,

        init() {
            this.$nextTick(() => {
                this.renderAllCharts();
            });

            window.addEventListener('update-charts', () => {
                this.$nextTick(() => {
                    this.renderAllCharts();
                });
            });
        },

        renderAllCharts() {
            this.renderTrendChart();
            this.renderPaymentChart();
            this.renderTopProductChart();
        },

        renderTrendChart() {
            const canvas = this.$refs.trendCanvas;
            if (!canvas || !window.Chart) return;

            const labels = JSON.parse(canvas.dataset.labels || '[]');
            const omset = JSON.parse(canvas.dataset.omset || '[]');
            const profit = JSON.parse(canvas.dataset.profit || '[]');

            if (this.trendChartInstance) {
                this.trendChartInstance.destroy();
            }

            const ctx = canvas.getContext('2d');
            this.trendChartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Omset Penjualan (Rp)',
                            data: omset,
                            borderColor: '#163144',
                            backgroundColor: 'rgba(160, 189, 219, 0.25)',
                            borderWidth: 2.5,
                            fill: true,
                            tension: 0.35,
                            pointRadius: labels.length > 20 ? 1.5 : 4,
                            pointHoverRadius: 6,
                            pointBackgroundColor: '#163144',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 1.5,
                        },
                        {
                            label: 'Laba Bersih (Rp)',
                            data: profit,
                            borderColor: '#386797',
                            backgroundColor: 'rgba(56, 103, 151, 0.08)',
                            borderWidth: 2,
                            borderDash: [4, 4],
                            fill: true,
                            tension: 0.35,
                            pointRadius: labels.length > 20 ? 1.5 : 3.5,
                            pointHoverRadius: 5.5,
                            pointBackgroundColor: '#386797',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 1.5,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: { family: "'Urbanist', sans-serif", size: 11, weight: '700' },
                                color: '#163144',
                                boxWidth: 12,
                                padding: 12,
                            }
                        },
                        tooltip: {
                            backgroundColor: '#163144',
                            titleFont: { family: "'Urbanist', sans-serif", size: 12, weight: 'bold' },
                            bodyFont: { family: "'Urbanist', sans-serif", size: 11, weight: '600' },
                            padding: 10,
                            cornerRadius: 12,
                            callbacks: {
                                label: function(context) {
                                    const val = context.raw || 0;
                                    return ` ${context.dataset.label}: Rp ${new Intl.NumberFormat('id-ID').format(val)}`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: {
                                font: { family: "'Urbanist', sans-serif", size: 10, weight: '600' },
                                color: '#94a3b8',
                                maxRotation: 45,
                                autoSkip: true,
                                maxTicksLimit: 14
                            }
                        },
                        y: {
                            border: { dash: [4, 4] },
                            grid: { color: 'rgba(226, 237, 246, 0.6)' },
                            ticks: {
                                font: { family: "'Urbanist', sans-serif", size: 10, weight: '600' },
                                color: '#94a3b8',
                                callback: function(value) {
                                    if (value >= 1000000) return (value / 1000000).toFixed(1) + 'M';
                                    if (value >= 1000) return (value / 1000).toFixed(0) + 'K';
                                    return value;
                                }
                            }
                        }
                    }
                }
            });
        },

        renderPaymentChart() {
            const canvas = this.$refs.paymentCanvas;
            if (!canvas || !window.Chart) return;

            const labels = JSON.parse(canvas.dataset.labels || '[]');
            const values = JSON.parse(canvas.dataset.values || '[]').map(Number);

            if (this.paymentChartInstance) {
                this.paymentChartInstance.destroy();
            }

            const ctx = canvas.getContext('2d');
            const total = values.reduce((a, b) => a + b, 0);

            this.paymentChartInstance = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: total === 0 ? [1] : values,
                        backgroundColor: total === 0 ? ['#e2e8f0'] : ['#163144', '#386797', '#a0bddb', '#cbd5e1'],
                        borderWidth: 2,
                        borderColor: '#ffffff',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: { family: "'Urbanist', sans-serif", size: 10, weight: '700' },
                                color: '#163144',
                                boxWidth: 10,
                                padding: 10,
                            }
                        },
                        tooltip: {
                            backgroundColor: '#163144',
                            titleFont: { family: "'Urbanist', sans-serif", size: 11, weight: 'bold' },
                            bodyFont: { family: "'Urbanist', sans-serif", size: 10, weight: '600' },
                            padding: 8,
                            cornerRadius: 12,
                            callbacks: {
                                label: function(context) {
                                    if (total === 0) return ' Belum ada data';
                                    const val = context.raw || 0;
                                    const pct = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                                    return ` Rp ${new Intl.NumberFormat('id-ID').format(val)} (${pct}%)`;
                                }
                            }
                        }
                    }
                }
            });
        },

        renderTopProductChart() {
            const canvas = this.$refs.topProductCanvas;
            if (!canvas || !window.Chart) return;

            const labels = JSON.parse(canvas.dataset.labels || '[]');
            const qty = JSON.parse(canvas.dataset.qty || '[]');

            if (this.topProductChartInstance) {
                this.topProductChartInstance.destroy();
            }

            const ctx = canvas.getContext('2d');
            this.topProductChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total Terjual (Unit)',
                        data: qty,
                        backgroundColor: '#163144',
                        hoverBackgroundColor: '#244262',
                        borderRadius: 8,
                        borderSkipped: false,
                        maxBarThickness: 22,
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#163144',
                            titleFont: { family: "'Urbanist', sans-serif", size: 11, weight: 'bold' },
                            bodyFont: { family: "'Urbanist', sans-serif", size: 10, weight: '600' },
                            padding: 8,
                            cornerRadius: 12,
                        }
                    },
                    scales: {
                        x: {
                            grid: { color: 'rgba(226, 237, 246, 0.6)' },
                            ticks: {
                                font: { family: "'Urbanist', sans-serif", size: 10, weight: '600' },
                                color: '#94a3b8',
                                precision: 0
                            }
                        },
                        y: {
                            grid: { display: false },
                            ticks: {
                                font: { family: "'Urbanist', sans-serif", size: 10, weight: '700' },
                                color: '#163144'
                            }
                        }
                    }
                }
            });
        }
    }));

    // Alpine component for Dashboard Analytics Charts
    window.Alpine.data('dashboardAnalytics', () => ({
        trendChart: null,
        paymentChart: null,

        init() {
            this.$nextTick(() => {
                this.renderCharts();
            });

            this._updateHandler = () => {
                this.$nextTick(() => {
                    this.renderCharts();
                });
            };
            window.addEventListener('update-dashboard-charts', this._updateHandler);
        },

        destroy() {
            if (this.trendChart) {
                this.trendChart.destroy();
                this.trendChart = null;
            }
            if (this.paymentChart) {
                this.paymentChart.destroy();
                this.paymentChart = null;
            }
            if (this._updateHandler) {
                window.removeEventListener('update-dashboard-charts', this._updateHandler);
            }
        },

        renderCharts() {
            this.renderTrend();
            this.renderPayment();
        },

        renderTrend() {
            const canvas = this.$refs.trendCanvas;
            if (!canvas || !window.Chart) return;

            let labels = [];
            let omset = [];
            let profit = [];

            try {
                labels = JSON.parse(canvas.dataset.labels || '[]');
                omset = JSON.parse(canvas.dataset.omset || '[]');
                profit = JSON.parse(canvas.dataset.profit || '[]');
            } catch(e) {
                console.error('Error parsing trend chart data:', e);
            }

            if (this.trendChart) {
                this.trendChart.destroy();
                this.trendChart = null;
            }

            const ctx = canvas.getContext('2d');
            if (!ctx) return;

            this.trendChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Omset Penjualan (Rp)',
                            data: omset,
                            borderColor: '#163144',
                            backgroundColor: 'rgba(160, 189, 219, 0.18)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            pointBackgroundColor: '#163144',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 1.5,
                        },
                        {
                            label: 'Estimasi Laba (Rp)',
                            data: profit,
                            borderColor: '#5080b0',
                            backgroundColor: 'rgba(80, 128, 176, 0.05)',
                            borderWidth: 1.5,
                            borderDash: [4, 4],
                            fill: true,
                            tension: 0.4,
                            pointRadius: 3,
                            pointHoverRadius: 5,
                            pointBackgroundColor: '#5080b0',
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            display: false,
                        },
                        tooltip: {
                            backgroundColor: '#163144',
                            titleFont: { family: "'Urbanist', sans-serif", size: 12, weight: 'bold' },
                            bodyFont: { family: "'Urbanist', sans-serif", size: 11, weight: '600' },
                            padding: 12,
                            cornerRadius: 12,
                            boxPadding: 4,
                            callbacks: {
                                label: function(context) {
                                    const val = context.raw || 0;
                                    return ` ${context.dataset.label}: Rp ${new Intl.NumberFormat('id-ID').format(val)}`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: {
                                font: { family: "'Urbanist', sans-serif", size: 11, weight: '600' },
                                color: '#94a3b8',
                                maxRotation: 0,
                                autoSkip: true,
                                maxTicksLimit: 8
                            }
                        },
                        y: {
                            position: 'right',
                            border: { dash: [4, 4], display: false },
                            grid: { color: 'rgba(226, 237, 246, 0.6)' },
                            ticks: {
                                font: { family: "'Urbanist', sans-serif", size: 10, weight: '600' },
                                color: '#94a3b8',
                                callback: function(value) {
                                    if (value >= 1000000) return (value / 1000000).toFixed(1) + 'M';
                                    if (value >= 1000) return (value / 1000).toFixed(0) + 'K';
                                    return value;
                                }
                            }
                        }
                    }
                }
            });
        },

        renderPayment() {
            const canvas = this.$refs.paymentCanvas;
            if (!canvas || !window.Chart) return;

            let labels = [];
            let values = [];

            try {
                labels = JSON.parse(canvas.dataset.labels || '[]');
                values = JSON.parse(canvas.dataset.values || '[]').map(Number);
            } catch(e) {
                console.error('Error parsing payment chart data:', e);
            }

            if (this.paymentChart) {
                this.paymentChart.destroy();
                this.paymentChart = null;
            }

            const ctx = canvas.getContext('2d');
            if (!ctx) return;

            const total = values.reduce((a, b) => a + b, 0);

            this.paymentChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: total === 0 ? [1] : values,
                        backgroundColor: total === 0 ? ['#e2e8f0'] : ['#2563eb', '#06b6d4', '#8b5cf6', '#f59e0b'],
                        borderWidth: 2,
                        borderColor: '#ffffff',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '68%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: { family: "'Plus Jakarta Sans', sans-serif", size: 10, weight: '600' },
                                boxWidth: 10,
                                padding: 8,
                            }
                        },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            titleFont: { family: "'Plus Jakarta Sans', sans-serif", size: 11, weight: 'bold' },
                            bodyFont: { family: "'JetBrains Mono', monospace", size: 10 },
                            padding: 8,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    if (total === 0) return ' Belum ada data';
                                    const val = context.raw || 0;
                                    const pct = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                                    return ` Rp ${new Intl.NumberFormat('id-ID').format(val)} (${pct}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }
    }));

}

function registerAllComponents() {
    defineSearchableSelect();
}

if (window.Alpine) {
    registerAllComponents();
} else {
    document.addEventListener('alpine:init', registerAllComponents);
}
document.addEventListener('livewire:navigated', registerAllComponents);

// WhatsApp Image Receipt Sharing Function using html-to-image (full oklch & CSS support)
window.sendReceiptToWhatsAppImage = async function(receiptElementId, rawPhoneNumber, invoiceNumber) {
    const el = document.getElementById(receiptElementId);
    if (!el) {
        alert('Elemen nota tidak ditemukan.');
        return;
    }

    try {
        const blob = await htmlToImage.toBlob(el, {
            quality: 0.98,
            backgroundColor: '#ffffff',
            pixelRatio: 2.5,
            cacheBust: true,
        });

        if (!blob) {
            alert('Gagal menghasilkan gambar nota.');
            return;
        }

        let phone = (rawPhoneNumber || '').replace(/[^0-9]/g, '');
        if (phone.startsWith('0')) {
            phone = '62' + phone.substring(1);
        }

        const fileName = `Nota-${invoiceNumber || 'Transaksi'}.png`;
        const file = new File([blob], fileName, { type: 'image/png' });

        // 1. Mobile Web Share API (Android / iOS)
        if (navigator.canShare && navigator.canShare({ files: [file] })) {
            try {
                await navigator.share({
                    files: [file],
                    title: `Nota ${invoiceNumber} - Syafa Frozen Food`,
                    text: `Nota Pembelian ${invoiceNumber} di Syafa Frozen Food.`
                });
                return;
            } catch (shareErr) {
                if (shareErr.name === 'AbortError') return;
            }
        }

        // 2. Desktop: Copy image to clipboard & open WhatsApp
        let isCopied = false;
        try {
            if (navigator.clipboard && window.ClipboardItem) {
                await navigator.clipboard.write([
                    new ClipboardItem({ 'image/png': blob })
                ]);
                isCopied = true;
            }
        } catch (clipErr) {
            console.warn('Clipboard write failed:', clipErr);
        }

        // Download receipt image as well
        const dataUrl = await htmlToImage.toPng(el, {
            quality: 0.98,
            backgroundColor: '#ffffff',
            pixelRatio: 2.5,
        });
        const link = document.createElement('a');
        link.download = fileName;
        link.href = dataUrl;
        link.click();

        // Open WhatsApp Web or App
        const waUrl = phone ? `https://wa.me/${phone}` : `https://web.whatsapp.com`;
        window.open(waUrl, '_blank');

        if (isCopied) {
            alert(`Gambar Nota (${fileName}) telah otomatis disalin ke clipboard dan diunduh!\n\nSilakan tekan Ctrl+V (Tempel) di chat WhatsApp untuk mengirim gambar nota.`);
        } else {
            alert(`Gambar Nota (${fileName}) telah berhasil diunduh!\n\nSilakan lampirkan gambar nota tersebut di chat WhatsApp.`);
        }

    } catch (err) {
        alert('Terjadi kesalahan saat memproses gambar nota: ' + err.message);
    }
};

window.downloadReceiptImage = async function(receiptElementId, invoiceNumber) {
    const el = document.getElementById(receiptElementId);
    if (!el) return;

    try {
        const dataUrl = await htmlToImage.toPng(el, {
            quality: 0.98,
            backgroundColor: '#ffffff',
            pixelRatio: 2.5,
        });

        const link = document.createElement('a');
        link.download = `Nota-${invoiceNumber || 'Transaksi'}.png`;
        link.href = dataUrl;
        link.click();
    } catch (e) {
        alert('Gagal mengunduh gambar nota: ' + e.message);
    }
};

// Thermal Receipt Direct Printing Helper (Supports 58mm & 80mm)
window.printThermalReceipt = function(receiptElementId, size = '58mm') {
    const el = document.getElementById(receiptElementId);
    if (!el) {
        window.print();
        return;
    }

    const is80 = (size === '80mm');
    const paperWidth = is80 ? '80mm' : '58mm';
    const printableWidth = is80 ? '68mm' : '42mm';
    const baseFontSize = is80 ? '11px' : '9.5px';
    const smallFontSize = is80 ? '9.5px' : '8.5px';
    const headerFontSize = is80 ? '13px' : '11.5px';

    const printWin = window.open('', '_blank', 'width=450,height=700,menubar=no,toolbar=no,location=no,status=no');
    if (!printWin) {
        // Popups blocked, fallback to standard window.print
        document.documentElement.style.setProperty('--print-receipt-width', printableWidth);
        window.print();
        return;
    }

    const receiptHtml = el.innerHTML;

    printWin.document.write(`<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Cetak Struk - ${paperWidth}</title>
    <style>
        @page {
            size: ${paperWidth} auto;
            margin: 0mm !important;
        }
        * {
            box-sizing: border-box !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        html, body {
            width: 100% !important;
            max-width: ${paperWidth} !important;
            margin: 0 !important;
            padding: 0 !important;
            background: #ffffff !important;
            color: #000000 !important;
            font-family: Arial, Helvetica, sans-serif !important;
            font-size: ${baseFontSize} !important;
            line-height: 1.25 !important;
            -webkit-font-smoothing: antialiased !important;
        }
        .thermal-sheet {
            width: ${printableWidth} !important;
            max-width: ${printableWidth} !important;
            padding: 0 !important;
            margin: 0 auto 0 1mm !important;
            overflow: hidden !important;
        }
        .text-center { text-align: center !important; }
        .text-right { text-align: right !important; }
        .text-left { text-align: left !important; }
        .font-bold, .font-black, strong, b { 
            font-weight: bold !important; 
            color: #000000 !important;
        }
        h1, h2, h3, h4, h5 {
            font-weight: bold !important;
            color: #000000 !important;
            margin: 0 0 1px 0 !important;
        }
        .font-mono { font-family: Arial, Helvetica, sans-serif !important; }
        .border-dashed { border-style: dashed !important; }
        .border-b { 
            border-bottom: 1px dashed #000000 !important; 
            border-top: none !important;
            border-left: none !important;
            border-right: none !important;
            height: 0 !important;
            margin-top: 3px !important;
            margin-bottom: 3px !important;
        }
        .border-t { 
            border-top: 1px dashed #000000 !important; 
            border-bottom: none !important;
            border-left: none !important;
            border-right: none !important;
            height: 0 !important;
            margin-top: 3px !important;
            margin-bottom: 3px !important;
        }
        .my-1\\.5, .my-2 { margin-top: 3px !important; margin-bottom: 3px !important; }
        .mb-0\\.5 { margin-bottom: 1px !important; }
        .mb-1, .mb-1\\.5, .mb-2 { margin-bottom: 2px !important; }
        .flex { 
            display: flex !important; 
            width: 100% !important;
        }
        .justify-between { 
            justify-content: space-between !important; 
            width: 100% !important;
        }
        .items-center { align-items: center !important; }
        .items-start { align-items: flex-start !important; }
        .space-y-0\\.5 > * + * { margin-top: 1px !important; }
        .space-y-1 > * + * { margin-top: 1.5px !important; }
        .space-y-1\\.5 > * + * { margin-top: 2px !important; }
        .space-y-2 > * + * { margin-top: 2.5px !important; }
        .space-y-3 > * + * { margin-top: 3.5px !important; }
        .py-1 { padding-top: 1.5px !important; padding-bottom: 1.5px !important; }
        .py-2 { padding-top: 2.5px !important; padding-bottom: 2.5px !important; }
        .py-2\\.5 { padding-top: 3px !important; padding-bottom: 3px !important; }
        .pb-2 { padding-bottom: 2px !important; }
        .pb-3 { padding-bottom: 3px !important; }
        .pt-0\\.5 { padding-top: 1px !important; }
        .pt-1 { padding-top: 1.5px !important; }
        .pt-2 { padding-top: 2.5px !important; }
        .text-xs { font-size: ${baseFontSize} !important; }
        .text-\\[9px\\], .text-\\[9\\.5px\\], .text-\\[10px\\], .text-\\[11px\\], .text-slate-400, .text-slate-500, .text-slate-600, .text-neutral-700, .text-neutral-800 {
            font-size: ${smallFontSize} !important;
            color: #000000 !important;
        }
        .text-sm, .text-base, h3 { font-size: ${headerFontSize} !important; }
        .text-rose-600, .text-red-600, .text-emerald-700, .text-blue-600, .text-blue-700, .text-amber-800 {
            color: #000000 !important;
        }
        .break-all { word-break: break-all !important; }
        .truncate {
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            white-space: normal !important;
            word-break: break-word !important;
        }
        button, .no-print { display: none !important; }
    </style>
</head>
<body>
    <div class="thermal-sheet">
        ${receiptHtml}
    </div>
    <script>
        window.onload = function() {
            window.focus();
            window.print();
            setTimeout(function() {
                window.close();
            }, 500);
        };
    <\/script>
</body>
</html>`);

    printWin.document.close();
};

