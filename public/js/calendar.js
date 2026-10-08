/* ==========================================================================
   LIFE PLANNER - CALENDAR MODULE (REAL DATABASE & FETCH API CRUD ENGINE)
   ========================================================================== */

document.addEventListener('DOMContentLoaded', function () {

    // ==========================================================================
    // 1. STATE & CHỈ SỐ THỜI GIAN
    // ==========================================================================
    let eventsList = []; // Chứa dữ liệu thật fetch từ API backend
    const todayDate = new Date();
    let currentYear = todayDate.getFullYear();
    let currentMonth = todayDate.getMonth() + 1;
    let activeSelectedDay = todayDate.getDate();

    // Chế độ xem Lịch: 'week' (Lịch Tuần Mobile - Mặc định trên màn hình < 768px) hoặc 'month' (Lịch Tháng)
    let calendarViewMode = (window.innerWidth < 768) ? 'week' : 'month';

    function getStartOfWeek(d) {
        const date = new Date(d);
        const day = date.getDay(); // 0: CN -> 6: T7
        const diff = date.getDate() - day;
        return new Date(date.setDate(diff));
    }

    let currentWeekStartDate = getStartOfWeek(todayDate);

    function formatShortDate(day, month, year) {
        const d = String(day).padStart(2, '0');
        const m = String(month).padStart(2, '0');
        return `${d}/${m}/${year}`;
    }

    function getRemindTimeValue(selectId) {
        const sel = document.getElementById(selectId);
        if (!sel) return 15;
        if (sel.value === 'custom') {
            const box = sel.closest('.notif-remind-box');
            const num = parseInt(box?.querySelector('.custom-time-num')?.value || 15);
            const unit = parseInt(box?.querySelector('.custom-time-unit')?.value || 1);
            return Math.max(0, num * unit);
        }
        return parseInt(sel.value) || 15;
    }

    function setRemindTimeValue(selectId, valueInMinutes) {
        const sel = document.getElementById(selectId);
        if (!sel) return;
        const val = parseInt(valueInMinutes !== undefined && valueInMinutes !== null ? valueInMinutes : 15);
        const box = sel.closest('.notif-remind-box');
        const customBox = box?.querySelector('.custom-time-inputs');

        const presetValues = ['0', '5', '10', '15', '30', '60', '120', '1440', '2880', '10080'];
        if (presetValues.includes(String(val))) {
            sel.value = String(val);
            if (customBox) customBox.classList.add('d-none');
        } else {
            sel.value = 'custom';
            if (customBox) customBox.classList.remove('d-none');
            const numInput = box?.querySelector('.custom-time-num');
            const unitSelect = box?.querySelector('.custom-time-unit');
            if (numInput && unitSelect) {
                if (val > 0 && val % 1440 === 0) {
                    numInput.value = val / 1440;
                    unitSelect.value = '1440';
                } else if (val > 0 && val % 60 === 0) {
                    numInput.value = val / 60;
                    unitSelect.value = '60';
                } else {
                    numInput.value = val;
                    unitSelect.value = '1';
                }
            }
        }
    }

    document.addEventListener('change', function (e) {
        if (e.target && e.target.classList.contains('notif-select-time')) {
            const box = e.target.closest('.notif-remind-box');
            const customBox = box?.querySelector('.custom-time-inputs');
            if (customBox) {
                if (e.target.value === 'custom') {
                    customBox.classList.remove('d-none');
                } else {
                    customBox.classList.add('d-none');
                }
            }
        }
    });

    const currentMonthTitleEl = document.getElementById('currentMonthTitle');
    if (currentMonthTitleEl) {
        currentMonthTitleEl.textContent = formatShortDate(activeSelectedDay, currentMonth, currentYear);
    }


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
        'ca-nhan': 'bi-person-fill',
        'cong-viec': 'bi-briefcase-fill'
    };

    const typeLabelMap = {
        'hoc-tap': '📘 Học tập',
        'tap-luyen': '🏋️ Tập luyện',
        'deadline': '⏰ Deadline',
        'ca-nhan': '🎉 Cá nhân',
        'cong-viec': '💼 Công việc'
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
                        soNgayNhac: parseInt(evt.so_ngay_nhac || 1),
                        quyTacLap: evt.quy_tac_lap || 'once',
                        ngayKetThucLap: evt.ngay_ket_thuc_lap || null,
                        nhomLapId: evt.nhom_lap_id || null
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
    // 4. RENDER CALENDAR GRID (ĐA CHẾ ĐỘ: TUẦN / THÁNG)
    // ==========================================================================
    function renderCalendarGrid() {
        disposeTooltips();
        const calendarGrid = document.getElementById('calendarGrid');
        const mobileWeekCalendar = document.getElementById('mobileWeekCalendar');

        if (calendarViewMode === 'week') {
            if (calendarGrid) calendarGrid.classList.add('d-none');
            if (mobileWeekCalendar) mobileWeekCalendar.classList.remove('d-none');
            renderWeekCalendarView();
        } else {
            if (calendarGrid) calendarGrid.classList.remove('d-none');
            if (mobileWeekCalendar) mobileWeekCalendar.classList.add('d-none');
            renderMonthCalendarGrid();
        }
        renderTodaySchedule();
    }

    /**
     * Render Chế độ Lịch Tháng (Desktop / Laptop View)
     */
    function renderMonthCalendarGrid() {
        const calendarGrid = document.getElementById('calendarGrid');
        if (!calendarGrid) return;

        if (currentMonthTitleEl) {
            currentMonthTitleEl.textContent = formatShortDate(activeSelectedDay, currentMonth, currentYear);
        }

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
                const repeatIconHtml = (evt.quyTacLap && evt.quyTacLap !== 'once') ? '🔄 ' : '';
                const tooltipTitle = evt.batThongBao
                    ? `Đang nhắc trước ${evt.soNgayNhac || 1} ngày • ${evt.title} (${evt.time})`
                    : `${evt.title} (${evt.time}) • ${evt.location}`;

                cellsHtml += `
                    <div class="event-pill ${evt.type}" data-event-id="${evt.id}" data-bs-toggle="tooltip" data-bs-placement="top" title="${tooltipTitle}">
                        <i class="bi ${iconClass} fs-8"></i>
                        <span>${bellIconHtml}${repeatIconHtml}${evt.title}</span>
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
    }

    /**
     * Render Chế độ Lịch Tuần Mobile (7 ngày dạng strip + danh sách lịch trình)
     */
    function renderWeekCalendarView() {
        const weekStripGrid = document.getElementById('weekStripGrid');
        const mobileDayEventsList = document.getElementById('mobileDayEventsList');
        const mobileSelectedDayTitle = document.getElementById('mobileSelectedDayTitle');
        if (!weekStripGrid) return;

        const weekDays = [];
        const today = new Date();
        const dayNames = ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'];

        for (let i = 0; i < 7; i++) {
            const d = new Date(currentWeekStartDate);
            d.setDate(currentWeekStartDate.getDate() + i);

            const isToday = (d.getFullYear() === today.getFullYear() &&
                             d.getMonth() === today.getMonth() &&
                             d.getDate() === today.getDate());

            weekDays.push({
                dateObj: d,
                day: d.getDate(),
                month: d.getMonth() + 1,
                year: d.getFullYear(),
                dayName: dayNames[d.getDay()],
                isToday: isToday
            });
        }

        let selectedInWeek = weekDays.find(w => w.day === activeSelectedDay && w.month === currentMonth);
        if (!selectedInWeek) {
            selectedInWeek = weekDays.find(w => w.isToday) || weekDays[0];
            activeSelectedDay = selectedInWeek.day;
            currentMonth = selectedInWeek.month;
            currentYear = selectedInWeek.year;
        }

        if (currentMonthTitleEl) {
            currentMonthTitleEl.textContent = formatShortDate(activeSelectedDay, currentMonth, currentYear);
        }

        let stripHtml = '';
        weekDays.forEach(w => {
            const dayEvents = getEventsForDay(w.day, true, w.month, w.year);
            const filtered = dayEvents.filter(e => activeFilters.size === 0 || activeFilters.has(e.type));

            let eventsHtml = '';
            if (filtered.length > 0) {
                filtered.forEach(evt => {
                    const evtTitle = evt.title || evt.tieu_de || 'Sự kiện';
                    const evtType = evt.type || 'hoc-tap';
                    const evtTime = evt.time || 'Cả ngày';
                    const evtLoc = evt.location || '';
                    const iconClass = typeIconMap[evtType] || 'bi-calendar-event';

                    eventsHtml += `
                        <div class="week-event-detail-item ${evtType}" title="${evtTitle}">
                            <div class="week-event-title">
                                <i class="bi ${iconClass} me-1"></i>${evtTitle}
                            </div>
                            <div class="week-event-meta">
                                <span class="week-event-time"><i class="bi bi-clock me-1"></i>${evtTime}</span>
                                ${evtLoc ? `<span class="week-event-loc"><i class="bi bi-geo-alt me-1"></i>${evtLoc}</span>` : ''}
                            </div>
                        </div>
                    `;
                });
            } else {
                eventsHtml = `<div class="week-event-empty">Chưa có lịch trình</div>`;
            }

            const isActive = (w.day === activeSelectedDay && w.month === currentMonth && w.year === currentYear) ? 'active' : '';
            const todayClass = w.isToday ? 'is-today' : '';

            stripHtml += `
                <div class="week-day-card ${isActive} ${todayClass}" data-day="${w.day}" data-month="${w.month}" data-year="${w.year}">
                    <div class="week-day-header">
                        <span class="day-name">${w.dayName}</span>
                        <span class="day-number">${w.day}</span>
                    </div>
                    <div class="week-events-container">
                        ${eventsHtml}
                    </div>
                </div>
            `;
        });

        weekStripGrid.innerHTML = stripHtml;

        const activeWeekCard = weekStripGrid.querySelector('.week-day-card.active');
        if (activeWeekCard) {
            activeWeekCard.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
        }

        weekStripGrid.querySelectorAll('.week-day-card').forEach(card => {
            card.addEventListener('click', function () {
                const day = parseInt(this.getAttribute('data-day'));
                const month = parseInt(this.getAttribute('data-month'));
                const year = parseInt(this.getAttribute('data-year'));

                activeSelectedDay = day;
                currentMonth = month;
                currentYear = year;

                renderWeekCalendarView();
            });
        });

        if (mobileSelectedDayTitle) {
            const selectedDateObj = new Date(currentYear, currentMonth - 1, activeSelectedDay);
            const dayOfWeekStr = dayNames[selectedDateObj.getDay()];
            mobileSelectedDayTitle.innerHTML = `<i class="bi bi-calendar-event text-primary fs-5"></i> Lịch trình ${dayOfWeekStr}, ${String(activeSelectedDay).padStart(2, '0')}/${String(currentMonth).padStart(2, '0')}/${currentYear}`;
        }

        if (mobileDayEventsList) {
            const selectedEvents = getEventsForDay(activeSelectedDay, true, currentMonth, currentYear);
            const filteredEvents = selectedEvents.filter(e => activeFilters.size === 0 || activeFilters.has(e.type));
            filteredEvents.sort((a, b) => a.time.localeCompare(b.time));

            if (filteredEvents.length === 0) {
                mobileDayEventsList.innerHTML = `
                    <div class="text-center py-4">
                        <i class="bi bi-calendar-x text-muted fs-1 d-block mb-2"></i>
                        <div class="fw-bold text-dark fs-7 mb-1">Chưa có lịch trình cho ngày này</div>
                        <p class="text-muted fs-8 mb-3">Bạn chưa có bài tập, môn học hay deadline nào được lên lịch.</p>
                        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3" id="btnMobileEmptyAdd">
                            <i class="bi bi-plus-lg me-1"></i>Thêm sự kiện mới
                        </button>
                    </div>
                `;
                document.getElementById('btnMobileEmptyAdd')?.addEventListener('click', function () {
                    openCreateModalWithDay(activeSelectedDay);
                });
            } else {
                let agendaHtml = '';
                filteredEvents.forEach(evt => {
                    const iconClass = typeIconMap[evt.type] || 'bi-calendar';
                    const notifTag = evt.batThongBao ? `<span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2 py-0.5 fs-8">🔔 Nhắc trước ${evt.soNgayNhac || 1}d</span>` : '';
                    const repeatTag = (evt.quyTacLap && evt.quyTacLap !== 'once') ? `<span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2 py-0.5 fs-8">🔄 Lặp</span>` : '';

                    agendaHtml += `
                        <div class="mobile-agenda-item ${evt.type}">
                            <div class="d-flex align-items-center gap-3 flex-grow-1 min-w-0">
                                <div class="event-icon-badge flex-shrink-0">
                                    <i class="bi ${iconClass}"></i>
                                </div>
                                <div class="min-w-0 flex-grow-1">
                                    <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                        <span class="fw-bold text-dark fs-6 text-truncate">${evt.title}</span>
                                        ${repeatTag}
                                        ${notifTag}
                                    </div>
                                    <div class="d-flex align-items-center gap-3 text-muted fs-7">
                                        <span><i class="bi bi-clock me-1 text-primary"></i>${evt.time}</span>
                                        <span class="text-truncate"><i class="bi bi-geo-alt me-1 text-secondary"></i>${evt.location || 'Chưa có vị trí'}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-1.5 flex-shrink-0 ms-2">
                                <button type="button" class="btn-action btn-edit" data-edit-id="${evt.id}" title="Sửa">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                <button type="button" class="btn-action btn-delete" data-delete-id="${evt.id}" title="Xóa">
                                    <i class="bi bi-trash3-fill"></i>
                                </button>
                            </div>
                        </div>
                    `;
                });
                mobileDayEventsList.innerHTML = agendaHtml;

                mobileDayEventsList.querySelectorAll('[data-edit-id]').forEach(btn => {
                    btn.addEventListener('click', function (e) {
                        e.stopPropagation();
                        const id = parseInt(this.getAttribute('data-edit-id'));
                        openEditModal(id);
                    });
                });

                mobileDayEventsList.querySelectorAll('[data-delete-id]').forEach(btn => {
                    btn.addEventListener('click', function (e) {
                        e.stopPropagation();
                        const id = parseInt(this.getAttribute('data-delete-id'));
                        deleteEvent(id, activeSelectedDay);
                    });
                });
            }
        }
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
            const repeatTag = (evt.quyTacLap && evt.quyTacLap !== 'once') ? `
                <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2 py-0.5 fs-8 d-inline-flex align-items-center gap-1">
                    🔄 ${evt.quyTacLap === 'daily' ? 'Hằng ngày' : (evt.quyTacLap === 'weekly' ? 'Hằng tuần' : 'Hằng tháng')}
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
                                ${repeatTag}
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
        const dateFormatted = `${currentYear}-${String(currentMonth).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        ['createHocTapDay', 'createTapLuyenDay', 'createDeadlineDay', 'createCaNhanDay'].forEach(id => {
            const input = document.getElementById(id);
            if (input) input.value = dateFormatted;
        });

        ['createHocTap', 'createTapLuyen', 'createDeadline', 'createCaNhan'].forEach(prefix => {
            const sw = document.getElementById(`${prefix}BatThongBao`);
            const sel = document.getElementById(`${prefix}SoNgayNhac`);
            const grp = document.getElementById(`${prefix}RemindGroup`);
            if (sw) sw.checked = false;
            setRemindTimeValue(`${prefix}SoNgayNhac`, 15);
            if (grp) grp.classList.remove('show');
        });

        createEventModal?.show();
    }

    // ==========================================================================
    // 5. HELPER CHECK TRÙNG LỊCH (HTTP 409 CONFLICT MODAL ENGINE)
    // ==========================================================================
    const conflictModalEl = document.getElementById('modalConflictWarning');
    const conflictModal = conflictModalEl ? new bootstrap.Modal(conflictModalEl) : null;
    const conflictListContainer = document.getElementById('conflictListContainer');
    const btnConfirmForceSave = document.getElementById('btnConfirmForceSave');

    async function sendEventRequestWithConflictCheck(url, method, payload, modalToHide, targetDay) {
        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(payload)
            });

            const result = await response.json();

            // Nếu phát hiện trùng lịch (HTTP 409)
            if (response.status === 409 && result.has_conflict) {
                if (conflictModal && conflictListContainer) {
                    let html = '<div class="fw-bold text-danger mb-2 fs-7"><i class="bi bi-exclamation-triangle-fill me-1"></i>Phát hiện ' + (result.conflicts ? result.conflicts.length : 0) + ' lịch bị trùng:</div>';
                    html += '<ul class="list-unstyled m-0 d-flex flex-column gap-2">';
                    if (Array.isArray(result.conflicts)) {
                        result.conflicts.forEach(item => {
                            html += `
                                <li class="p-2.5 rounded-3 bg-white border shadow-2xs d-flex align-items-center justify-content-between gap-2">
                                    <div>
                                        <div class="fw-bold text-dark fs-7">${item.title}</div>
                                        <div class="text-muted fs-8"><i class="bi bi-clock me-1"></i>${item.date}: <strong>${item.time}</strong></div>
                                    </div>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill fs-8 flex-shrink-0">Trùng lịch</span>
                                </li>
                            `;
                        });
                    }
                    html += '</ul>';
                    conflictListContainer.innerHTML = html;

                    // Handler khi nhấn "Vẫn lưu sự kiện"
                    btnConfirmForceSave.onclick = async function () {
                        conflictModal.hide();
                        const forcedPayload = { ...payload, force: true };
                        await sendEventRequestWithConflictCheck(url, method, forcedPayload, modalToHide, targetDay);
                    };

                    conflictModal.show();
                }
                return;
            }

            if (response.ok && result.success) {
                modalToHide?.hide();
                showToast(result.message || 'Lưu sự kiện thành công!');
                await loadEventsFromDatabase();
                if (targetDay) {
                    openDayDetailModal(targetDay);
                }
            } else {
                showToast(result.message || 'Lỗi khi lưu sự kiện.', false);
            }
        } catch (error) {
            console.error('Lỗi API event request:', error);
            showToast('Lỗi gửi yêu cầu đến máy chủ.', false);
        }
    }

    // Helper Ẩn/Hiện Ngày kết thúc lặp theo Quy tắc lặp
    function toggleEndRepeatVisibility(repeatSelectId, groupContainerId) {
        const select = document.getElementById(repeatSelectId);
        const group = document.getElementById(groupContainerId);
        if (!select || !group) return;
        if (select.value === 'once') {
            group.classList.add('d-none');
        } else {
            group.classList.remove('d-none');
        }
    }

    const repeatToggleMap = {
        'createHocTapRepeat': 'createHocTapEndRepeatGroup',
        'createTapLuyenRepeat': 'createTapLuyenEndRepeatGroup',
        'createDeadlineRepeat': 'createDeadlineEndRepeatGroup',
        'createCaNhanRepeat': 'createCaNhanEndRepeatGroup',
        'editEventRepeat': 'editEventEndRepeatGroup'
    };

    Object.keys(repeatToggleMap).forEach(selectId => {
        const select = document.getElementById(selectId);
        if (!select) return;
        select.addEventListener('change', () => toggleEndRepeatVisibility(selectId, repeatToggleMap[selectId]));
    });

    // ==========================================================================
    // 6. TẠO SỰ KIỆN (POST /calendar/events)
    // ==========================================================================
    document.getElementById('btnSaveNewEvent')?.addEventListener('click', async function () {
        const activeTab = document.querySelector('#eventTab .nav-link.active');
        const tabId = activeTab ? activeTab.getAttribute('id') : 'hoc-tap-tab';

        let payload = {};
        let targetDay = activeSelectedDay;

        if (tabId === 'hoc-tap-tab') {
            const title = document.getElementById('createHocTapTitle').value.trim();
            if (!title) { showToast('Vui lòng nhập tên môn học.', false); return; }
            const dateStr = document.getElementById('createHocTapDay').value || `${currentYear}-${String(currentMonth).padStart(2, '0')}-${String(activeSelectedDay).padStart(2, '0')}`;
            const parts = dateStr.split('-');
            targetDay = parseInt(parts[2]) || activeSelectedDay;
            const startTime = document.getElementById('createHocTapTime').value || '08:00';
            const endTime = document.getElementById('createHocTapEndTime').value || '10:30';
            const location = document.getElementById('createHocTapLocation').value.trim();
            const repeat = document.getElementById('createHocTapRepeat')?.value || 'weekly';
            const endRepeat = document.getElementById('createHocTapEndRepeat')?.value;

            if (repeat !== 'once' && !endRepeat) {
                showToast('Vui lòng chọn ngày kết thúc lặp.', false);
                return;
            }

            payload = {
                tieu_de: title,
                loai_su_kien: 'hoc_tap',
                thoi_gian_bat_dau: `${dateStr} ${startTime}:00`,
                thoi_gian_ket_thuc: `${dateStr} ${endTime}:00`,
                mo_ta: location,
                bat_thong_bao: document.getElementById('createHocTapBatThongBao')?.checked || false,
                so_ngay_nhac: getRemindTimeValue('createHocTapSoNgayNhac'),
                quy_tac_lap: repeat,
                ngay_ket_thuc_lap: repeat !== 'once' ? endRepeat : null
            };
        } else if (tabId === 'tap-luyen-tab') {
            const title = document.getElementById('createTapLuyenTitle').value.trim();
            if (!title) { showToast('Vui lòng nhập tên buổi tập.', false); return; }
            const dateStr = document.getElementById('createTapLuyenDay').value || `${currentYear}-${String(currentMonth).padStart(2, '0')}-${String(activeSelectedDay).padStart(2, '0')}`;
            const parts = dateStr.split('-');
            targetDay = parseInt(parts[2]) || activeSelectedDay;
            const startTime = document.getElementById('createTapLuyenTime').value || '18:00';
            const endTime = document.getElementById('createTapLuyenEndTime').value || '19:30';
            const location = document.getElementById('createTapLuyenLocation').value.trim();
            const repeat = document.getElementById('createTapLuyenRepeat')?.value || 'weekly';
            const endRepeat = document.getElementById('createTapLuyenEndRepeat')?.value;

            if (repeat !== 'once' && !endRepeat) {
                showToast('Vui lòng chọn ngày kết thúc lặp.', false);
                return;
            }

            payload = {
                tieu_de: title,
                loai_su_kien: 'tap_luyen',
                thoi_gian_bat_dau: `${dateStr} ${startTime}:00`,
                thoi_gian_ket_thuc: `${dateStr} ${endTime}:00`,
                mo_ta: location,
                bat_thong_bao: document.getElementById('createTapLuyenBatThongBao')?.checked || false,
                so_ngay_nhac: getRemindTimeValue('createTapLuyenSoNgayNhac'),
                quy_tac_lap: repeat,
                ngay_ket_thuc_lap: repeat !== 'once' ? endRepeat : null
            };
        } else if (tabId === 'deadline-tab') {
            const title = document.getElementById('createDeadlineTitle').value.trim();
            if (!title) { showToast('Vui lòng nhập tiêu đề deadline.', false); return; }
            const dateStr = document.getElementById('createDeadlineDay').value || `${currentYear}-${String(currentMonth).padStart(2, '0')}-${String(activeSelectedDay).padStart(2, '0')}`;
            const parts = dateStr.split('-');
            targetDay = parseInt(parts[2]) || activeSelectedDay;
            const time = document.getElementById('createDeadlineTime').value || '23:59';
            const location = document.getElementById('createDeadlineLocation').value.trim();
            const repeat = document.getElementById('createDeadlineRepeat')?.value || 'once';
            const endRepeat = document.getElementById('createDeadlineEndRepeat')?.value;

            if (repeat !== 'once' && !endRepeat) {
                showToast('Vui lòng chọn ngày kết thúc lặp.', false);
                return;
            }

            payload = {
                tieu_de: title,
                loai_su_kien: 'deadline',
                thoi_gian_bat_dau: `${dateStr} ${time}:00`,
                mo_ta: location,
                bat_thong_bao: document.getElementById('createDeadlineBatThongBao')?.checked || false,
                so_ngay_nhac: getRemindTimeValue('createDeadlineSoNgayNhac'),
                quy_tac_lap: repeat,
                ngay_ket_thuc_lap: repeat !== 'once' ? endRepeat : null
            };
        } else {
            const title = document.getElementById('createCaNhanTitle').value.trim();
            if (!title) { showToast('Vui lòng nhập tên sự kiện.', false); return; }
            const dateStr = document.getElementById('createCaNhanDay').value || `${currentYear}-${String(currentMonth).padStart(2, '0')}-${String(activeSelectedDay).padStart(2, '0')}`;
            const parts = dateStr.split('-');
            targetDay = parseInt(parts[2]) || activeSelectedDay;
            const time = document.getElementById('createCaNhanTime').value || '14:00';
            const location = document.getElementById('createCaNhanLocation').value.trim();
            const repeat = document.getElementById('createCaNhanRepeat')?.value || 'once';
            const endRepeat = document.getElementById('createCaNhanEndRepeat')?.value;

            if (repeat !== 'once' && !endRepeat) {
                showToast('Vui lòng chọn ngày kết thúc lặp.', false);
                return;
            }

            payload = {
                tieu_de: title,
                loai_su_kien: 'ca_nhan',
                thoi_gian_bat_dau: `${dateStr} ${time}:00`,
                mo_ta: location,
                bat_thong_bao: document.getElementById('createCaNhanBatThongBao')?.checked || false,
                so_ngay_nhac: getRemindTimeValue('createCaNhanSoNgayNhac'),
                quy_tac_lap: repeat,
                ngay_ket_thuc_lap: repeat !== 'once' ? endRepeat : null
            };
        }

        await sendEventRequestWithConflictCheck('/calendar/events', 'POST', payload, createEventModal, targetDay);
    });

    // ==========================================================================
    // 7. SỬA SỰ KIỆN (PUT /calendar/events/{id})
    // ==========================================================================
    function openEditModal(eventId) {
        const evt = eventsList.find(e => e.id === eventId);
        if (!evt) return;

        dayDetailModal?.hide();

        const evtDateStr = evt.start ? evt.start.substring(0, 10) : `${evt.year || currentYear}-${String(evt.month || currentMonth).padStart(2, '0')}-${String(evt.day || 1).padStart(2, '0')}`;

        document.getElementById('editEventId').value = evt.id;
        document.getElementById('editEventType').value = evt.type;
        document.getElementById('editEventTitle').value = evt.title;
        document.getElementById('editEventDay').value = evtDateStr;
        document.getElementById('editEventTime').value = evt.startTime || '08:00';
        document.getElementById('editEventEndTime').value = evt.endTime || '';
        document.getElementById('editEventLocation').value = evt.location || '';

        const editRepeatSel = document.getElementById('editEventRepeat');
        if (editRepeatSel) editRepeatSel.value = evt.quyTacLap || 'once';

        const editEndRepeatInput = document.getElementById('editEventEndRepeat');
        if (editEndRepeatInput) editEndRepeatInput.value = evt.ngayKetThucLap || '';
        toggleEndRepeatVisibility('editEventRepeat', 'editEventEndRepeatGroup');

        const editSw = document.getElementById('editEventBatThongBao');
        const editSel = document.getElementById('editEventSoNgayNhac');
        const editGrp = document.getElementById('editEventRemindGroup');

        if (editSw) editSw.checked = evt.batThongBao || false;
        setRemindTimeValue('editEventSoNgayNhac', evt.soNgayNhac);
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
        const dateStr = document.getElementById('editEventDay').value || `${currentYear}-${String(currentMonth).padStart(2, '0')}-${String(activeSelectedDay).padStart(2, '0')}`;
        const parts = dateStr.split('-');
        const day = parseInt(parts[2]) || activeSelectedDay;
        const startTime = document.getElementById('editEventTime').value || '08:00';
        const endTime = document.getElementById('editEventEndTime').value;
        const location = document.getElementById('editEventLocation').value.trim();
        const repeat = document.getElementById('editEventRepeat')?.value || 'once';
        const endRepeat = document.getElementById('editEventEndRepeat')?.value;

        if (!title) {
            showToast('Vui lòng nhập tiêu đề sự kiện.', false);
            return;
        }

        if (repeat !== 'once' && !endRepeat) {
            showToast('Vui lòng chọn ngày kết thúc lặp.', false);
            return;
        }

        const payload = {
            tieu_de: title,
            loai_su_kien: type,
            thoi_gian_bat_dau: `${dateStr} ${startTime}:00`,
            mo_ta: location,
            bat_thong_bao: document.getElementById('editEventBatThongBao')?.checked || false,
            so_ngay_nhac: getRemindTimeValue('editEventSoNgayNhac'),
            quy_tac_lap: repeat,
            ngay_ket_thuc_lap: repeat !== 'once' ? endRepeat : null
        };

        if (endTime) {
            payload.thoi_gian_ket_thuc = `${dateStr} ${endTime}:00`;
        }

        await sendEventRequestWithConflictCheck(`/calendar/events/${id}`, 'PUT', payload, editEventModal, day);
    });

    // ==========================================================================
    // 7. XÓA SỰ KIỆN (DELETE /calendar/events/{id})
    // ==========================================================================
    let pendingDeleteEventId = null;
    let pendingDeleteDay = null;

    function deleteEvent(eventId, day) {
        const evt = eventsList.find(e => e.id === eventId);
        if (!evt) return;

        if (evt.nhomLapId && evt.quyTacLap && evt.quyTacLap !== 'once') {
            pendingDeleteEventId = eventId;
            pendingDeleteDay = day;

            const titleEl = document.getElementById('deleteEventChoiceTitle');
            const dayEl = document.getElementById('deleteEventChoiceDay');
            if (titleEl) titleEl.textContent = evt.title;
            if (dayEl) dayEl.textContent = `${day}/${String(currentMonth).padStart(2, '0')}`;

            dayDetailModal?.hide();
            deleteChoiceModal?.show();
        } else {
            if (confirm(`Bạn có chắc chắn muốn xóa sự kiện "${evt.title}" không?`)) {
                executeDeleteApi(eventId, day, 'single');
            }
        }
    }

    async function executeDeleteApi(eventId, day, mode = 'single') {
        try {
            const response = await fetch(`/calendar/events/${eventId}?mode=${mode}`, {
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
            executeDeleteApi(pendingDeleteEventId, pendingDeleteDay, 'single');
        }
    });

    document.getElementById('btnDeleteAllOccurrences')?.addEventListener('click', function () {
        if (pendingDeleteEventId && pendingDeleteDay) {
            executeDeleteApi(pendingDeleteEventId, pendingDeleteDay, 'all');
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

    // Nút chuyển đổi Chế độ xem: Tuần / Tháng
    const btnViewModeWeek = document.getElementById('btnViewModeWeek');
    const btnViewModeMonth = document.getElementById('btnViewModeMonth');

    function syncViewModeUI() {
        if (calendarViewMode === 'week') {
            btnViewModeWeek?.classList.add('active');
            btnViewModeMonth?.classList.remove('active');
        } else {
            btnViewModeMonth?.classList.add('active');
            btnViewModeWeek?.classList.remove('active');
        }
    }

    btnViewModeWeek?.addEventListener('click', function () {
        calendarViewMode = 'week';
        syncViewModeUI();
        renderCalendarGrid();
    });

    btnViewModeMonth?.addEventListener('click', function () {
        calendarViewMode = 'month';
        syncViewModeUI();
        renderCalendarGrid();
    });

    syncViewModeUI();

    // Nút Prev/Next (Hỗ trợ chuyển theo Tuần ở Mobile hoặc theo Tháng ở Desktop)
    document.getElementById('btnPrevMonth')?.addEventListener('click', function () {
        if (calendarViewMode === 'week') {
            currentWeekStartDate.setDate(currentWeekStartDate.getDate() - 7);
            activeSelectedDay = currentWeekStartDate.getDate();
            currentMonth = currentWeekStartDate.getMonth() + 1;
            currentYear = currentWeekStartDate.getFullYear();
        } else {
            if (currentMonth === 1) {
                currentMonth = 12;
                currentYear--;
            } else {
                currentMonth--;
            }
        }
        renderCalendarGrid();
    });

    document.getElementById('btnNextMonth')?.addEventListener('click', function () {
        if (calendarViewMode === 'week') {
            currentWeekStartDate.setDate(currentWeekStartDate.getDate() + 7);
            activeSelectedDay = currentWeekStartDate.getDate();
            currentMonth = currentWeekStartDate.getMonth() + 1;
            currentYear = currentWeekStartDate.getFullYear();
        } else {
            if (currentMonth === 12) {
                currentMonth = 1;
                currentYear++;
            } else {
                currentMonth++;
            }
        }
        renderCalendarGrid();
    });

    // Lắng nghe sự kiện thay đổi kích thước màn hình để tự động chuyển Lịch Tuần trên Mobile
    window.addEventListener('resize', function () {
        const isMobile = window.innerWidth < 768;
        if (isMobile && calendarViewMode !== 'week') {
            calendarViewMode = 'week';
            syncViewModeUI();
            renderCalendarGrid();
        }
    });

    // 1. Vẽ lưới lịch ban đầu lập tức khi vừa mở trang
    renderCalendarGrid();

    // 2. Nạp dữ liệu sự kiện thực từ Database
    loadEventsFromDatabase();

    // 3. Kiểm tra tham số ?action=create trên URL để mở modal Thêm sự kiện
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('action') === 'create') {
        setTimeout(() => {
            openCreateModalWithDay(activeSelectedDay || todayDate.getDate());
        }, 200);
    }
});

