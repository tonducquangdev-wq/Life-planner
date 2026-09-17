/**
 * ==========================================================================
 * SCRIPT ĐIỀU KHIỂN CHỨC NĂNG THỂ CHẤT & TẬP LUYỆN - STUDENT-LIFE / LIFE PLANNER
 * Chuyển giao toàn bộ logic từ suckhoe.js, tối ưu hóa cho cấu trúc Laravel Blade
 * ==========================================================================
 */

document.addEventListener('DOMContentLoaded', function () {

    // 1. Dữ liệu mẫu các phân nhóm bài tập
    const defaultWorkoutTemplates = {
        'Push': {
            type: 'Push',
            title: 'Push — 3 ngực, 2 tay sau, 1 vai',
            exercises: [
                { name: 'Barbell Bench Press', sets: 4, reps: '8-10' },
                { name: 'Incline Dumbbell Press', sets: 3, reps: '10-12' },
                { name: 'Cable Fly', sets: 3, reps: '12-15' },
                { name: 'Triceps Pushdown', sets: 3, reps: '10-12' },
                { name: 'Overhead Triceps Extension', sets: 3, reps: '10-12' },
                { name: 'Dumbbell Shoulder Press', sets: 3, reps: '10-12' }
            ]
        },
        'Pull': {
            type: 'Pull',
            title: 'Pull — Lưng xô, bắp tay trước',
            exercises: [
                { name: 'Deadlift', sets: 4, reps: '6-8' },
                { name: 'Pull up', sets: 3, reps: '8-10' },
                { name: 'Lat Pulldown', sets: 3, reps: '10-12' },
                { name: 'Barbell Row', sets: 3, reps: '8-10' },
                { name: 'Bicep Curl', sets: 3, reps: '10-12' }
            ]
        },
        'Legs': {
            type: 'Legs',
            title: 'Legs — Đùi, mông, bắp chân',
            exercises: [
                { name: 'Squat', sets: 4, reps: '8-10' },
                { name: 'Leg Press', sets: 4, reps: '10-12' },
                { name: 'Leg Extension', sets: 3, reps: '12-15' },
                { name: 'Leg Curl', sets: 3, reps: '10-12' },
                { name: 'Calf Raise', sets: 4, reps: '15-20' }
            ]
        },
        'Other': {
            type: 'Other',
            title: 'Cardio & Core',
            exercises: [
                { name: 'Treadmill Run', sets: 1, reps: '20 min' },
                { name: 'Plank', sets: 3, reps: '60s' },
                { name: 'Crunch', sets: 3, reps: '15-20' }
            ]
        }
    };

    let currentWorkout = JSON.parse(JSON.stringify(defaultWorkoutTemplates['Push']));

    // Khôi phục trạng thái từ localStorage nếu có
    const savedData = localStorage.getItem('workoutData');
    if (savedData) {
        try {
            currentWorkout = JSON.parse(savedData);
        } catch (e) {
            console.error('Lỗi khi phân tích dữ liệu workoutData từ localStorage', e);
        }
    }

    let editingWorkout = JSON.parse(JSON.stringify(currentWorkout));
    let currentExerciseIndex = 0;

    // 2. Render giao diện Trình tập luyện
    function renderDashboard() {
        // Cập nhật tab active
        document.querySelectorAll('.workout-tab').forEach(tab => {
            if (tab.dataset.type === currentWorkout.type) {
                tab.classList.add('active');
            } else {
                tab.classList.remove('active');
            }
        });

        // Cập nhật tiêu đề buổi tập
        const titleDisplay = document.getElementById('workout-title-display');
        if (titleDisplay) {
            titleDisplay.textContent = currentWorkout.title || currentWorkout.type;
        }

        // Cập nhật thông tin bài tập hiện tại
        const activeName = document.getElementById('active-ex-name');
        const activeSetRep = document.getElementById('active-ex-set-rep');
        const activeNumber = document.getElementById('active-ex-number');

        if (currentWorkout.exercises && currentWorkout.exercises.length > 0) {
            if (currentExerciseIndex >= currentWorkout.exercises.length) {
                currentExerciseIndex = 0;
            }
            const ex = currentWorkout.exercises[currentExerciseIndex];
            if (activeName) activeName.textContent = ex.name;
            if (activeSetRep) activeSetRep.textContent = `${ex.sets} x ${ex.reps}`;
            if (activeNumber) activeNumber.textContent = currentExerciseIndex + 1;
        } else {
            if (activeName) activeName.textContent = 'Chưa có bài tập';
            if (activeSetRep) activeSetRep.textContent = '-- x --';
            if (activeNumber) activeNumber.textContent = '0';
        }
    }

    // 3. Render danh sách bài tập trong Modal chỉnh sửa
    function renderModalList() {
        const listContainer = document.getElementById('modal-exercise-list');
        if (!listContainer) return;

        listContainer.innerHTML = '';

        const countBadge = document.getElementById('exercise-count-badge');
        if (countBadge) {
            countBadge.textContent = `${editingWorkout.exercises.length} bài tập`;
        }

        if (editingWorkout.exercises.length === 0) {
            listContainer.innerHTML = '<div class="text-center text-muted py-3 small bg-white rounded-3 border border-dashed">Chưa có bài tập nào trong buổi tập này.</div>';
            return;
        }

        editingWorkout.exercises.forEach((ex, index) => {
            const html = `
                <div class="d-flex align-items-center justify-content-between bg-white p-2 px-3 rounded-3 border exercise-item-row shadow-2xs">
                    <div class="d-flex align-items-center gap-2 flex-grow-1 text-truncate me-2">
                        <span class="badge bg-light text-secondary border rounded-circle" style="width: 24px; height: 24px; display:flex; align-items:center; justify-content:center;">${index + 1}</span>
                        <span class="fw-semibold text-dark text-truncate small">${ex.name}</span>
                        <span class="badge bg-light text-muted border small ms-auto">${ex.sets} sets • ${ex.reps} reps</span>
                    </div>
                    <button type="button" class="btn btn-sm btn-light border rounded-circle text-danger delete-ex-btn p-1 px-2" data-index="${index}" title="Xóa bài">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            `;
            listContainer.insertAdjacentHTML('beforeend', html);
        });

        // Gán sự kiện xóa bài tập
        document.querySelectorAll('.delete-ex-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const idx = parseInt(this.dataset.index);
                editingWorkout.exercises.splice(idx, 1);
                renderModalList();
            });
        });
    }

    // 4. Modal Chỉnh sửa
    const editWorkoutModal = document.getElementById('editWorkoutModal');
    if (editWorkoutModal) {
        editWorkoutModal.addEventListener('show.bs.modal', function () {
            editingWorkout = JSON.parse(JSON.stringify(currentWorkout));
            document.getElementById('modal-workout-type').value = editingWorkout.type;
            document.getElementById('modal-workout-title').value = editingWorkout.title;

            renderModalList();

            document.getElementById('new-ex-name').value = '';
            document.getElementById('new-ex-sets').value = '';
            document.getElementById('new-ex-reps').value = '';
        });
    }

    // Thêm bài tập trong modal
    const addExerciseBtn = document.getElementById('add-exercise-btn');
    if (addExerciseBtn) {
        addExerciseBtn.addEventListener('click', function () {
            const nameInput = document.getElementById('new-ex-name');
            const setsInput = document.getElementById('new-ex-sets');
            const repsInput = document.getElementById('new-ex-reps');

            const name = nameInput.value.trim();
            const sets = setsInput.value.trim();
            const reps = repsInput.value.trim();

            if (name && sets && reps) {
                editingWorkout.exercises.push({ name, sets, reps });
                renderModalList();

                nameInput.value = '';
                setsInput.value = '';
                repsInput.value = '';
                nameInput.focus();
            } else {
                alert('Vui lòng nhập đầy đủ Tên bài tập, Số sets và Số reps.');
            }
        });
    }

    // Lưu thay đổi từ modal
    const saveWorkoutBtn = document.getElementById('save-workout-btn');
    if (saveWorkoutBtn) {
        saveWorkoutBtn.addEventListener('click', function () {
            editingWorkout.type = document.getElementById('modal-workout-type').value;
            let newTitle = document.getElementById('modal-workout-title').value.trim();
            if (!newTitle) {
                newTitle = editingWorkout.type;
            }
            editingWorkout.title = newTitle;

            currentWorkout = JSON.parse(JSON.stringify(editingWorkout));
            localStorage.setItem('workoutData', JSON.stringify(currentWorkout));

            renderDashboard();

            const bsModal = bootstrap.Modal.getInstance(editWorkoutModal);
            if (bsModal) {
                bsModal.hide();
            }
        });
    }

    // 5. Chuyển đổi tab buổi tập (Push / Pull / Legs / Khác)
    document.querySelectorAll('.workout-tab').forEach(tab => {
        tab.addEventListener('click', function () {
            const newType = this.dataset.type;
            if (newType !== currentWorkout.type) {
                if (defaultWorkoutTemplates[newType]) {
                    currentWorkout = JSON.parse(JSON.stringify(defaultWorkoutTemplates[newType]));
                } else {
                    currentWorkout.type = newType;
                }
                currentExerciseIndex = 0;
                localStorage.setItem('workoutData', JSON.stringify(currentWorkout));
                renderDashboard();
            }
        });
    });

    // 6. Đồng hồ đếm thời gian tập luyện (Timer)
    let timerInterval = null;
    let timerSeconds = 0;
    let timerRunning = false;

    function formatTime(totalSeconds) {
        const h = Math.floor(totalSeconds / 3600).toString().padStart(2, '0');
        const m = Math.floor((totalSeconds % 3600) / 60).toString().padStart(2, '0');
        const s = (totalSeconds % 60).toString().padStart(2, '0');
        return `${h}:${m}:${s}`;
    }

    const timerDisplay = document.getElementById('workout-timer');
    const stopTimerBtn = document.getElementById('stop-timer-btn');

    function startTimer() {
        if (timerInterval) clearInterval(timerInterval);
        timerRunning = true;
        if (stopTimerBtn) {
            stopTimerBtn.innerHTML = '<i class="bi bi-pause-fill fs-5 text-warning"></i><span>Tạm dừng</span>';
            stopTimerBtn.classList.remove('text-primary');
            stopTimerBtn.classList.add('text-warning');
        }

        timerInterval = setInterval(() => {
            timerSeconds++;
            if (timerDisplay) timerDisplay.textContent = formatTime(timerSeconds);
        }, 1000);
    }

    function stopTimer() {
        if (timerInterval) clearInterval(timerInterval);
        timerInterval = null;
        timerRunning = false;
        if (stopTimerBtn) {
            stopTimerBtn.innerHTML = '<i class="bi bi-play-fill fs-5 text-primary"></i><span>Tiếp tục tính thời gian</span>';
            stopTimerBtn.classList.remove('text-warning');
            stopTimerBtn.classList.add('text-primary');
        }
    }

    if (stopTimerBtn) {
        stopTimerBtn.addEventListener('click', function () {
            if (timerRunning) {
                stopTimer();
            } else {
                startTimer();
            }
        });
    }

    // Nút "Bài tiếp theo"
    const nextBtn = document.getElementById('next-ex-btn');
    if (nextBtn) {
        nextBtn.addEventListener('click', function () {
            if (currentWorkout.exercises && currentWorkout.exercises.length > 0) {
                currentExerciseIndex = (currentExerciseIndex + 1) % currentWorkout.exercises.length;
                renderDashboard();
            }
        });
    }

    // Nút "Hoàn thành"
    const finishBtn = document.getElementById('finish-ex-btn');
    if (finishBtn) {
        finishBtn.addEventListener('click', function () {
            stopTimer();
            alert('🎉 Chúc mừng! Bạn đã hoàn thành xuất sắc buổi tập hôm nay. Dữ liệu đã được ghi nhận.');
        });
    }

    // 7. Sinh lịch tập dạng Heatmap (Calendar)
    function generateCalendar() {
        const grid = document.getElementById('calendar-grid');
        if (!grid) return;

        // Giữ lại 7 thẻ tiêu đề ngày đầu tuần (T2 - CN)
        const headers = Array.from(grid.children).slice(0, 7);
        grid.innerHTML = '';
        headers.forEach(h => grid.appendChild(h));

        // Cường độ tập mẫu 35 ô: 0 = Nghỉ, 1 = Nhẹ, 2 = Vừa, 3 = Nhiều, 4 = Rất nhiều
        const sampleIntensity = [
            0, 0, 1, 0, 2, 0, 0,
            0, 1, 3, 0, 2, 0, 1,
            0, 2, 0, 4, 3, 2, 0,
            0, 1, 2, 3, 0, 0, 1,
            2, 0, 0, 1, 0, 0, 0
        ];

        const colorScale = ['#f1f5f9', '#c7d2fe', '#818cf8', '#6366f1', '#4338ca'];

        for (let i = 0; i < 35; i++) {
            const cell = document.createElement('div');
            cell.className = 'calendar-heatmap-cell';
            const intensity = sampleIntensity[i] || 0;
            cell.style.backgroundColor = colorScale[intensity];
            cell.title = `Ngày ${i + 1} - Cường độ: ${intensity}/4`;
            grid.appendChild(cell);
        }
    }

    // Bật/tắt Sidebar trên màn hình di động
    document.getElementById('sidebarToggle')?.addEventListener('click', function () {
        document.getElementById('sidebar')?.classList.toggle('show');
    });

    // Khởi chạy ban đầu
    renderDashboard();
    generateCalendar();
});
