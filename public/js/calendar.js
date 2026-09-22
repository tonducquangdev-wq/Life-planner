/* ==========================================================================
   LIFE PLANNER - CALENDAR MODULE (REAL DATABASE & FETCH API CRUD ENGINE)
   ========================================================================== */

document.addEventListener('DOMContentLoaded', function () {

    // ==========================================================================
    // 1. STATE & CHỈ SỐ THỜI GIAN
    // ==========================================================================
    let eventsList = []; // Chứa dữ liệu thật fetch từ API backend
    let currentYear = 2026;
    let currentMonth = 9; // Tháng 9
    let activeSelectedDay = 14;

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    // Modals
    const dayDetailModalEl = document.getElementById('dayDetailModal');
    const dayDetailModal = dayDetailModalEl ? new bootstrap.Modal(dayDetailModalEl) : null;

    const createEventModalEl = document.getElementById('createEventModal');
    const createEventModal = createEventModalEl ? new bootstrap.Modal(createEventModalEl) : null;

    const editEventModalEl = document.getElementById('editEventModal');
    const editEventModal = editEventModalEl ? new bootstrap.Modal(editEventModalEl) : null;

    const deleteChoiceModalEl = document.getElementById('deleteEventChoiceModal');
    const deleteChoiceModal = deleteChoiceModalEl ? new bootstrap.Modal(deleteChoiceModalEl) : null;

    // Filter State
    let activeFilters = new Set();

    // Type Maps
    const typeIconMap = {
        'hoc-tap': 'bi-book-fill',
        'tap-luyen': 'bi-activity',
        'deadline': 'bi-exclamation-triangle-fill',
        'ca-nhan': 'bi-person-fill'
    };

    const typeLabelMap = {
        'hoc-tap': '📘 Học tập',
        'tap-luyen': '🏋️ Tập luyện',
        'deadline': '⏰ Deadline',
        'ca-nhan': '🎉 Cá nhân'
    };

    // Helper Toast Thông báo
    function showToast(message, isSuccess = true) {
        let toastContainer = document.getElementById('toastContainer');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toastContainer';
            toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            toastContainer.style.zIndex = '1090';
            document.body.appendChild(toastContainer);
        }

        const toastId = 'toast-' + Date.now();
        const toastHtml = `
            <div id="${toastId}" class="toast align-items-center text-white ${isSuccess ? 'bg-success' : 'bg-danger'} border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body d-flex align-items-center gap-2">
                        <i class="bi ${isSuccess ? 'bi-check-circle-fill' : 'bi-exclamation-octagon-fill'} fs-5"></i>
                        <span>${message}</span>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;
        toastContainer.insertAdjacentHTML('beforeend', toastHtml);
        const toastEl = document.getElementById(toastId);
        const bsToast = new bootstrap.Toast(toastEl, { delay: 3500 });
        bsToast.show();
        toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
    }

    function disposeTooltips() {
        const tooltips = document.querySelectorAll('.tooltip');
        tooltips.forEach(t => t.remove());
    }

    // ==========================================================================
    // 2. FETCH DATABASE CRUD OPERATIONS
    // ==========================================================================

    /**
     * Nạp toàn bộ dữ liệu sự kiện từ Database qua API GET /calendar/events
     */
    async function loadEventsFromDatabase() {
        try {
            const response = await fetch('/calendar/events', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            if (!response.ok) {
                throw new Error('Không thể kết nối dữ liệu từ máy chủ.');
            }

            const result = await response.json();
            if (result.success && Array.isArray(result.data)) {
                eventsList = result.data.map(evt => {
                    const startDate = evt.start ? new Date(evt.start) : new Date();
                    const endDate = evt.end ? new Date(evt.end) : null;

                    const day = startDate.getDate();
                    const month = startDate.getMonth() + 1;
                    const year = startDate.getFullYear();

                    const startHours = String(startDate.getHours()).padStart(2, '0');
                    const startMins = String(startDate.getMinutes()).padStart(2, '0');
                    const startTimeStr = `${startHours}:${startMins}`;

                    let timeRangeStr = startTimeStr;
                    let endTimeStr = '';
                    if (endDate) {
                        const endHours = String(endDate.getHours()).padStart(2, '0');
                        const endMins = String(endDate.getMinutes()).padStart(2, '0');
                        endTimeStr = `${endHours}:${endMins}`;
                        timeRangeStr = `${startTimeStr} - ${endTimeStr}`;
                    }

                    return {
                        id: evt.id,
                        day: day,
                        month: month,
                        year: year,
                        isCurrentMonth: true,
                        type: evt.type || 'ca-nhan',
                        title: evt.title,
                        time: timeRangeStr,
                        startTime: startTimeStr,
                        endTime: endTimeStr,
                        location: evt.location || '',
                        batThongBao: Boolean(evt.bat_thong_bao),
                        soNgayNhac: parseInt(evt.so_ngay_nhac || 1)
                    };
                });
            }
        } catch (error) {
            console.error('Lỗi khi tải lịch:', error);
        } finally {
            renderCalendarGrid();
        }
    }

    // ==========================================================================
    // 3. DYNAMIC MONTH GRID GENERATOR (TÍNH TOÁN NGÀY TRONG THÁNG)
    // ==========================================================================
    function generateMonthDaysStructure(year, month) {
        const firstDay = new Date(year, month - 1, 1);
        const lastDay = new Date(year, month, 0);
        const prevMonthLastDay = new Date(year, month - 1, 0).getDate();

        const startDayOfWeek = firstDay.getDay(); // 0 (CN) -> 6 (T7)
        const totalDays = lastDay.getDate();

        const today = new Date();
        const isCurrentActualYear = today.getFullYear() === year;
        const isCurrentActualMonth = (today.getMonth() + 1) === month;
        const currentActualDay = today.getDate();

        const monthDays = [];

        // Các ngày cuối tháng trước
        for (let i = startDayOfWeek - 1; i >= 0; i--) {
            monthDays.push({
                day: prevMonthLastDay - i,
                month: month === 1 ? 12 : month - 1,
                year: month === 1 ? year - 1 : year,
                isCurrentMonth: false,
                isToday: false
            });
        }

        // Các ngày trong tháng hiện tại
        for (let d = 1; d <= totalDays; d++) {
            monthDays.push({
                day: d,
                month: month,
                year: year,
                isCurrentMonth: true,
                isToday: isCurrentActualYear && isCurrentActualMonth && (d === currentActualDay)
            });
        }

        // Các ngày đầu tháng sau cho tròn ô lưới
        const totalCells = monthDays.length > 35 ? 42 : 35;
        const remaining = totalCells - monthDays.length;
        for (let n = 1; n <= remaining; n++) {
            monthDays.push({
                day: n,
                month: month === 12 ? 1 : month + 1,
                year: month === 12 ? year + 1 : year,
                isCurrentMonth: false,
                isToday: false
            });
        }

        return monthDays;
    }

    function getEventsForDay(day, isCurrentMonth, month = currentMonth, year = currentYear) {
        if (!isCurrentMonth) return [];
        return eventsList.filter(evt => evt.day === day && evt.month === month && evt.year === year);
    }

    // ==========================================================================
    // 4. RENDER CALENDAR GRID
    // ==========================================================================
    function renderCalendarGrid() {
        disposeTooltips();
        const calendarGrid = document.getElementById('calendarGrid');
        if (!calendarGrid) return;

        const headerHtml = `
            <div class="calendar-header-day weekend">CN</div>
            <div class="calendar-header-day">T2</div>
            <div class="calendar-header-day">T3</div>
            <div class="calendar-header-day">T4</div>
            <div class="calendar-header-day">T5</div>
            <div class="calendar-header-day">T6</div>
            <div class="calendar-header-day weekend">T7</div>
        `;

        const monthDays = generateMonthDaysStructure(currentYear, currentMonth);
        let cellsHtml = '';

        monthDays.forEach(cell => {
            const cellEvents = getEventsForDay(cell.day, cell.isCurrentMonth, cell.month, cell.year);

            const filteredEvents = cellEvents.filter(e => {
                return activeFilters.size === 0 || activeFilters.has(e.type);
            });

            filteredEvents.sort((a, b) => a.time.localeCompare(b.time));

            const otherMonthClass = !cell.isCurrentMonth ? 'other-month' : '';
            const todayClass = cell.isToday ? 'is-today' : '';

            cellsHtml += `
                <div class="calendar-day-cell ${otherMonthClass} ${todayClass}" data-day="${cell.day}" data-current-month="${cell.isCurrentMonth ? '1' : '0'}">
                    <div class="day-header">
                        <span class="day-number">${cell.day}</span>
                        ${cell.isToday ? '<span class="today-tag">Hôm nay</span>' : ''}
                    </div>
                    <div class="event-pill-list">
            `;

            const maxVisible = 3;
            const displayCount = Math.min(filteredEvents.length, maxVisible);

            for (let i = 0; i < displayCount; i++) {
                const evt = filteredEvents[i];
                const iconClass = typeIconMap[evt.type] || 'bi-circle-fill';
                const bellIconHtml = evt.batThongBao ? '🔔 ' : '';
                const tooltipTitle = evt.batThongBao
                    ? `Đang nhắc trước ${evt.soNgayNhac || 1} ngày • ${evt.title} (${evt.time})`
                    : `${evt.title} (${evt.time}) • ${evt.location}`;

                cellsHtml += `
                    <div class="event-pill ${evt.type}" data-event-id="${evt.id}" data-bs-toggle="tooltip" data-bs-placement="top" title="${tooltipTitle}">
                        <i class="bi ${iconClass} fs-8"></i>
                        <span>${bellIconHtml}${evt.title}</span>
                    </div>
                `;
            }

            if (filteredEvents.length > maxVisible) {
                const extraCount = filteredEvents.length - maxVisible;
                cellsHtml += `
                    <div class="event-more-pill" data-day="${cell.day}">
                        +${extraCount} khác
                    </div>
                `;
            }

            cellsHtml += `
                    </div>
                </div>
            `;
        });

        calendarGrid.innerHTML = headerHtml + cellsHtml;

        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltipTriggerList.forEach(el => new bootstrap.Tooltip(el, { container: 'body' }));

        attachCellClickHandlers();
        renderTodaySchedule();
    }

    function attachCellClickHandlers() {
        document.querySelectorAll('.calendar-day-cell').forEach(cell => {
            cell.addEventListener('click', function () {
                const day = parseInt(this.getAttribute('data-day'));
                const isCurrentMonth = this.getAttribute('data-current-month') === '1';

                if (!isCurrentMonth) return;

                activeSelectedDay = day;
                openDayDetailModal(day);
            });
        });
    }

    function openDayDetailModal(day) {
        activeSelectedDay = day;
        const modalTitle = document.getElementById('selectedDateTitle');
        if (modalTitle) {
            modalTitle.textContent = `Chi tiết Lịch Ngày ${day}/${String(currentMonth).padStart(2, '0')}/${currentYear}`;
        }

        renderDayDetailEventsList(day);
        dayDetailModal?.show();
    }

    function renderDayDetailEventsList(day) {
        const listContainer = document.getElementById('dayEventsDetailList');
        if (!listContainer) return;

        const dayEvents = getEventsForDay(day, true, currentMonth, currentYear);
        dayEvents.sort((a, b) => a.time.localeCompare(b.time));

        if (dayEvents.length === 0) {
            listContainer.innerHTML = `
                <div class="text-center py-5">
                    <i class="bi bi-calendar-x text-muted display-4"></i>
                    <h6 class="fw-bold text-dark mt-3 mb-1">Chưa có sự kiện nào cho Ngày ${day}/${String(currentMonth).padStart(2, '0')}/${currentYear}</h6>
                    <p class="text-muted fs-7 mb-4">Bạn chưa lên lịch học, lịch tập hay deadline cho ngày này.</p>
                    <button type="button" class="btn btn-primary rounded-pill px-4 btn-sm" id="btnEmptyStateAdd">
                        <i class="bi bi-plus-lg me-1"></i>Thêm sự kiện ngay
                    </button>
                </div>
            `;

            document.getElementById('btnEmptyStateAdd')?.addEventListener('click', function () {
                dayDetailModal?.hide();
                openCreateModalWithDay(day);
            });
            return;
        }

        let html = '';
        dayEvents.forEach(evt => {
            const iconClass = typeIconMap[evt.type] || 'bi-calendar';
            const notifTag = evt.batThongBao ? `
                <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2 py-0.5 fs-8 d-inline-flex align-items-center gap-1" title="Đang nhắc trước ${evt.soNgayNhac || 1} ngày">
                    🔔 Nhắc trước ${evt.soNgayNhac || 1} ngày
                </span>
            ` : '';

            html += `
                <div class="day-event-detail-item ${evt.type}">
                    <div class="d-flex align-items-center gap-3 flex-grow-1 min-w-0">
                        <div class="event-icon-badge">
                            <i class="bi ${iconClass}"></i>
                        </div>
                        <div class="text-truncate">
                            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                <span class="fw-bold text-dark fs-6 text-truncate">${evt.title}</span>
                                ${notifTag}
                            </div>
                            <div class="d-flex align-items-center gap-3 text-muted fs-7">
                                <span><i class="bi bi-clock me-1 text-primary"></i>${evt.time}</span>
                                <span><i class="bi bi-geo-alt me-1 text-secondary"></i>${evt.location || 'Chưa có thông tin'}</span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-shrink-0">
                        <button type="button" class="btn-action btn-edit" data-edit-id="${evt.id}" title="Sửa sự kiện">
                            <i class="bi bi-pencil-fill"></i>
                        </button>
                        <button type="button" class="btn-action btn-delete" data-delete-id="${evt.id}" title="Xóa sự kiện">
                            <i class="bi bi-trash3-fill"></i>
                        </button>
                    </div>
                </div>
            `;
        });

        listContainer.innerHTML = html;

        listContainer.querySelectorAll('[data-edit-id]').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                const id = parseInt(this.getAttribute('data-edit-id'));
                openEditModal(id);
            });
        });

        listContainer.querySelectorAll('[data-delete-id]').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                const id = parseInt(this.getAttribute('data-delete-id'));
                deleteEvent(id, day);
            });
        });
    }

    function renderTodaySchedule() {
        const todayList = document.getElementById('todayScheduleList');
        const badge = document.getElementById('todayCountBadge');
        if (!todayList) return;

        const today = new Date();
        const todayEvents = getEventsForDay(today.getDate(), true, today.getMonth() + 1, today.getFullYear());
        todayEvents.sort((a, b) => a.time.localeCompare(b.time));

        if (badge) badge.textContent = todayEvents.length;

        if (todayEvents.length === 0) {
            todayList.innerHTML = '<p class="text-muted fs-7 text-center py-3 mb-0">Hôm nay không có lịch trình nào.</p>';
            return;
        }

        let html = '';
        todayEvents.slice(0, 4).forEach(evt => {
            html += `
                <div class="schedule-item ${evt.type}" data-type="${evt.type}">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="fw-bold text-dark fs-7">${evt.time}</span>
                        <span class="badge rounded-pill px-2 py-0 fs-8 legend-pill ${evt.type}">${typeLabelMap[evt.type] || 'Sự kiện'}</span>
                    </div>
                    <div class="fw-semibold text-dark fs-6 mb-1 text-truncate">${evt.title}</div>
                    <small class="text-muted fs-7"><i class="bi bi-geo-alt me-1"></i>${evt.location || 'Chưa có vị trí'}</small>
                </div>
            `;
        });

        todayList.innerHTML = html;
    }

    // Toggle switch thông báo UI
    document.querySelectorAll('.notif-toggle-switch').forEach(switchEl => {
        switchEl.addEventListener('change', function () {
            const targetSelector = this.getAttribute('data-target');
            if (targetSelector) {
                const targetEl = document.querySelector(targetSelector);
                if (targetEl) {
                    if (this.checked) {
                        targetEl.classList.add('show');
                    } else {
                        targetEl.classList.remove('show');
                    }
                }
            }
        });
    });

    document.getElementById('btnAddNewFromDayModal')?.addEventListener('click', function () {
        dayDetailModal?.hide();
        openCreateModalWithDay(activeSelectedDay);
    });

    function openCreateModalWithDay(day) {
        document.getElementById('createHocTapDay').value = day;
        document.getElementById('createTapLuyenDay').value = day;
        document.getElementById('createDeadlineDay').value = day;
        document.getElementById('createCaNhanDay').value = day;

        ['createHocTap', 'createTapLuyen', 'createDeadline', 'createCaNhan'].forEach(prefix => {
            const sw = document.getElementById(`${prefix}BatThongBao`);
            const sel = document.getElementById(`${prefix}SoNgayNhac`);
            const grp = document.getElementById(`${prefix}RemindGroup`);
            if (sw) sw.checked = false;
            if (sel) sel.value = "1";
            if (grp) grp.classList.remove('show');
        });

        createEventModal?.show();
    }

    // ==========================================================================
    // 5. BƯỚC 5: TẠO SỰ KIỆN (POST /calendar/events)
    // ==========================================================================
    document.getElementById('btnSaveNewEvent')?.addEventListener('click', async function () {
        const activeTab = document.querySelector('#eventTab .nav-link.active');
        const tabId = activeTab ? activeTab.getAttribute('id') : 'hoc-tap-tab';

        let payload = {};

        if (tabId === 'hoc-tap-tab') {
            const title = document.getElementById('createHocTapTitle').value.trim();
            if (!title) { showToast('Vui lòng nhập tên môn học.', false); return; }
            const day = parseInt(document.getElementById('createHocTapDay').value);
            const startTime = document.getElementById('createHocTapTime').value || '08:00';
            const endTime = document.getElementById('createHocTapEndTime').value || '10:30';
            const location = document.getElementById('createHocTapLocation').value.trim();

            payload = {
                tieu_de: title,
                loai_su_kien: 'hoc_tap',
                thoi_gian_bat_dau: `${currentYear}-${String(currentMonth).padStart(2, '0')}-${String(day).padStart(2, '0')} ${startTime}:00`,
                thoi_gian_ket_thuc: `${currentYear}-${String(currentMonth).padStart(2, '0')}-${String(day).padStart(2, '0')} ${endTime}:00`,
                mo_ta: location,
                bat_thong_bao: document.getElementById('createHocTapBatThongBao')?.checked || false,
                so_ngay_nhac: parseInt(document.getElementById('createHocTapSoNgayNhac')?.value || 1)
            };
        } else if (tabId === 'tap-luyen-tab') {
            const title = document.getElementById('createTapLuyenTitle').value.trim();
            if (!title) { showToast('Vui lòng nhập tên buổi tập.', false); return; }
            const day = parseInt(document.getElementById('createTapLuyenDay').value);
            const startTime = document.getElementById('createTapLuyenTime').value || '18:00';
            const endTime = document.getElementById('createTapLuyenEndTime').value || '19:30';
            const location = document.getElementById('createTapLuyenLocation').value.trim();

            payload = {
                tieu_de: title,
                loai_su_kien: 'tap_luyen',
                thoi_gian_bat_dau: `${currentYear}-${String(currentMonth).padStart(2, '0')}-${String(day).padStart(2, '0')} ${startTime}:00`,
                thoi_gian_ket_thuc: `${currentYear}-${String(currentMonth).padStart(2, '0')}-${String(day).padStart(2, '0')} ${endTime}:00`,
                mo_ta: location,
                bat_thong_bao: document.getElementById('createTapLuyenBatThongBao')?.checked || false,
                so_ngay_nhac: parseInt(document.getElementById('createTapLuyenSoNgayNhac')?.value || 1)
            };
        } else if (tabId === 'deadline-tab') {
            const title = document.getElementById('createDeadlineTitle').value.trim();
            if (!title) { showToast('Vui lòng nhập tiêu đề deadline.', false); return; }
            const day = parseInt(document.getElementById('createDeadlineDay').value);
            const time = document.getElementById('createDeadlineTime').value || '23:59';
            const location = document.getElementById('createDeadlineLocation').value.trim();

            payload = {
                tieu_de: title,
                loai_su_kien: 'deadline',
                thoi_gian_bat_dau: `${currentYear}-${String(currentMonth).padStart(2, '0')}-${String(day).padStart(2, '0')} ${time}:00`,
                mo_ta: location,
                bat_thong_bao: document.getElementById('createDeadlineBatThongBao')?.checked || false,
                so_ngay_nhac: parseInt(document.getElementById('createDeadlineSoNgayNhac')?.value || 1)
            };
        } else {
            const title = document.getElementById('createCaNhanTitle').value.trim();
            if (!title) { showToast('Vui lòng nhập tên sự kiện.', false); return; }
            const day = parseInt(document.getElementById('createCaNhanDay').value);
            const time = document.getElementById('createCaNhanTime').value || '14:00';
            const location = document.getElementById('createCaNhanLocation').value.trim();

            payload = {
                tieu_de: title,
                loai_su_kien: 'ca_nhan',
                thoi_gian_bat_dau: `${currentYear}-${String(currentMonth).padStart(2, '0')}-${String(day).padStart(2, '0')} ${time}:00`,
                mo_ta: location,
                bat_thong_bao: document.getElementById('createCaNhanBatThongBao')?.checked || false,
                so_ngay_nhac: parseInt(document.getElementById('createCaNhanSoNgayNhac')?.value || 1)
            };
        }

        try {
            const response = await fetch('/calendar/events', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(payload)
            });

            const result = await response.json();
            if (response.ok && result.success) {
                createEventModal?.hide();
                showToast(result.message || 'Thêm sự kiện thành công!');
                await loadEventsFromDatabase();
                openDayDetailModal(payload.thoi_gian_bat_dau ? parseInt(payload.thoi_gian_bat_dau.split(' ')[0].split('-')[2]) : activeSelectedDay);
            } else {
                showToast(result.message || 'Lỗi khi tạo sự kiện.', false);
            }
        } catch (error) {
            console.error('Lỗi API POST /calendar/events:', error);
            showToast('Lỗi gửi yêu cầu tạo sự kiện.', false);
        }
    });

    // ==========================================================================
    // 6. BƯỚC 6: SỬA SỰ KIỆN (PUT /calendar/events/{id})
    // ==========================================================================
    function openEditModal(eventId) {
        const evt = eventsList.find(e => e.id === eventId);
        if (!evt) return;

        dayDetailModal?.hide();

        document.getElementById('editEventId').value = evt.id;
        document.getElementById('editEventType').value = evt.type;
        document.getElementById('editEventTitle').value = evt.title;
        document.getElementById('editEventDay').value = evt.day;
        document.getElementById('editEventTime').value = evt.startTime || '08:00';
        document.getElementById('editEventEndTime').value = evt.endTime || '';
        document.getElementById('editEventLocation').value = evt.location || '';

        const editSw = document.getElementById('editEventBatThongBao');
        const editSel = document.getElementById('editEventSoNgayNhac');
        const editGrp = document.getElementById('editEventRemindGroup');

        if (editSw) editSw.checked = evt.batThongBao || false;
        if (editSel) editSel.value = evt.soNgayNhac || 1;
        if (editGrp) {
            if (evt.batThongBao) editGrp.classList.add('show');
            else editGrp.classList.remove('show');
        }

        editEventModal?.show();
    }

    document.getElementById('btnUpdateEvent')?.addEventListener('click', async function () {
        const id = document.getElementById('editEventId').value;
        if (!id) return;

        const type = document.getElementById('editEventType').value;
        const title = document.getElementById('editEventTitle').value.trim();
        const day = parseInt(document.getElementById('editEventDay').value);
        const startTime = document.getElementById('editEventTime').value || '08:00';
        const endTime = document.getElementById('editEventEndTime').value;
        const location = document.getElementById('editEventLocation').value.trim();

        if (!title) {
            showToast('Vui lòng nhập tiêu đề sự kiện.', false);
            return;
        }

        const payload = {
            tieu_de: title,
            loai_su_kien: type,
            thoi_gian_bat_dau: `${currentYear}-${String(currentMonth).padStart(2, '0')}-${String(day).padStart(2, '0')} ${startTime}:00`,
            mo_ta: location,
            bat_thong_bao: document.getElementById('editEventBatThongBao')?.checked || false,
            so_ngay_nhac: parseInt(document.getElementById('editEventSoNgayNhac')?.value || 1)
        };

        if (endTime) {
            payload.thoi_gian_ket_thuc = `${currentYear}-${String(currentMonth).padStart(2, '0')}-${String(day).padStart(2, '0')} ${endTime}:00`;
        }

        try {
            const response = await fetch(`/calendar/events/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(payload)
            });

            const result = await response.json();
            if (response.ok && result.success) {
                editEventModal?.hide();
                showToast(result.message || 'Cập nhật sự kiện thành công!');
                await loadEventsFromDatabase();
                openDayDetailModal(day);
            } else {
                showToast(result.message || 'Lỗi khi cập nhật sự kiện.', false);
            }
        } catch (error) {
            console.error('Lỗi API PUT /calendar/events:', error);
            showToast('Lỗi gửi yêu cầu cập nhật.', false);
        }
    });

    // ==========================================================================
    // 7. BƯỚC 7: XÓA SỰ KIỆN (DELETE /calendar/events/{id})
    // ==========================================================================
    let pendingDeleteEventId = null;
    let pendingDeleteDay = null;

    function deleteEvent(eventId, day) {
        const evt = eventsList.find(e => e.id === eventId);
        if (!evt) return;

        if (confirm(`Bạn có chắc chắn muốn xóa sự kiện "${evt.title}" không?`)) {
            executeDeleteApi(eventId, day);
        }
    }

    async function executeDeleteApi(eventId, day) {
        try {
            const response = await fetch(`/calendar/events/${eventId}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            const result = await response.json();
            if (response.ok && result.success) {
                deleteChoiceModal?.hide();
                showToast(result.message || 'Đã xóa sự kiện thành công!');
                await loadEventsFromDatabase();
                openDayDetailModal(day);
            } else {
                showToast(result.message || 'Không thể xóa sự kiện này.', false);
            }
        } catch (error) {
            console.error('Lỗi API DELETE /calendar/events:', error);
            showToast('Lỗi gửi yêu cầu xóa.', false);
        }
    }

    document.getElementById('btnDeleteOnlyThisOccurrence')?.addEventListener('click', function () {
        if (pendingDeleteEventId && pendingDeleteDay) {
            executeDeleteApi(pendingDeleteEventId, pendingDeleteDay);
        }
    });

    document.getElementById('btnDeleteAllOccurrences')?.addEventListener('click', function () {
        if (pendingDeleteEventId && pendingDeleteDay) {
            executeDeleteApi(pendingDeleteEventId, pendingDeleteDay);
        }
    });

    // Multi-select Legend Filter Buttons
    const allBtn = document.querySelector('#calendarFilterGroup .legend-btn[data-filter="all"]');
    const categoryButtons = document.querySelectorAll('#calendarFilterGroup .legend-btn:not([data-filter="all"])');
    const filterContainer = document.getElementById('calendarFilterGroup');

    allBtn?.addEventListener('click', function () {
        activeFilters.clear();
        applyFilterStyles();
        renderCalendarGrid();
    });

    categoryButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const filter = this.getAttribute('data-filter');
            if (activeFilters.has(filter)) {
                activeFilters.delete(filter);
            } else {
                activeFilters.add(filter);
            }
            applyFilterStyles();
            renderCalendarGrid();
        });
    });

    function applyFilterStyles() {
        if (activeFilters.size === 0) {
            allBtn?.classList.add('active');
            filterContainer?.classList.remove('filter-active');
            categoryButtons.forEach(b => b.classList.remove('active'));
        } else {
            allBtn?.classList.remove('active');
            filterContainer?.classList.add('filter-active');
            categoryButtons.forEach(b => {
                const filter = b.getAttribute('data-filter');
                if (activeFilters.has(filter)) {
                    b.classList.add('active');
                } else {
                    b.classList.remove('active');
                }
            });
        }
    }

    // Quick Search Input
    document.getElementById('searchCalendar')?.addEventListener('input', function (e) {
        const query = e.target.value.toLowerCase().trim();
        if (!query) {
            renderCalendarGrid();
            return;
        }

        document.querySelectorAll('.event-pill').forEach(pill => {
            const text = pill.textContent.toLowerCase();
            if (text.includes(query)) {
                pill.style.outline = '2px solid #4f46e5';
            } else {
                pill.style.outline = 'none';
            }
        });
    });

    // Nút Prev/Next Month
    document.getElementById('btnPrevMonth')?.addEventListener('click', function () {
        if (currentMonth === 1) {
            currentMonth = 12;
            currentYear--;
        } else {
            currentMonth--;
        }
        document.getElementById('currentMonthTitle').textContent = `Tháng ${currentMonth}, ${currentYear}`;
        renderCalendarGrid();
    });

    document.getElementById('btnNextMonth')?.addEventListener('click', function () {
        if (currentMonth === 12) {
            currentMonth = 1;
            currentYear++;
        } else {
            currentMonth++;
        }
        document.getElementById('currentMonthTitle').textContent = `Tháng ${currentMonth}, ${currentYear}`;
        renderCalendarGrid();
    });

    // 1. Vẽ lưới lịch ban đầu lập tức khi vừa mở trang
    renderCalendarGrid();

    // 2. Nạp dữ liệu sự kiện thực từ Database
    loadEventsFromDatabase();
});
