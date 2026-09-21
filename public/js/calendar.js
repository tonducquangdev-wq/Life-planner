/* ==========================================================================
   LIFE PLANNER - CALENDAR FIRST MODULE (VANILLA JS ENGINE)
   ========================================================================== */

document.addEventListener('DOMContentLoaded', function() {

    // ==========================================================================
    // 1. STATE DỮ LIỆN LỊCH CHÍNH (MASTER CALENDAR EVENTS DATA STORE)
    // ==========================================================================
    let eventsList = [
        // Ngày 1
        { id: 101, day: 1, isCurrentMonth: true, type: 'hoc-tap', title: '08:00 - 10:30 Lập trình Web & Laravel 13', time: '08:00 - 10:30', startTime: '08:00', endTime: '10:30', location: 'Phòng B2.04', repeatType: 'weekly', repeatLabel: 'Lặp hàng tuần', excludedDays: [] },
        
        // Ngày 2
        { id: 102, day: 2, isCurrentMonth: true, type: 'ca-nhan', title: 'Nghỉ lễ Quốc Khánh 2/9', time: '08:00', startTime: '08:00', endTime: '', location: 'Gia đình', repeatType: 'once', repeatLabel: 'Sự kiện 1 lần', excludedDays: [] },
        
        // Ngày 3
        { id: 103, day: 3, isCurrentMonth: true, type: 'hoc-tap', title: '14:00 - 16:30 Cơ sở dữ liệu nâng cao', time: '14:00 - 16:30', startTime: '14:00', endTime: '16:30', location: 'Phòng A1.02', repeatType: 'weekly', repeatLabel: 'Lặp hàng tuần', excludedDays: [] },
        { id: 104, day: 3, isCurrentMonth: true, type: 'tap-luyen', title: '18:00 - 19:30 Ngực Vai Tay Sau', time: '18:00 - 19:30', startTime: '18:00', endTime: '19:30', location: 'Fitness Center', repeatType: 'weekly', repeatLabel: 'Lặp hàng tuần', excludedDays: [] },
        
        // Ngày 5
        { id: 105, day: 5, isCurrentMonth: true, type: 'tap-luyen', title: '09:00 - 10:00 Chạy bộ 5km công viên', time: '09:00 - 10:00', startTime: '09:00', endTime: '10:00', location: 'Công viên Gia Định', repeatType: 'once', repeatLabel: 'Sự kiện 1 lần', excludedDays: [] },

        // Ngày 7
        { id: 106, day: 7, isCurrentMonth: true, type: 'hoc-tap', title: '08:00 - 10:30 Lập trình Web & Laravel 13', time: '08:00 - 10:30', startTime: '08:00', endTime: '10:30', location: 'Phòng B2.04', repeatType: 'weekly', repeatLabel: 'Lặp hàng tuần', excludedDays: [] },
        
        // Ngày 8
        { id: 107, day: 8, isCurrentMonth: true, type: 'tap-luyen', title: '18:00 - 19:30 Lưng Tay Trước Abs', time: '18:00 - 19:30', startTime: '18:00', endTime: '19:30', location: 'Gym Club', repeatType: 'weekly', repeatLabel: 'Lặp hàng tuần', excludedDays: [] },
        
        // Ngày 9
        { id: 108, day: 9, isCurrentMonth: true, type: 'deadline', title: '23:59 Nộp đồ án PHP & MySQL', time: '23:59', startTime: '23:59', endTime: '', location: 'Hệ thống LMS', repeatType: 'once', repeatLabel: 'Sự kiện 1 lần', excludedDays: [] },

        // Ngày 10
        { id: 109, day: 10, isCurrentMonth: true, type: 'hoc-tap', title: '13:30 - 16:00 Kiểm thử phần mềm', time: '13:30 - 16:00', startTime: '13:30', endTime: '16:00', location: 'Phòng C3.01', repeatType: 'weekly', repeatLabel: 'Lặp hàng tuần', excludedDays: [] },
        
        // Ngày 11
        { id: 110, day: 11, isCurrentMonth: true, type: 'tap-luyen', title: '18:00 - 19:30 Tập Leg Day Chân Bắp Chân', time: '18:00 - 19:30', startTime: '18:00', endTime: '19:30', location: 'Gym Club', repeatType: 'weekly', repeatLabel: 'Lặp hàng tuần', excludedDays: [] },
        
        // Ngày 12
        { id: 111, day: 12, isCurrentMonth: true, type: 'ca-nhan', title: '19:00 - 22:00 Sinh nhật bạn thân', time: '19:00 - 22:00', startTime: '19:00', endTime: '22:00', location: 'Nhà hàng BBQ', repeatType: 'once', repeatLabel: 'Sự kiện 1 lần', excludedDays: [] },

        // Ngày 14 (HÔM NAY - CÓ 6 SỰ KIỆN ĐỂ TEST ĐỦ VIỆC "+3 KHÁC")
        { id: 1, day: 14, isCurrentMonth: true, type: 'hoc-tap', title: '08:00 - 10:30 Lập trình Web & Laravel 13', time: '08:00 - 10:30', startTime: '08:00', endTime: '10:30', location: 'Phòng B2.04 • Thầy Nguyễn Văn A', repeatType: 'weekly', repeatLabel: 'Lặp hàng tuần', excludedDays: [] },
        { id: 2, day: 14, isCurrentMonth: true, type: 'ca-nhan', title: '14:00 - 15:30 Họp nhóm Đồ án Life Planner', time: '14:00 - 15:30', startTime: '14:00', endTime: '15:30', location: 'Google Meet', repeatType: 'once', repeatLabel: 'Sự kiện 1 lần', excludedDays: [] },
        { id: 3, day: 14, isCurrentMonth: true, type: 'tap-luyen', title: '18:00 - 19:30 Ngực Vai Tay Sau (Chest & Shoulders)', time: '18:00 - 19:30', startTime: '18:00', endTime: '19:30', location: 'Fitness Center • Bench Press 4x10', repeatType: 'weekly', repeatLabel: 'Lặp hàng tuần', excludedDays: [] },
        { id: 4, day: 14, isCurrentMonth: true, type: 'ca-nhan', title: '21:00 - 22:00 Đọc sách Clean Code & Refactoring', time: '21:00 - 22:00', startTime: '21:00', endTime: '22:00', location: 'Phòng đọc sách', repeatType: 'once', repeatLabel: 'Sự kiện 1 lần', excludedDays: [] },
        { id: 5, day: 14, isCurrentMonth: true, type: 'deadline', title: '23:59 Nộp Báo cáo Lab 4 PHP Laravel', time: '23:59', startTime: '23:59', endTime: '', location: 'Nộp trên Portal Trường', repeatType: 'once', repeatLabel: 'Sự kiện 1 lần', excludedDays: [] },
        { id: 6, day: 14, isCurrentMonth: true, type: 'hoc-tap', title: '23:59 Ôn tập Kiểm thử phần mềm', time: '23:59', startTime: '23:59', endTime: '', location: 'Tự học online', repeatType: 'weekly', repeatLabel: 'Lặp hàng tuần', excludedDays: [] },

        // Ngày 15
        { id: 112, day: 15, isCurrentMonth: true, type: 'hoc-tap', title: '10:00 - 11:30 Học Tiếng Anh Chuyên Ngành', time: '10:00 - 11:30', startTime: '10:00', endTime: '11:30', location: 'Phòng C1.02', repeatType: 'weekly', repeatLabel: 'Lặp hàng tuần', excludedDays: [] },

        // Ngày 16
        { id: 113, day: 16, isCurrentMonth: true, type: 'deadline', title: '23:59 Nộp bài tập CSDL MySQL', time: '23:59', startTime: '23:59', endTime: '', location: 'LMS Portal', repeatType: 'once', repeatLabel: 'Sự kiện 1 lần', excludedDays: [] },
        { id: 114, day: 16, isCurrentMonth: true, type: 'tap-luyen', title: '18:00 - 18:45 Tập Cardio & HIIT 45p', time: '18:00 - 18:45', startTime: '18:00', endTime: '18:45', location: 'Công viên', repeatType: 'weekly', repeatLabel: 'Lặp hàng tuần', excludedDays: [] },

        // Ngày 17
        { id: 115, day: 17, isCurrentMonth: true, type: 'hoc-tap', title: '08:00 - 10:30 CSDL Nâng cao & Indexing', time: '08:00 - 10:30', startTime: '08:00', endTime: '10:30', location: 'Phòng A2.01', repeatType: 'weekly', repeatLabel: 'Lặp hàng tuần', excludedDays: [] },

        // Ngày 18
        { id: 116, day: 18, isCurrentMonth: true, type: 'deadline', title: '23:59 Nộp Báo cáo Giữa Kỳ Đồ án', time: '23:59', startTime: '23:59', endTime: '', location: 'Portal Trường', repeatType: 'once', repeatLabel: 'Sự kiện 1 lần', excludedDays: [] },

        // Ngày 19
        { id: 117, day: 19, isCurrentMonth: true, type: 'tap-luyen', title: '17:00 - 19:00 Đá bóng giao hữu Khoa CNTT', time: '17:00 - 19:00', startTime: '17:00', endTime: '19:00', location: 'Sân bóng đá Thống Nhất', repeatType: 'once', repeatLabel: 'Sự kiện 1 lần', excludedDays: [] },

        // Ngày 21
        { id: 118, day: 21, isCurrentMonth: true, type: 'deadline', title: '08:00 Thi giữa kỳ Kiểm thử phần mềm', time: '08:00', startTime: '08:00', endTime: '', location: 'Phòng Máy 3', repeatType: 'once', repeatLabel: 'Sự kiện 1 lần', excludedDays: [] },

        // Ngày 22
        { id: 119, day: 22, isCurrentMonth: true, type: 'tap-luyen', title: '18:00 - 19:30 FullBody Gym Workout', time: '18:00 - 19:30', startTime: '18:00', endTime: '19:30', location: 'Gym Club', repeatType: 'weekly', repeatLabel: 'Lặp hàng tuần', excludedDays: [] },

        // Ngày 24
        { id: 120, day: 24, isCurrentMonth: true, type: 'hoc-tap', title: '14:00 - 16:00 Workshop AI & Machine Learning', time: '14:00 - 16:00', startTime: '14:00', endTime: '16:00', location: 'Hội trường A', repeatType: 'once', repeatLabel: 'Sự kiện 1 lần', excludedDays: [] },

        // Ngày 25
        { id: 121, day: 25, isCurrentMonth: true, type: 'tap-luyen', title: '18:00 - 19:00 Tập Yoga giãn cơ', time: '18:00 - 19:00', startTime: '18:00', endTime: '19:00', location: 'Yoga Center', repeatType: 'weekly', repeatLabel: 'Lặp hàng tuần', excludedDays: [] },

        // Ngày 30
        { id: 122, day: 30, isCurrentMonth: true, type: 'ca-nhan', title: '20:00 - 21:00 Tổng kết Mục tiêu Tháng 9', time: '20:00 - 21:00', startTime: '20:00', endTime: '21:00', location: 'Nhà', repeatType: 'once', repeatLabel: 'Sự kiện 1 lần', excludedDays: [] }
    ];

    // Filter state
    let activeFilters = new Set();
    let activeSelectedDay = 14;

    // Modals
    const dayDetailModalEl = document.getElementById('dayDetailModal');
    const dayDetailModal = dayDetailModalEl ? new bootstrap.Modal(dayDetailModalEl) : null;

    const createEventModalEl = document.getElementById('createEventModal');
    const createEventModal = createEventModalEl ? new bootstrap.Modal(createEventModalEl) : null;

    const editEventModalEl = document.getElementById('editEventModal');
    const editEventModal = editEventModalEl ? new bootstrap.Modal(editEventModalEl) : null;

    const deleteChoiceModalEl = document.getElementById('deleteEventChoiceModal');
    const deleteChoiceModal = deleteChoiceModalEl ? new bootstrap.Modal(deleteChoiceModalEl) : null;

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

    const repeatLabelMap = {
        'once': 'Sự kiện 1 lần',
        'weekly': 'Lặp hàng tuần',
        'daily': 'Lặp hàng ngày',
        'monthly': 'Lặp hàng tháng'
    };

    const repeatIconMap = {
        'once': 'bi-calendar-event',
        'weekly': 'bi-repeat',
        'daily': 'bi-arrow-repeat',
        'monthly': 'bi-calendar-month'
    };

    // Monthly days structure (35 cells)
    const monthDaysStructure = [
        { day: 31, isCurrentMonth: false, isToday: false },
        { day: 1, isCurrentMonth: true, isToday: false },
        { day: 2, isCurrentMonth: true, isToday: false },
        { day: 3, isCurrentMonth: true, isToday: false },
        { day: 4, isCurrentMonth: true, isToday: false },
        { day: 5, isCurrentMonth: true, isToday: false },
        { day: 6, isCurrentMonth: true, isToday: false },

        { day: 7, isCurrentMonth: true, isToday: false },
        { day: 8, isCurrentMonth: true, isToday: false },
        { day: 9, isCurrentMonth: true, isToday: false },
        { day: 10, isCurrentMonth: true, isToday: false },
        { day: 11, isCurrentMonth: true, isToday: false },
        { day: 12, isCurrentMonth: true, isToday: false },
        { day: 13, isCurrentMonth: true, isToday: false },

        { day: 14, isCurrentMonth: true, isToday: true }, // HÔM NAY (Thứ 2)
        { day: 15, isCurrentMonth: true, isToday: false },
        { day: 16, isCurrentMonth: true, isToday: false },
        { day: 17, isCurrentMonth: true, isToday: false },
        { day: 18, isCurrentMonth: true, isToday: false },
        { day: 19, isCurrentMonth: true, isToday: false },
        { day: 20, isCurrentMonth: true, isToday: false },

        { day: 21, isCurrentMonth: true, isToday: false },
        { day: 22, isCurrentMonth: true, isToday: false },
        { day: 23, isCurrentMonth: true, isToday: false },
        { day: 24, isCurrentMonth: true, isToday: false },
        { day: 25, isCurrentMonth: true, isToday: false },
        { day: 26, isCurrentMonth: true, isToday: false },
        { day: 27, isCurrentMonth: true, isToday: false },

        { day: 28, isCurrentMonth: true, isToday: false },
        { day: 29, isCurrentMonth: true, isToday: false },
        { day: 30, isCurrentMonth: true, isToday: false },
        { day: 1, isCurrentMonth: false, isToday: false },
        { day: 2, isCurrentMonth: false, isToday: false },
        { day: 3, isCurrentMonth: false, isToday: false },
        { day: 4, isCurrentMonth: false, isToday: false }
    ];

    function disposeTooltips() {
        const tooltips = document.querySelectorAll('.tooltip');
        tooltips.forEach(t => t.remove());
    }

    // Recurrence Engine
    function getEventsForDay(day, isCurrentMonth) {
        if (!isCurrentMonth) return [];

        return eventsList.filter(evt => {
            if (!evt.isCurrentMonth) return false;

            if (Array.isArray(evt.excludedDays) && evt.excludedDays.includes(day)) {
                return false;
            }

            const repeatType = evt.repeatType || 'once';

            if (repeatType === 'once') {
                return evt.day === day;
            } else if (repeatType === 'daily') {
                return true;
            } else if (repeatType === 'weekly') {
                return Math.abs(day - evt.day) % 7 === 0;
            } else if (repeatType === 'monthly') {
                return evt.day === day;
            }
            return evt.day === day;
        });
    }

    // Render monthly grid
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

        let cellsHtml = '';

        monthDaysStructure.forEach(cell => {
            const cellEvents = getEventsForDay(cell.day, cell.isCurrentMonth);

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
                cellsHtml += `
                    <div class="event-pill ${evt.type}" data-event-id="${evt.id}" data-bs-toggle="tooltip" data-bs-placement="top" title="${evt.title} (${evt.time}) • ${evt.location}">
                        <i class="bi ${iconClass} fs-8"></i>
                        <span>${evt.title}</span>
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
            cell.addEventListener('click', function(e) {
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
            modalTitle.textContent = `Chi tiết Lịch Ngày ${day}/09/2026`;
        }

        renderDayDetailEventsList(day);
        dayDetailModal?.show();
    }

    function renderDayDetailEventsList(day) {
        const listContainer = document.getElementById('dayEventsDetailList');
        if (!listContainer) return;

        const dayEvents = getEventsForDay(day, true);
        dayEvents.sort((a, b) => a.time.localeCompare(b.time));

        if (dayEvents.length === 0) {
            listContainer.innerHTML = `
                <div class="text-center py-5">
                    <i class="bi bi-calendar-x text-muted display-4"></i>
                    <h6 class="fw-bold text-dark mt-3 mb-1">Chưa có sự kiện nào cho Ngày ${day}/09/2026</h6>
                    <p class="text-muted fs-7 mb-4">Bạn chưa lên lịch học, lịch tập hay deadline cho ngày này.</p>
                    <button type="button" class="btn btn-primary rounded-pill px-4 btn-sm" id="btnEmptyStateAdd">
                        <i class="bi bi-plus-lg me-1"></i>Thêm sự kiện ngay
                    </button>
                </div>
            `;

            document.getElementById('btnEmptyStateAdd')?.addEventListener('click', function() {
                dayDetailModal?.hide();
                openCreateModalWithDay(day);
            });
            return;
        }

        let html = '';
        dayEvents.forEach(evt => {
            const iconClass = typeIconMap[evt.type] || 'bi-calendar';
            const repeatBadgeClass = evt.repeatType === 'once' ? 'once' : 'weekly';
            const repeatBadgeIcon = repeatIconMap[evt.repeatType] || 'bi-repeat';
            const repeatBadgeText = evt.repeatLabel || repeatLabelMap[evt.repeatType] || 'Lặp hàng tuần';

            html += `
                <div class="day-event-detail-item ${evt.type}">
                    <div class="d-flex align-items-center gap-3 flex-grow-1 min-w-0">
                        <div class="event-icon-badge">
                            <i class="bi ${iconClass}"></i>
                        </div>
                        <div class="text-truncate">
                            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                <span class="fw-bold text-dark fs-6 text-truncate">${evt.title}</span>
                                <span class="repeat-tag ${repeatBadgeClass}">
                                    <i class="bi ${repeatBadgeIcon}"></i>${repeatBadgeText}
                                </span>
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
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const id = parseInt(this.getAttribute('data-edit-id'));
                openEditModal(id);
            });
        });

        listContainer.querySelectorAll('[data-delete-id]').forEach(btn => {
            btn.addEventListener('click', function(e) {
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

        const todayEvents = getEventsForDay(14, true);
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
                        <span class="badge rounded-pill px-2 py-0 fs-8 legend-pill ${evt.type}">${typeLabelMap[evt.type]}</span>
                    </div>
                    <div class="fw-semibold text-dark fs-6 mb-1 text-truncate">${evt.title}</div>
                    <small class="text-muted fs-7"><i class="bi bi-geo-alt me-1"></i>${evt.location}</small>
                </div>
            `;
        });

        todayList.innerHTML = html;
    }

    document.getElementById('btnAddNewFromDayModal')?.addEventListener('click', function() {
        dayDetailModal?.hide();
        openCreateModalWithDay(activeSelectedDay);
    });

    function openCreateModalWithDay(day) {
        document.getElementById('createHocTapDay').value = day;
        document.getElementById('createTapLuyenDay').value = day;
        document.getElementById('createDeadlineDay').value = day;
        document.getElementById('createCaNhanDay').value = day;

        createEventModal?.show();
    }

    document.getElementById('btnSaveNewEvent')?.addEventListener('click', function() {
        const activeTab = document.querySelector('#eventTab .nav-link.active');
        const tabId = activeTab ? activeTab.getAttribute('id') : 'hoc-tap-tab';

        let newEvt = null;
        const newId = Date.now();

        if (tabId === 'hoc-tap-tab') {
            const title = document.getElementById('createHocTapTitle').value.trim() || 'Môn học mới';
            const day = parseInt(document.getElementById('createHocTapDay').value);
            const startTime = document.getElementById('createHocTapTime').value || '08:00';
            const endTime = document.getElementById('createHocTapEndTime').value || '10:30';
            const timeRange = `${startTime} - ${endTime}`;
            const location = document.getElementById('createHocTapLocation').value.trim() || 'Phòng học';
            const repeatType = document.getElementById('createHocTapRepeat').value || 'weekly';

            newEvt = {
                id: newId,
                day: day,
                isCurrentMonth: true,
                type: 'hoc-tap',
                title: `${timeRange} ${title}`,
                time: timeRange,
                startTime: startTime,
                endTime: endTime,
                location: location,
                repeatType: repeatType,
                repeatLabel: repeatLabelMap[repeatType] || 'Lặp hàng tuần',
                excludedDays: []
            };
        } else if (tabId === 'tap-luyen-tab') {
            const title = document.getElementById('createTapLuyenTitle').value.trim() || 'Buổi tập mới';
            const day = parseInt(document.getElementById('createTapLuyenDay').value);
            const startTime = document.getElementById('createTapLuyenTime').value || '18:00';
            const endTime = document.getElementById('createTapLuyenEndTime').value || '19:30';
            const timeRange = `${startTime} - ${endTime}`;
            const location = document.getElementById('createTapLuyenLocation').value.trim() || 'Gym Club';
            const repeatType = document.getElementById('createTapLuyenRepeat').value || 'weekly';

            newEvt = {
                id: newId,
                day: day,
                isCurrentMonth: true,
                type: 'tap-luyen',
                title: `${timeRange} ${title}`,
                time: timeRange,
                startTime: startTime,
                endTime: endTime,
                location: location,
                repeatType: repeatType,
                repeatLabel: repeatLabelMap[repeatType] || 'Lặp hàng tuần',
                excludedDays: []
            };
        } else if (tabId === 'deadline-tab') {
            const title = document.getElementById('createDeadlineTitle').value.trim() || 'Deadline mới';
            const day = parseInt(document.getElementById('createDeadlineDay').value);
            const time = document.getElementById('createDeadlineTime').value || '23:59';
            const location = document.getElementById('createDeadlineLocation').value.trim() || 'Nộp online';
            const repeatType = document.getElementById('createDeadlineRepeat').value || 'once';

            newEvt = {
                id: newId,
                day: day,
                isCurrentMonth: true,
                type: 'deadline',
                title: `${time} ${title}`,
                time: time,
                startTime: time,
                endTime: '',
                location: location,
                repeatType: repeatType,
                repeatLabel: repeatLabelMap[repeatType] || 'Sự kiện 1 lần',
                excludedDays: []
            };
        } else {
            const title = document.getElementById('createCaNhanTitle').value.trim() || 'Sự kiện cá nhân';
            const day = parseInt(document.getElementById('createCaNhanDay').value);
            const time = document.getElementById('createCaNhanTime').value || '14:00';
            const location = document.getElementById('createCaNhanLocation').value.trim() || 'Địa điểm';
            const repeatType = document.getElementById('createCaNhanRepeat').value || 'once';

            newEvt = {
                id: newId,
                day: day,
                isCurrentMonth: true,
                type: 'ca-nhan',
                title: `${time} ${title}`,
                time: time,
                startTime: time,
                endTime: '',
                location: location,
                repeatType: repeatType,
                repeatLabel: repeatLabelMap[repeatType] || 'Sự kiện 1 lần',
                excludedDays: []
            };
        }

        eventsList.push(newEvt);
        createEventModal?.hide();

        renderCalendarGrid();
        openDayDetailModal(newEvt.day);
    });

    function openEditModal(eventId) {
        const evt = eventsList.find(e => e.id === eventId);
        if (!evt) return;

        dayDetailModal?.hide();

        document.getElementById('editEventId').value = evt.id;
        document.getElementById('editEventType').value = evt.type;
        document.getElementById('editEventTitle').value = evt.title;
        document.getElementById('editEventDay').value = evt.day;
        document.getElementById('editEventTime').value = evt.startTime || (evt.time ? evt.time.split(' - ')[0] : '08:00');
        document.getElementById('editEventEndTime').value = evt.endTime || (evt.time ? (evt.time.split(' - ')[1] || '') : '');
        document.getElementById('editEventRepeat').value = evt.repeatType || 'weekly';
        document.getElementById('editEventLocation').value = evt.location;

        editEventModal?.show();
    }

    document.getElementById('btnUpdateEvent')?.addEventListener('click', function() {
        const id = parseInt(document.getElementById('editEventId').value);
        const evtIndex = eventsList.findIndex(e => e.id === id);

        if (evtIndex !== -1) {
            const type = document.getElementById('editEventType').value;
            const title = document.getElementById('editEventTitle').value;
            const day = parseInt(document.getElementById('editEventDay').value);
            const startTime = document.getElementById('editEventTime').value;
            const endTime = document.getElementById('editEventEndTime').value;
            const timeRange = endTime ? `${startTime} - ${endTime}` : startTime;
            const location = document.getElementById('editEventLocation').value;
            const repeatType = document.getElementById('editEventRepeat').value || 'weekly';

            eventsList[evtIndex] = {
                ...eventsList[evtIndex],
                type,
                title,
                day,
                time: timeRange,
                startTime,
                endTime,
                location,
                repeatType,
                repeatLabel: repeatLabelMap[repeatType] || 'Sự kiện'
            };
        }

        editEventModal?.hide();
        renderCalendarGrid();
        openDayDetailModal(eventsList[evtIndex].day);
    });

    // Delete handling
    let pendingDeleteEventId = null;
    let pendingDeleteDay = null;

    function deleteEvent(eventId, day) {
        const evt = eventsList.find(e => e.id === eventId);
        if (!evt) return;

        const repeatType = evt.repeatType || 'once';

        if (repeatType === 'once') {
            if (confirm(`Bạn có chắc chắn muốn xóa sự kiện "${evt.title}" không?`)) {
                eventsList = eventsList.filter(e => e.id !== eventId);
                renderCalendarGrid();
                renderDayDetailEventsList(day);
            }
        } else {
            pendingDeleteEventId = eventId;
            pendingDeleteDay = day;

            const titleEl = document.getElementById('deleteEventChoiceTitle');
            const dayEl = document.getElementById('deleteEventChoiceDay');

            if (titleEl) titleEl.textContent = evt.title;
            if (dayEl) dayEl.textContent = day;

            deleteChoiceModal?.show();
        }
    }

    document.getElementById('btnDeleteOnlyThisOccurrence')?.addEventListener('click', function() {
        if (!pendingDeleteEventId || !pendingDeleteDay) return;

        const evt = eventsList.find(e => e.id === pendingDeleteEventId);
        if (evt) {
            evt.excludedDays = evt.excludedDays || [];
            if (!evt.excludedDays.includes(pendingDeleteDay)) {
                evt.excludedDays.push(pendingDeleteDay);
            }
        }

        deleteChoiceModal?.hide();
        renderCalendarGrid();
        renderDayDetailEventsList(pendingDeleteDay);
    });

    document.getElementById('btnDeleteAllOccurrences')?.addEventListener('click', function() {
        if (!pendingDeleteEventId) return;

        eventsList = eventsList.filter(e => e.id !== pendingDeleteEventId);

        deleteChoiceModal?.hide();
        renderCalendarGrid();
        renderDayDetailEventsList(pendingDeleteDay);
    });

    // Filter events
    const allBtn = document.querySelector('#calendarFilterGroup .legend-btn[data-filter="all"]');
    const categoryButtons = document.querySelectorAll('#calendarFilterGroup .legend-btn:not([data-filter="all"])');
    const filterContainer = document.getElementById('calendarFilterGroup');

    allBtn?.addEventListener('click', function() {
        activeFilters.clear();
        applyFilterStyles();
        renderCalendarGrid();
    });

    categoryButtons.forEach(btn => {
        btn.addEventListener('click', function() {
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

    // Search filter
    document.getElementById('searchCalendar')?.addEventListener('input', function(e) {
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

    // Mark all notifications read
    document.getElementById('btnMarkAllRead')?.addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelectorAll('.notification-item.unread').forEach(item => {
            item.classList.remove('unread');
            item.classList.add('read', 'opacity-75');
            item.querySelector('.notif-dot')?.remove();
        });
        const notifBadgeDot = document.getElementById('notifBadgeDot');
        if (notifBadgeDot) notifBadgeDot.style.display = 'none';

        const countBadge = document.getElementById('notifCountBadge');
        if (countBadge) countBadge.textContent = '0 mới';
    });

    // Initial render
    renderCalendarGrid();
});
