import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

const statusClasses = {
    antrean: {
        Dipanggil: ['clinic-badge-info', 'bg-sky-500'],
        Menunggu: ['clinic-badge-warning', 'bg-amber-500'],
        Selesai: ['clinic-badge-success', 'bg-emerald-500'],
        Batal: ['clinic-badge-muted', 'bg-slate-400'],
        default: ['clinic-badge-muted', 'bg-slate-400'],
    },
};

function updateText(id, value) {
    const element = document.getElementById(id);

    if (element) {
        element.textContent = value;
    }
}

function escapeHtml(value) {
    const element = document.createElement('textarea');
    element.textContent = value ?? '';

    return element.innerHTML;
}

function initQueuePreview() {
    const root = document.querySelector('[data-queue-preview-url]');

    if (!root) {
        return;
    }

    async function refreshQueuePreview() {
        try {
            const response = await fetch(root.dataset.queuePreviewUrl, {
                headers: { Accept: 'application/json' },
            });

            if (!response.ok) {
                return;
            }

            const data = await response.json();
            const status = data.status || 'Belum Ada';
            const [badgeClass, dotClass] = statusClasses.antrean[status] || statusClasses.antrean.default;

            updateText('queue-preview-number', data.number || '--');
            updateText('queue-preview-status', status);
            updateText('queue-preview-doctor', data.doctor || 'Belum ada antrean aktif');
            updateText('queue-preview-schedule', data.schedule || 'Booking antrean untuk hari ini');
            updateText('queue-preview-code', data.code || 'Belum tersedia');
            updateText('queue-preview-updated', data.updated_at || '-');

            const badge = document.getElementById('queue-preview-badge');
            const dot = document.getElementById('queue-preview-dot');

            if (badge) {
                badge.className = badgeClass;
            }

            if (dot) {
                dot.className = `h-2 w-2 rounded-full ${dotClass}`;
            }
        } catch {
            return;
        }
    }

    refreshQueuePreview();
    window.setInterval(refreshQueuePreview, 20000);
}

function initBookingSchedulePicker() {
    const form = document.getElementById('form-booking');

    if (!form) {
        return;
    }

    const dokterSelect = document.getElementById('dokter_id');
    const tanggalInput = document.getElementById('tanggal_kunjungan');
    const jadwalWrapper = document.getElementById('jadwal-wrapper');
    const jadwalList = document.getElementById('jadwal-list');
    const jadwalKosong = document.getElementById('jadwal-kosong');
    const jadwalHidden = document.getElementById('jadwal_dokter_id');

    function resetJadwal() {
        jadwalList.innerHTML = '';
        jadwalHidden.value = '';
    }

    function buildScheduleOption(jadwal) {
        const disabled = jadwal.sisa_kuota <= 0;
        const label = document.createElement('label');

        label.className = `flex cursor-pointer items-center justify-between gap-4 rounded-lg border p-4 transition ${disabled ? 'border-slate-200 bg-slate-50 opacity-60' : 'border-[#d6e7dd] bg-white hover:border-[#7ba891] hover:bg-[#f3faf6]'}`;
        label.innerHTML = `
            <span class="flex items-center gap-3">
                <input type="radio" name="_jadwal_pick" value="${jadwal.id}" class="text-[#7ba891] focus:ring-[#7ba891]" ${disabled ? 'disabled' : ''}>
                <span>
                    <span class="block text-sm font-black text-[#14342f]">${escapeHtml(jadwal.hari)}</span>
                    <span class="block text-sm font-semibold text-[#62756f]">${escapeHtml(jadwal.jam_mulai.substring(0, 5))} - ${escapeHtml(jadwal.jam_selesai.substring(0, 5))} WIB</span>
                </span>
            </span>
            <span class="rounded-md px-3 py-1 text-xs font-black ${disabled ? 'bg-red-50 text-red-600' : 'bg-emerald-50 text-emerald-700'}">
                ${disabled ? 'Penuh' : `Sisa ${jadwal.sisa_kuota} slot`}
            </span>
        `;

        label.querySelector('input[type="radio"]')?.addEventListener('change', (event) => {
            jadwalHidden.value = event.target.value;
        });

        return label;
    }

    async function loadJadwal() {
        const dokterId = dokterSelect.value;
        const tanggal = tanggalInput.value;

        if (!dokterId || !tanggal) {
            jadwalWrapper.classList.add('hidden');
            jadwalKosong.classList.add('hidden');
            resetJadwal();

            return;
        }

        try {
            const url = new URL(form.dataset.scheduleUrl, window.location.origin);
            url.searchParams.set('dokter_id', dokterId);
            url.searchParams.set('tanggal', tanggal);

            const response = await fetch(url, { headers: { Accept: 'application/json' } });
            const data = await response.json();

            resetJadwal();
            jadwalKosong.textContent = 'Tidak ada jadwal praktek tersedia untuk dokter ini pada tanggal yang dipilih.';

            if (data.length === 0) {
                jadwalWrapper.classList.add('hidden');
                jadwalKosong.classList.remove('hidden');

                return;
            }

            jadwalKosong.classList.add('hidden');
            jadwalWrapper.classList.remove('hidden');
            data.forEach((jadwal) => jadwalList.appendChild(buildScheduleOption(jadwal)));
        } catch {
            resetJadwal();
            jadwalWrapper.classList.add('hidden');
            jadwalKosong.classList.remove('hidden');
            jadwalKosong.textContent = 'Jadwal belum bisa dimuat. Coba pilih ulang dokter atau tanggal.';
        }
    }

    dokterSelect.addEventListener('change', loadJadwal);
    tanggalInput.addEventListener('change', loadJadwal);

    if (dokterSelect.value && tanggalInput.value) {
        loadJadwal();
    }
}

function initMidtransPayment() {
    const button = document.getElementById('pay-button');

    if (!button) {
        return;
    }

    button.addEventListener('click', () => {
        window.snap?.pay(button.dataset.snapToken, {
            onSuccess: () => window.location.reload(),
            onPending: () => window.location.reload(),
            onError: () => window.location.reload(),
            onClose: () => {},
        });
    });
}

window.addEventListener('load', () => {
    initQueuePreview();
    initBookingSchedulePicker();
    initMidtransPayment();
});
