@props([
'currentMonth' => date('n'),
'currentYear' => date('Y'),
'events' => collect()
])

@php
use Carbon\Carbon;

// Khởi tạo đối tượng thời gian cho tháng/năm truyền vào
$firstDayOfMonth = Carbon::createFromDate((int)$currentYear, (int)$currentMonth, 1)->startOfDay();
$daysInMonth = $firstDayOfMonth->daysInMonth;

// Thứ của ngày đầu tiên trong tháng (1: Thứ 2 -> 7: Chủ Nhật)
$firstDayOfWeek = $firstDayOfMonth->isoWeekday();

// Ngày hiện tại thực tế để đánh dấu "Hôm nay"
$today = Carbon::today();

// Tháng trước và Tháng sau để điều hướng nút bấm
$prevMonthDate = $firstDayOfMonth->copy()->subMonth();
$nextMonthDate = $firstDayOfMonth->copy()->addMonth();

$prevMonth = $prevMonthDate->month;
$prevYear = $prevMonthDate->year;
$nextMonth = $nextMonthDate->month;
$nextYear = $nextMonthDate->year;

// Số ngày của tháng trước dùng cho các ô padding đầu lịch
$daysInPrevMonth = $prevMonthDate->daysInMonth;
$paddingOffset = $firstDayOfWeek - 1; // Số ô trống cần điền của tháng trước

// Gom nhóm sự kiện theo chuỗi ngày 'Y-m-d'
$groupedEvents = collect($events)->groupBy(function($event) {
if (is_array($event)) {
$date = $event['thoi_gian_bat_dau'] ?? $event['date'] ?? null;
} else {
$date = $event->thoi_gian_bat_dau ?? $event->date ?? null;
}
return $date ? Carbon::parse($date)->format('Y-m-d') : '';
});
@endphp

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <!-- HEADER NÚT CHUYỂN THÁNG -->
    <div class="card-header bg-white border-0 py-3 px-4 d-flex align-items-center justify-content-between">
        <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
            <i class="bi bi-calendar-month text-primary fs-4"></i>
            <span>Tháng {{ $currentMonth }}, {{ $currentYear }}</span>
        </h5>

        <div class="d-flex align-items-center gap-2">
            <!-- Nút về tháng hiện tại -->
            <a href="?month={{ date('n') }}&year={{ date('Y') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-medium">
                Hôm nay
            </a>

            <!-- Nút Tháng trước -->
            <a href="?month={{ $prevMonth }}&year={{ $prevYear }}" class="btn btn-sm btn-light border rounded-circle p-0 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;" title="Tháng trước">
                <i class="bi bi-chevron-left"></i>
            </a>

            <!-- Nút Tháng sau -->
            <a href="?month={{ $nextMonth }}&year={{ $nextYear }}" class="btn btn-sm btn-light border rounded-circle p-0 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;" title="Tháng sau">
                <i class="bi bi-chevron-right"></i>
            </a>
        </div>
    </div>

    <!-- BẢNG LỊCH THÁNG BOOTSTRAP 5 -->
    <div class="card-body p-3">
        <table class="table table-borderless text-center align-middle mb-0" style="table-layout: fixed;">
            <thead>
                <tr class="text-secondary fw-semibold fs-7 border-bottom">
                    <th class="py-2">T2</th>
                    <th class="py-2">T3</th>
                    <th class="py-2">T4</th>
                    <th class="py-2">T5</th>
                    <th class="py-2">T6</th>
                    <th class="py-2 text-primary">T7</th>
                    <th class="py-2 text-danger">CN</th>
                </tr>
            </thead>
            <tbody>
                @php
                $currentCell = 1;
                $dayCounter = 1;
                $nextMonthDayCounter = 1;
                @endphp

                @while ($dayCounter <= $daysInMonth)
                    <tr>
                    @for ($i = 1; $i <= 7; $i++)
                        @php
                        $cellDateString=null;
                        $isCurrentMonth=false;
                        $isToday=false;
                        $displayDay='' ;

                        if ($currentCell <=$paddingOffset) {
                        // Ô hiển thị ngày tháng trước
                        $displayDay=$daysInPrevMonth - $paddingOffset + $currentCell;
                        } elseif ($dayCounter <=$daysInMonth) {
                        // Ô hiển thị ngày tháng hiện tại
                        $isCurrentMonth=true;
                        $displayDay=$dayCounter;
                        $cellDateString=sprintf('%04d-%02d-%02d', $currentYear, $currentMonth, $dayCounter);
                        $cellDate=Carbon::parse($cellDateString);
                        $isToday=$today->isSameDay($cellDate);
                        $dayCounter++;
                        } else {
                        // Ô hiển thị ngày tháng sau
                        $displayDay = $nextMonthDayCounter++;
                        }

                        $currentCell++;
                        $dayEvents = ($isCurrentMonth && $cellDateString) ? ($groupedEvents->get($cellDateString) ?? collect()) : collect();
                        @endphp

                        <td class="p-1">
                            <div class="p-2 rounded-3 text-start position-relative border"
                                style="min-height: 85px; background-color: {{ $isToday ? '#eef2ff' : ($isCurrentMonth ? '#ffffff' : '#f8fafc') }}; border-color: {{ $isToday ? '#818cf8 !important' : '#f1f5f9' }};">

                                <!-- Hiển thị số ngày -->
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold fs-7 {{ $isToday ? 'badge bg-primary rounded-circle p-1 px-2' : ($isCurrentMonth ? ($i >= 6 ? 'text-danger' : 'text-dark') : 'text-black-50') }}">
                                        {{ $displayDay }}
                                    </span>
                                    @if($dayEvents->count() > 0)
                                    <span class="badge bg-primary-subtle text-primary rounded-pill fs-8">
                                        {{ $dayEvents->count() }}
                                    </span>
                                    @endif
                                </div>

                                <!-- Hiển thị danh sách sự kiện trong ngày -->
                                <div class="events-wrapper overflow-hidden" style="max-height: 52px;">
                                    @foreach($dayEvents->take(2) as $event)
                                    @php
                                    $tieuDe = is_array($event) ? ($event['tieu_de'] ?? $event['title'] ?? '') : ($event->tieu_de ?? $event->title ?? '');
                                    $loai = is_array($event) ? ($event['loai_su_kien'] ?? 'hoc_tap') : ($event->loai_su_kien ?? 'hoc_tap');

                                    $badgeStyle = match($loai) {
                                    'hoc_tap' => 'background-color: #e0e7ff; color: #4338ca; border-left: 3px solid #6366f1;',
                                    'the_chat' => 'background-color: #d1fae5; color: #065f46; border-left: 3px solid #10b981;',
                                    'ca_nhan' => 'background-color: #fef3c7; color: #92400e; border-left: 3px solid #f59e0b;',
                                    default => 'background-color: #e0f2fe; color: #0369a1; border-left: 3px solid #0284c7;'
                                    };
                                    @endphp
                                    <div class="text-truncate fs-8 p-1 mb-1 rounded" style="{{ $badgeStyle }}" title="{{ $tieuDe }}">
                                        {{ $tieuDe }}
                                    </div>
                                    @endforeach

                                    @if($dayEvents->count() > 2)
                                    <div class="text-muted fs-8 text-center fw-semibold">
                                        +{{ $dayEvents->count() - 2 }} sự kiện khác
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        @endfor
                        </tr>
                        @endwhile
            </tbody>
        </table>
    </div>
</div>