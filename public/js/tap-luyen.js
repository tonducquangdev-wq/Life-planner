/**
 * ==========================================================================
 * SCRIPT ĐIỀU KHIỂN CHỨC NĂNG THỂ CHẤT & TẬP LUYỆN - STUDENT-LIFE
 * Dữ liệu thật 100% từ Database - Custom Workout Schedule (Production-ready)
 * ==========================================================================
 */

document.addEventListener('DOMContentLoaded', function () {
    const config = window.FITNESS_CONFIG || {
        currentMonth: new Date().getMonth() + 1,
        currentYear: new Date().getFullYear(),
        currentType: 'Push',
        currentBuoiTapId: null,
        activePlanId: null,
        danhSachBaiTap: [],
        buoiTapList: [],
        workoutPlansByType: {},
        lichTuan: {},
        monthlyHeatmap: {},
        completeRoute: '/tap-luyen/hoan-thanh',
        updateWorkoutRoute: '/tap-luyen/cap-nhat-buoi-tap',
        createPlanRoute: '/tap-luyen/ke-hoach',
        activatePlanUrl: '/tap-luyen/ke-hoach',
        deletePlanUrl: '/tap-luyen/ke-hoach',
        createSessionRoute: '/tap-luyen/buoi-tap',
        deleteSessionUrl: '/tap-luyen/buoi-tap'
    };

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    // 1. Quản lý trạng thái buổi tập hiện tại
    let currentWorkout = {
        type: config.currentType || 'Push',
        title: config.currentType || 'Buổi rèn luyện',
        buoiTapId: config.currentBuoiTapId,
        ngayTrongTuan: null,
        exercises: Array.isArray(config.danhSachBaiTap) ? [...config.danhSachBaiTap] : []
    };

    // Tìm thông tin ban đầu nếu có trong buoiTapList
    if (config.buoiTapList && config.buoiTapList.length > 0) {
        const found = config.buoiTapList.find(b => b.id === currentWorkout.buoiTapId) || config.buoiTapList[0];
        if (found) {
            currentWorkout.buoiTapId = found.id;
            currentWorkout.type = found.title;
            currentWorkout.title = found.title;
            currentWorkout.ngayTrongTuan = found.ngay_trong_tuan;
            if (!currentWorkout.exercises || currentWorkout.exercises.length === 0) {
                currentWorkout.exercises = Array.isArray(found.exercises) ? [...found.exercises] : [];
            }
        }
    }

    let editingWorkout = JSON.parse(JSON.stringify(currentWorkout));
    let currentExerciseIndex = 0;

    // 2. Các phần tử DOM
    const timerDisplay = document.getElementById('workout-timer');
    const toggleTimerBtn = document.getElementById('toggle-timer-btn');
    const toggleTimerIcon = document.getElementById('toggle-timer-icon');
    const toggleTimerText = document.getElementById('toggle-timer-text');
    const nextExBtn = document.getElementById('next-ex-btn');
    const finishExBtn = document.getElementById('finish-ex-btn');
    const statusBadge = document.getElementById('workout-status-badge');
    const statusText = document.getElementById('workout-status-text');
    const activeExName = document.getElementById('active-ex-name');
    const activeExSetRep = document.getElementById('active-ex-set-rep');
    const emptyBanner = document.getElementById('exercise-empty-banner');
    const titleDisplay = document.getElementById('workout-title-display');
    const workoutTabsContainer = document.getElementById('workout-tabs-container');

    // Hàm định dạng thông số bài tập theo đúng chuẩn nghiệp vụ (Strength / Core / Cardio / Khác)
    function formatExerciseMetric(ex) {
        if (!ex) return '-- x --';
        const type = (ex.type || 'strength').toLowerCase();
        const unit = (ex.duration_unit === 'giay') ? 'giây' : 'phút';

        if (type === 'cardio') {
            const duration = ex.duration || 30;
            return `${duration} ${unit}`;
        }

        if (type === 'core') {
            if (ex.duration) {
                if (ex.sets && parseInt(ex.sets) > 1) {
                    return `${ex.sets} sets × ${ex.duration} ${unit}`;
                }
                return `${ex.duration} ${unit}`;
            }
            const sets = ex.sets || 3;
            const reps = ex.reps || '10-12';
            return `${sets} sets × ${reps} reps`;
        }

        // Strength & Other mặc định
        if (ex.duration && !ex.reps) {
            return `${ex.duration} ${unit}`;
        }
        const sets = ex.sets || 3;
        const reps = ex.reps || '8-12';
        return `${sets} sets × ${reps} reps`;
    }

    // 3. Render giao diện Trình tập luyện (Workout Player)
    function renderPlayer() {
        // Cập nhật tab active
        const tabs = workoutTabsContainer?.querySelectorAll('.workout-tab') || [];
        tabs.forEach(tab => {
            const tabId = tab.dataset.id ? parseInt(tab.dataset.id) : null;
            const tabType = tab.dataset.type;

            if ((tabId && currentWorkout.buoiTapId && tabId === currentWorkout.buoiTapId) ||
                (tabType && tabType.toLowerCase() === currentWorkout.type.toLowerCase())) {
                tab.classList.add('active');
            } else {
                tab.classList.remove('active');
            }
        });

        // Cập nhật tiêu đề buổi tập
        if (titleDisplay) {
            titleDisplay.textContent = currentWorkout.title || currentWorkout.type;
        }

        // Cập nhật bài tập
        const exercises = currentWorkout.exercises;
        const activeExTypeBadge = document.getElementById('active-ex-type-badge');

        if (exercises && exercises.length > 0) {
            if (currentExerciseIndex >= exercises.length) {
                currentExerciseIndex = 0;
            }
            const ex = exercises[currentExerciseIndex];
            const type = (ex.type || 'strength').toLowerCase();

            if (activeExName) {
                activeExName.textContent = `${currentExerciseIndex + 1}. ${ex.name}`;
                activeExName.classList.remove('text-muted');
                activeExName.classList.add('text-dark');
            }

            if (activeExTypeBadge) {
                activeExTypeBadge.classList.remove('d-none');
                activeExTypeBadge.className = `badge exercise-type-badge badge-type-${type} px-3 py-1 rounded-pill small`;
                if (type === 'cardio') {
                    activeExTypeBadge.innerHTML = '<i class="bi bi-heart-pulse-fill me-1"></i>Cardio';
                } else if (type === 'core') {
                    activeExTypeBadge.innerHTML = '<i class="bi bi-bullseye me-1"></i>Core';
                } else if (type === 'strength') {
                    activeExTypeBadge.innerHTML = '<i class="bi bi-lightning-fill me-1"></i>Strength';
                } else {
                    activeExTypeBadge.innerHTML = '<i class="bi bi-sliders me-1"></i>Khác';
                }
            }

            if (activeExSetRep) {
                activeExSetRep.textContent = formatExerciseMetric(ex);
            }

            if (emptyBanner) {
                emptyBanner.classList.add('d-none');
            }
        } else {
            if (activeExName) {
                activeExName.textContent = 'Chưa có bài tập trong buổi này';
                activeExName.classList.remove('text-dark');
                activeExName.classList.add('text-muted');
            }
            if (activeExTypeBadge) {
                activeExTypeBadge.classList.add('d-none');
            }
            if (activeExSetRep) {
                activeExSetRep.textContent = '-- x --';
            }
            if (emptyBanner) {
                emptyBanner.classList.remove('d-none');
            }
        }
    }

    // Chuyển sang buổi tập bất kỳ
    function switchWorkoutSession(buoiTapId, title) {
        currentExerciseIndex = 0;
        let session = null;

        if (config.buoiTapList && config.buoiTapList.length > 0) {
            if (buoiTapId) {
                session = config.buoiTapList.find(b => b.id === parseInt(buoiTapId));
            }
            if (!session && title) {
                session = config.buoiTapList.find(b => b.title.toLowerCase() === title.toLowerCase());
            }
        }

        if (session) {
            currentWorkout.buoiTapId = session.id;
            currentWorkout.type = session.title;
            currentWorkout.title = session.title;
            currentWorkout.ngayTrongTuan = session.ngay_trong_tuan;
            currentWorkout.exercises = Array.isArray(session.exercises) ? [...session.exercises] : [];
        } else {
            currentWorkout.buoiTapId = buoiTapId || null;
            currentWorkout.type = title || 'Tập tự do';
            currentWorkout.title = title || 'Tập tự do';
            currentWorkout.exercises = [];
        }

        const modalBuoiTapId = document.getElementById('modal-buoi-tap-id');
        if (modalBuoiTapId) modalBuoiTapId.value = currentWorkout.buoiTapId || '';

        renderPlayer();
    }

    // Bắt sự kiện click vào các Tab buổi tập
    function bindWorkoutTabEvents() {
        const tabs = workoutTabsContainer?.querySelectorAll('.workout-tab') || [];
        tabs.forEach(tab => {
            tab.addEventListener('click', function () {
                const id = this.dataset.id ? parseInt(this.dataset.id) : null;
                const type = this.dataset.type;
                switchWorkoutSession(id, type);
            });
        });
    }
    bindWorkoutTabEvents();

    // Bắt sự kiện click vào các Pill ngày trong tuần (chuyển nhanh sang buổi tập ngày đó)
    document.querySelectorAll('#weekly-schedule-pills .schedule-day-pill').forEach(pill => {
        pill.addEventListener('click', function () {
            const buoiTapId = this.dataset.buoiTapId;
            const name = this.dataset.name;
            if (buoiTapId) {
                switchWorkoutSession(parseInt(buoiTapId), name);
                window.scrollTo({ top: 200, behavior: 'smooth' });
            }
        });
    });

    // 4. Đồng hồ đếm thời gian tập luyện (Timer) - Tuân thủ 4 trạng thái
    let timerInterval = null;
    let timerSeconds = 0;
    let timerState = 'NOT_STARTED'; // 'NOT_STARTED', 'RUNNING', 'PAUSED', 'COMPLETED'

    function formatTime(totalSeconds) {
        const h = Math.floor(totalSeconds / 3600).toString().padStart(2, '0');
        const m = Math.floor((totalSeconds % 3600) / 60).toString().padStart(2, '0');
        const s = (totalSeconds % 60).toString().padStart(2, '0');
        return `${h}:${m}:${s}`;
    }

    function updateStatusUI(state) {
        timerState = state;
        if (!statusBadge || !statusText) return;

        statusBadge.className = 'badge workout-status-badge px-3 py-2 rounded-pill shadow-xs border';

        if (state === 'NOT_STARTED') {
            statusBadge.classList.add('badge-not-started');
            statusText.textContent = 'Chưa bắt đầu';
            if (toggleTimerIcon) toggleTimerIcon.className = 'bi bi-play-fill fs-5 text-primary';
            if (toggleTimerText) toggleTimerText.textContent = 'Bắt đầu tập';
        } else if (state === 'RUNNING') {
            statusBadge.classList.add('badge-running');
            statusText.textContent = 'Đang tập luyện';
            if (toggleTimerIcon) toggleTimerIcon.className = 'bi bi-pause-fill fs-5 text-warning';
            if (toggleTimerText) toggleTimerText.textContent = 'Tạm dừng';
        } else if (state === 'PAUSED') {
            statusBadge.classList.add('badge-paused');
            statusText.textContent = 'Tạm dừng';
            if (toggleTimerIcon) toggleTimerIcon.className = 'bi bi-play-fill fs-5 text-primary';
            if (toggleTimerText) toggleTimerText.textContent = 'Tiếp tục tập';
        } else if (state === 'COMPLETED') {
            statusBadge.classList.add('badge-completed');
            statusText.textContent = 'Đã hoàn thành';
            if (toggleTimerIcon) toggleTimerIcon.className = 'bi bi-arrow-repeat fs-5 text-primary';
            if (toggleTimerText) toggleTimerText.textContent = 'Tập buổi mới';
        }
    }

    function startTimer() {
        if (timerInterval) clearInterval(timerInterval);
        updateStatusUI('RUNNING');

        timerInterval = setInterval(() => {
            timerSeconds++;
            if (timerDisplay) {
                timerDisplay.textContent = formatTime(timerSeconds);
            }
        }, 1000);
    }

    function pauseTimer() {
        if (timerInterval) clearInterval(timerInterval);
        timerInterval = null;
        updateStatusUI('PAUSED');
    }

    function stopAndFinishTimer() {
        if (timerInterval) clearInterval(timerInterval);
        timerInterval = null;
        updateStatusUI('COMPLETED');
    }

    function resetTimer() {
        if (timerInterval) clearInterval(timerInterval);
        timerInterval = null;
        timerSeconds = 0;
        if (timerDisplay) {
            timerDisplay.textContent = '00:00:00';
        }
        updateStatusUI('NOT_STARTED');
    }

    if (toggleTimerBtn) {
        toggleTimerBtn.addEventListener('click', function () {
            if (timerState === 'NOT_STARTED' || timerState === 'PAUSED') {
                startTimer();
            } else if (timerState === 'RUNNING') {
                pauseTimer();
            } else if (timerState === 'COMPLETED') {
                resetTimer();
                startTimer();
            }
        });
    }

    if (nextExBtn) {
        nextExBtn.addEventListener('click', function () {
            if (currentWorkout.exercises && currentWorkout.exercises.length > 0) {
                currentExerciseIndex = (currentExerciseIndex + 1) % currentWorkout.exercises.length;
                renderPlayer();
            }
        });
    }

    if (finishExBtn) {
        finishExBtn.addEventListener('click', function () {
            const elapsed = timerSeconds;

            if (timerState === 'NOT_STARTED' && elapsed === 0) {
                alert('Vui lòng bấm "Bắt đầu tập" để bắt đầu tính giờ trước khi hoàn thành buổi tập.');
                return;
            }

            stopAndFinishTimer();
            saveWorkoutSession(elapsed);
        });
    }

    function saveWorkoutSession(seconds) {
        const durationMinutes = Math.max(1, Math.round(seconds / 60));

        const payload = {
            buoi_tap_id: currentWorkout.buoiTapId,
            ten_buoi_tap: currentWorkout.title || currentWorkout.type,
            seconds: seconds,
            tong_thoi_luong: durationMinutes,
            ghi_chu: `Hoàn thành buổi tập ${currentWorkout.title || currentWorkout.type}`
        };

        fetch(config.completeRoute, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken || ''
            },
            body: JSON.stringify(payload)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Không thể lưu buổi tập. Mã lỗi: ' + response.status);
            }
            return response.json();
        })
        .then(res => {
            if (res.success) {
                const kpiDuration = document.getElementById('kpi-duration');
                const kpiWeek = document.getElementById('kpi-week');
                const kpiTotal = document.getElementById('kpi-total');

                if (kpiDuration && res.data.thoiGianTapHomNay !== undefined) {
                    kpiDuration.innerHTML = `${res.data.thoiGianTapHomNay} <span class="fs-6 fw-normal text-muted">phút</span>`;
                }
                if (kpiWeek && res.data.buoiTapTuanNay !== undefined) {
                    kpiWeek.innerHTML = `${res.data.buoiTapTuanNay} <span class="fs-6 fw-normal text-muted">buổi</span>`;
                }
                if (kpiTotal && res.data.tongBuoiTap !== undefined) {
                    kpiTotal.innerHTML = `${res.data.tongBuoiTap} <span class="fs-6 fw-normal text-muted">buổi</span>`;
                }

                addRecentActivityUI(res.data);
                updateTodayHeatmap(durationMinutes);
                showCompletionToast(durationMinutes);
            }
        })
        .catch(err => {
            console.error('Lỗi khi lưu buổi tập:', err);
            alert(`🎉 Bạn đã hoàn thành buổi tập ${durationMinutes} phút! (Lưu ý: Không thể đồng bộ trực tiếp tới máy chủ: ${err.message})`);
        });
    }

    function addRecentActivityUI(data) {
        const container = document.getElementById('recent-activities-container');
        if (!container) return;

        const emptyBox = container.querySelector('.empty-activity-box');
        if (emptyBox) {
            container.innerHTML = '<div class="d-flex flex-column gap-3" id="history-items-list"></div>';
        }

        const historyList = document.getElementById('history-items-list');
        if (!historyList) return;

        const newHtml = `
            <div class="d-flex align-items-center justify-content-between p-2 rounded-3 border-bottom pb-3 activity-record-item border-primary-subtle bg-primary-subtle-10">
                <div class="d-flex align-items-center gap-3">
                    <div class="metric-icon-box bg-primary-subtle text-primary">
                        <i class="bi bi-activity"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark fs-6">${data.ten || 'Buổi tập'}</div>
                        <small class="text-muted">${data.thoi_gian} • Vừa xong</small>
                    </div>
                </div>
                <div>
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Vừa xong</span>
                </div>
            </div>
        `;
        historyList.insertAdjacentHTML('afterbegin', newHtml);
    }

    function showCompletionToast(minutes) {
        const alertBox = document.createElement('div');
        alertBox.className = 'alert alert-success alert-dismissible fade show position-fixed bottom-0 end-0 m-3 shadow-lg rounded-4 z-3';
        alertBox.style.maxWidth = '380px';
        alertBox.innerHTML = `
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-trophy-fill text-warning fs-3"></i>
                <div>
                    <strong class="d-block">Xuất sắc hoàn thành!</strong>
                    <small>Đã ghi nhận ${minutes} phút tập luyện vào lịch sử thực tế.</small>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        document.body.appendChild(alertBox);
        setTimeout(() => {
            alertBox.classList.remove('show');
            setTimeout(() => alertBox.remove(), 300);
        }, 5000);
    }

    document.getElementById('quick-start-workout-btn')?.addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: 'smooth' });
        startTimer();
    });

    // 6. Modal Chỉnh sửa / Thêm bài tập
    const editWorkoutModal = document.getElementById('editWorkoutModal');
    const modalExerciseList = document.getElementById('modal-exercise-list');
    const exerciseCountBadge = document.getElementById('exercise-count-badge');
    const modalBuoiTapId = document.getElementById('modal-buoi-tap-id');
    const modalWorkoutTitle = document.getElementById('modal-workout-title');
    const modalWorkoutDay = document.getElementById('modal-workout-day');

    const newExType = document.getElementById('new-ex-type');
    const newExName = document.getElementById('new-ex-name');
    const coreMeasureSelector = document.getElementById('core-measure-selector');
    const fieldGroupSets = document.getElementById('field-group-sets');
    const fieldGroupReps = document.getElementById('field-group-reps');
    const fieldGroupDuration = document.getElementById('field-group-duration');
    const fieldGroupUnit = document.getElementById('field-group-unit');
    const newExSets = document.getElementById('new-ex-sets');
    const newExReps = document.getElementById('new-ex-reps');
    const newExDuration = document.getElementById('new-ex-duration');
    const newExUnit = document.getElementById('new-ex-unit');
    const modeReps = document.getElementById('mode-reps');
    const modeDuration = document.getElementById('mode-duration');
    const saveWorkoutBtn = document.getElementById('save-workout-btn');
    const saveWorkoutSpinner = document.getElementById('save-workout-spinner');
    const saveWorkoutIcon = document.getElementById('save-workout-icon');

    function updateModalFieldVisibility() {
        const type = (newExType ? newExType.value : 'strength').toLowerCase();

        if (type === 'strength') {
            coreMeasureSelector?.classList.add('d-none');
            fieldGroupSets?.classList.remove('d-none');
            fieldGroupReps?.classList.remove('d-none');
            fieldGroupDuration?.classList.add('d-none');
            fieldGroupUnit?.classList.add('d-none');
            if (newExSets) newExSets.placeholder = 'Số sets (VD: 3 hoặc 4)';
            if (newExReps) newExReps.placeholder = 'Reps (VD: 8-10 hoặc 8-12)';
        } else if (type === 'cardio') {
            coreMeasureSelector?.classList.add('d-none');
            fieldGroupSets?.classList.add('d-none');
            fieldGroupReps?.classList.add('d-none');
            fieldGroupDuration?.classList.remove('d-none');
            fieldGroupUnit?.classList.remove('d-none');
            if (newExDuration) newExDuration.placeholder = 'Thời lượng (VD: 30)';
            if (newExUnit) newExUnit.value = 'phut';
        } else {
            coreMeasureSelector?.classList.remove('d-none');
            const isDurationMode = modeDuration && modeDuration.checked;
            if (isDurationMode) {
                fieldGroupSets?.classList.remove('d-none');
                fieldGroupReps?.classList.add('d-none');
                fieldGroupDuration?.classList.remove('d-none');
                fieldGroupUnit?.classList.remove('d-none');
                if (newExSets) newExSets.placeholder = 'Sets (VD: 3, hoặc để trống)';
                if (newExDuration) newExDuration.placeholder = 'Thời gian (VD: 30 hoặc 60)';
            } else {
                fieldGroupSets?.classList.remove('d-none');
                fieldGroupReps?.classList.remove('d-none');
                fieldGroupDuration?.classList.add('d-none');
                fieldGroupUnit?.classList.add('d-none');
                if (newExSets) newExSets.placeholder = 'Số sets (VD: 3)';
                if (newExReps) newExReps.placeholder = 'Reps (VD: 10-12 hoặc 15)';
            }
        }
    }

    if (newExType) newExType.addEventListener('change', updateModalFieldVisibility);
    if (modeReps) modeReps.addEventListener('change', updateModalFieldVisibility);
    if (modeDuration) modeDuration.addEventListener('change', updateModalFieldVisibility);

    function renderModalList() {
        if (!modalExerciseList) return;
        modalExerciseList.innerHTML = '';

        const exercises = editingWorkout.exercises || [];
        if (exerciseCountBadge) {
            exerciseCountBadge.textContent = `${exercises.length} bài tập`;
        }

        if (exercises.length === 0) {
            modalExerciseList.innerHTML = '<div class="text-center text-muted py-3 small bg-white rounded-3 border border-dashed">Chưa có bài tập nào. Hãy thêm bài tập mới bên trên.</div>';
            return;
        }

        exercises.forEach((ex, idx) => {
            const row = document.createElement('div');
            row.className = 'd-flex align-items-center justify-content-between bg-white p-2 px-3 rounded-3 border exercise-item-row shadow-2xs';

            const type = (ex.type || 'strength').toLowerCase();
            const metricText = formatExerciseMetric(ex);

            let typeBadgeHtml = '';
            if (type === 'cardio') {
                typeBadgeHtml = '<span class="badge badge-type-cardio rounded-pill px-2 py-1 fs-8">Cardio</span>';
            } else if (type === 'core') {
                typeBadgeHtml = '<span class="badge badge-type-core rounded-pill px-2 py-1 fs-8">Core</span>';
            } else if (type === 'strength') {
                typeBadgeHtml = '<span class="badge badge-type-strength rounded-pill px-2 py-1 fs-8">Strength</span>';
            } else {
                typeBadgeHtml = '<span class="badge badge-type-other rounded-pill px-2 py-1 fs-8">Khác</span>';
            }

            row.innerHTML = `
                <div class="d-flex align-items-center gap-2 flex-grow-1 text-truncate me-2">
                    <span class="badge bg-light text-secondary border rounded-circle d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 0.75rem;">${idx + 1}</span>
                    ${typeBadgeHtml}
                    <span class="fw-semibold text-dark text-truncate small">${ex.name}</span>
                    <span class="badge bg-light text-dark border small ms-auto fw-medium">${metricText}</span>
                </div>
                <button type="button" class="btn btn-sm btn-light border rounded-circle text-danger delete-modal-ex-btn p-1 px-2" data-index="${idx}" title="Xóa bài">
                    <i class="bi bi-trash"></i>
                </button>
            `;
            modalExerciseList.appendChild(row);
        });

        modalExerciseList.querySelectorAll('.delete-modal-ex-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const idx = parseInt(this.dataset.index);
                editingWorkout.exercises.splice(idx, 1);
                editingWorkout.exercises.forEach((item, i) => {
                    item.order = i + 1;
                });
                renderModalList();
            });
        });
    }

    if (editWorkoutModal) {
        editWorkoutModal.addEventListener('show.bs.modal', function () {
            editingWorkout = JSON.parse(JSON.stringify(currentWorkout));

            if (modalBuoiTapId) modalBuoiTapId.value = editingWorkout.buoiTapId || '';
            if (modalWorkoutTitle) modalWorkoutTitle.value = editingWorkout.title || '';
            if (modalWorkoutDay) modalWorkoutDay.value = editingWorkout.ngayTrongTuan || '';

            renderModalList();

            if (newExType) newExType.value = 'strength';
            if (newExName) newExName.value = '';
            if (newExSets) newExSets.value = '3';
            if (newExReps) newExReps.value = '8-12';
            if (newExDuration) newExDuration.value = '';
            if (newExUnit) newExUnit.value = 'phut';
            if (modeReps) modeReps.checked = true;

            updateModalFieldVisibility();
        });
    }

    document.getElementById('add-exercise-btn')?.addEventListener('click', function () {
        const type = (newExType ? newExType.value : 'strength').toLowerCase();
        const name = newExName?.value.trim();

        if (!name) {
            alert('Vui lòng nhập Tên bài tập.');
            newExName?.focus();
            return;
        }

        let newExercise = {
            name: name,
            type: type,
            sets: null,
            reps: null,
            duration: null,
            duration_unit: newExUnit ? newExUnit.value : 'phut',
            order: (editingWorkout.exercises || []).length + 1
        };

        if (type === 'strength') {
            const sets = parseInt(newExSets?.value.trim());
            const reps = newExReps?.value.trim();

            if (!sets || sets <= 0) {
                alert('Vui lòng nhập Số sets hợp lệ cho bài tập Strength.');
                newExSets?.focus();
                return;
            }
            if (!reps) {
                alert('Vui lòng nhập Số reps (VD: 8-10 hoặc 8-12) cho bài tập Strength.');
                newExReps?.focus();
                return;
            }

            newExercise.sets = sets;
            newExercise.reps = reps;
        } else if (type === 'cardio') {
            const duration = parseInt(newExDuration?.value.trim());
            if (!duration || duration <= 0) {
                alert('Vui lòng nhập Thời lượng (phút/giây) cho bài tập Cardio.');
                newExDuration?.focus();
                return;
            }

            newExercise.duration = duration;
            newExercise.duration_unit = newExUnit?.value || 'phut';
        } else {
            const isDurationMode = modeDuration && modeDuration.checked;
            if (isDurationMode) {
                const duration = parseInt(newExDuration?.value.trim());
                if (!duration || duration <= 0) {
                    alert('Vui lòng nhập Thời gian cho bài tập.');
                    newExDuration?.focus();
                    return;
                }
                const sets = parseInt(newExSets?.value.trim());
                if (sets && sets > 0) {
                    newExercise.sets = sets;
                }
                newExercise.duration = duration;
                newExercise.duration_unit = newExUnit?.value || 'giay';
            } else {
                const sets = parseInt(newExSets?.value.trim()) || 3;
                const reps = newExReps?.value.trim() || '10-12';
                newExercise.sets = sets;
                newExercise.reps = reps;
            }
        }

        editingWorkout.exercises = editingWorkout.exercises || [];
        editingWorkout.exercises.push(newExercise);
        renderModalList();

        if (newExName) {
            newExName.value = '';
            newExName.focus();
        }
    });

    // Nút "Lưu & Áp dụng bài tập"
    if (saveWorkoutBtn) {
        saveWorkoutBtn.addEventListener('click', function () {
            const titleVal = modalWorkoutTitle ? modalWorkoutTitle.value.trim() : '';
            if (titleVal) {
                editingWorkout.title = titleVal;
                editingWorkout.type = titleVal;
            }

            const dayVal = modalWorkoutDay ? modalWorkoutDay.value : '';
            const dayInt = dayVal ? parseInt(dayVal) : null;

            saveWorkoutBtn.disabled = true;
            saveWorkoutSpinner?.classList.remove('d-none');
            saveWorkoutIcon?.classList.add('d-none');

            const payload = {
                buoi_tap_id: editingWorkout.buoiTapId,
                ten_buoi_tap: editingWorkout.title || 'Buổi rèn luyện',
                ngay_trong_tuan: dayInt,
                exercises: editingWorkout.exercises || []
            };

            fetch(config.updateWorkoutRoute || '/tap-luyen/cap-nhat-buoi-tap', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || ''
                },
                body: JSON.stringify(payload)
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => {
                        throw new Error(err.message || 'Lỗi lưu buổi tập (' + response.status + ')');
                    });
                }
                return response.json();
            })
            .then(res => {
                if (res.success && res.data) {
                    currentWorkout.buoiTapId = res.data.buoi_tap_id;
                    currentWorkout.title = res.data.ten_buoi_tap;
                    currentWorkout.type = res.data.ten_buoi_tap;
                    currentWorkout.ngayTrongTuan = res.data.ngay_trong_tuan;
                    currentWorkout.exercises = res.data.exercises || [];

                    if (config.buoiTapList) {
                        const existing = config.buoiTapList.find(b => b.id === currentWorkout.buoiTapId);
                        if (existing) {
                            existing.title = currentWorkout.title;
                            existing.exercises = currentWorkout.exercises;
                            existing.ngay_trong_tuan = currentWorkout.ngayTrongTuan;
                            existing.ten_thu = res.data.ten_thu;
                        }
                    }

                    currentExerciseIndex = 0;
                    renderPlayer();

                    const bsModal = bootstrap.Modal.getInstance(editWorkoutModal);
                    if (bsModal) bsModal.hide();

                    showWorkoutSavedToast(res.message);
                }
            })
            .catch(err => {
                console.error('Lỗi khi lưu cấu hình buổi tập:', err);
                alert('Không thể lưu buổi tập: ' + err.message);
            })
            .finally(() => {
                saveWorkoutBtn.disabled = false;
                saveWorkoutSpinner?.classList.add('d-none');
                saveWorkoutIcon?.classList.remove('d-none');
            });
        });
    }

    function showWorkoutSavedToast(msg) {
        const alertBox = document.createElement('div');
        alertBox.className = 'alert alert-primary alert-dismissible fade show position-fixed bottom-0 end-0 m-3 shadow-lg rounded-4 z-3';
        alertBox.style.maxWidth = '380px';
        alertBox.innerHTML = `
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill text-primary fs-4"></i>
                <div>
                    <strong class="d-block">Đã cập nhật bài tập!</strong>
                    <small>${msg}</small>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        document.body.appendChild(alertBox);
        setTimeout(() => {
            alertBox.classList.remove('show');
            setTimeout(() => alertBox.remove(), 300);
        }, 4000);
    }

    // 7. QUẢN LÝ LỊCH TẬP & KẾ HOẠCH (MANAGE SCHEDULE MODAL ACTIONS)
    document.getElementById('btn-add-session')?.addEventListener('click', function () {
        const nameInput = document.getElementById('new-session-name');
        const daySelect = document.getElementById('new-session-day');
        const name = nameInput?.value.trim();
        const dayVal = daySelect?.value;

        if (!name) {
            alert('Vui lòng nhập tên buổi tập (VD: Upper Body, Legs...).');
            nameInput?.focus();
            return;
        }

        const payload = {
            ke_hoach_tap_luyen_id: config.activePlanId,
            ten_buoi_tap: name,
            ngay_trong_tuan: dayVal ? parseInt(dayVal) : null,
        };

        fetch(config.createSessionRoute || '/tap-luyen/buoi-tap', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken || ''
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.reload();
            } else {
                alert('Không thể thêm buổi tập: ' + (data.message || 'Lỗi'));
            }
        })
        .catch(err => {
            console.error(err);
            alert('Lỗi kết nối máy chủ: ' + err.message);
        });
    });

    document.querySelectorAll('.btn-delete-session').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            if (!confirm('Bạn có chắc chắn muốn xóa buổi tập này khỏi kế hoạch?')) return;

            fetch(`${config.deleteSessionUrl || '/tap-luyen/buoi-tap'}/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || ''
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.reload();
                } else {
                    alert('Lỗi: ' + data.message);
                }
            })
            .catch(err => alert('Lỗi: ' + err.message));
        });
    });

    document.getElementById('btn-create-plan')?.addEventListener('click', function () {
        const nameInput = document.getElementById('new-plan-name');
        const descInput = document.getElementById('new-plan-desc');
        const name = nameInput?.value.trim();

        if (!name) {
            alert('Vui lòng nhập tên kế hoạch mới.');
            nameInput?.focus();
            return;
        }

        const payload = {
            ten_ke_hoach: name,
            mo_ta: descInput?.value.trim() || null,
            is_active: true
        };

        fetch(config.createPlanRoute || '/tap-luyen/ke-hoach', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken || ''
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.reload();
            } else {
                alert('Không thể tạo kế hoạch: ' + (data.message || 'Lỗi'));
            }
        })
        .catch(err => alert('Lỗi kết nối: ' + err.message));
    });

    document.querySelectorAll('.btn-activate-plan').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            fetch(`${config.activatePlanUrl || '/tap-luyen/ke-hoach'}/${id}/kich-hoat`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || ''
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.reload();
                } else {
                    alert('Lỗi: ' + data.message);
                }
            })
            .catch(err => alert('Lỗi: ' + err.message));
        });
    });

    document.querySelectorAll('.btn-delete-plan').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            if (!confirm('Bạn có chắc chắn muốn xóa kế hoạch này? Các buổi tập thuộc kế hoạch cũng sẽ bị xóa.')) return;

            fetch(`${config.deletePlanUrl || '/tap-luyen/ke-hoach'}/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || ''
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.reload();
                } else {
                    alert('Lỗi: ' + data.message);
                }
            })
            .catch(err => alert('Lỗi: ' + err.message));
        });
    });

    // 8. Sinh Lịch tập Heatmap từ DB thật
    let viewMonth = config.currentMonth;
    let viewYear = config.currentYear;
    const heatmapData = config.monthlyHeatmap || {};

    const colorScale = [
        '#f1f5f9', // 0: Nghỉ / Chưa tập
        '#c7d2fe', // 1: Nhẹ (1 - 20p)
        '#818cf8', // 2: Vừa (21 - 40p)
        '#6366f1', // 3: Nhiều (41 - 60p)
        '#4338ca'  // 4: Rất nhiều (> 60p)
    ];

    function getIntensity(minutes) {
        if (!minutes || minutes <= 0) return 0;
        if (minutes <= 20) return 1;
        if (minutes <= 40) return 2;
        if (minutes <= 60) return 3;
        return 4;
    }

    function generateCalendar(month, year) {
        const grid = document.getElementById('calendar-grid');
        const monthTitle = document.getElementById('calendar-month-title');
        if (!grid) return;

        if (monthTitle) {
            monthTitle.textContent = `Tháng ${month}, ${year}`;
        }

        const headers = Array.from(grid.children).slice(0, 7);
        grid.innerHTML = '';
        headers.forEach(h => grid.appendChild(h));

        const firstDay = new Date(year, month - 1, 1);
        let startDayOfWeek = firstDay.getDay();
        if (startDayOfWeek === 0) startDayOfWeek = 7;

        const daysInMonth = new Date(year, month, 0).getDate();

        for (let i = 1; i < startDayOfWeek; i++) {
            const blankCell = document.createElement('div');
            blankCell.className = 'calendar-heatmap-cell cell-empty';
            grid.appendChild(blankCell);
        }

        for (let day = 1; day <= daysInMonth; day++) {
            const cell = document.createElement('div');
            cell.className = 'calendar-heatmap-cell';

            const monthStr = month.toString().padStart(2, '0');
            const dayStr = day.toString().padStart(2, '0');
            const dateKey = `${year}-${monthStr}-${dayStr}`;

            const record = heatmapData[dateKey];
            const duration = record ? (record.duration || 0) : 0;
            const count = record ? (record.count || 0) : 0;
            const intensity = getIntensity(duration);

            cell.style.backgroundColor = colorScale[intensity];
            cell.dataset.date = dateKey;

            if (duration > 0) {
                cell.title = `Ngày ${day}/${month}: Đã tập ${duration} phút (${count} buổi)`;
                cell.classList.add('has-workout');
            } else {
                cell.title = `Ngày ${day}/${month}: Chưa có hoạt động tập luyện`;
            }

            const today = new Date();
            if (today.getFullYear() === year && (today.getMonth() + 1) === month && today.getDate() === day) {
                cell.classList.add('today-cell');
            }

            grid.appendChild(cell);
        }
    }

    function updateTodayHeatmap(newMinutes) {
        const today = new Date();
        const monthStr = (today.getMonth() + 1).toString().padStart(2, '0');
        const dayStr = today.getDate().toString().padStart(2, '0');
        const dateKey = `${today.getFullYear()}-${monthStr}-${dayStr}`;

        if (!heatmapData[dateKey]) {
            heatmapData[dateKey] = { count: 0, duration: 0 };
        }
        heatmapData[dateKey].count += 1;
        heatmapData[dateKey].duration += newMinutes;

        generateCalendar(viewMonth, viewYear);
    }

    document.getElementById('prev-month-btn')?.addEventListener('click', function () {
        viewMonth--;
        if (viewMonth < 1) {
            viewMonth = 12;
            viewYear--;
        }
        generateCalendar(viewMonth, viewYear);
    });

    document.getElementById('next-month-btn')?.addEventListener('click', function () {
        viewMonth++;
        if (viewMonth > 12) {
            viewMonth = 1;
            viewYear++;
        }
        generateCalendar(viewMonth, viewYear);
    });

    document.getElementById('sidebarToggle')?.addEventListener('click', function () {
        const offcanvasEl = document.getElementById('sidebarOffcanvas');
        if (offcanvasEl && typeof bootstrap !== 'undefined') {
            const bsOffcanvas = bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);
            bsOffcanvas.toggle();
        }
    });

    renderPlayer();
    updateStatusUI('NOT_STARTED');
    generateCalendar(viewMonth, viewYear);
});
