<?php
/**
 * functions.php — รวบรวมฟังก์ชันช่วยเหลือส่วนกลาง (Helper Functions)
 * ใช้จัดการข้อมูลพื้นฐาน, การแสดงผลสถานะ และการจัดการ Path
 */

/**
 * ทำความสะอาดข้อมูล Input ป้องกัน XSS
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * คืนค่า Base URL ของโปรเจกต์
 */
function getBaseUrl() {
    return '/project_is/hu_internships';
}

/**
 * แปลงสถานะตัวเลขเป็น Badge HTML
 * $mode: 'default' (ทั่วไป), 'staff' (สำหรับเจ้าหน้าที่)
 */
function getStatusBadge($status, $t, $mode = 'default') {
    if ($mode === 'staff') {
        $labels = [
            0 => $t['status_pending_staff'] ?? 'รอเจ้าหน้าที่รับเรื่อง',
            1 => $t['status_pending'] ?? 'รับเรื่องเข้าระบบ',
            2 => $t['status_approved'] ?? 'อนุมัติแล้ว',
            3 => $t['status_doc_issued'] ?? 'ออกใบส่งตัว',
            4 => $t['status_completed'] ?? 'เสร็จสิ้น',
            9 => $t['status_canceled'] ?? 'ยกเลิก/ให้แก้ไข'
        ];
    } else {
        // mode 'default' — ใช้สำหรับนิสิต ต้อง map ตรงกับค่า status จริงในฐานข้อมูล
        $labels = [
            0 => $t['status_pending_staff'] ?? 'รอเจ้าหน้าที่รับเรื่อง',
            1 => $t['status_pending']       ?? 'รับเรื่องเข้าระบบ',
            2 => $t['status_approved']      ?? 'อนุมัติแล้ว',
            3 => $t['status_doc_issued']    ?? 'ออกใบส่งตัวแล้ว',
            4 => $t['status_completed']     ?? 'ฝึกงานเสร็จสิ้น',
            9 => $t['status_canceled']      ?? 'ยกเลิก / ให้แก้ไข'
        ];
    }

    $classes = [
        0 => 'pending',
        1 => 'pending',
        2 => 'approved',
        3 => 'approved',
        4 => 'approved',
        9 => 'rejected'
    ];
    // ลบ override เดิมที่ผิดพลาด (เคยทำให้ status=2 แสดงเป็น rejected สำหรับนิสิต)

    $label = $labels[$status] ?? 'Unknown';
    $class = $classes[$status] ?? 'pending';

    return '<span class="badge badge-'.$class.'">'.$label.'</span>';
}

/**
 * คืนค่า SVG Icon ตามชื่อ
 */
function getIcon($name, $width = 16, $height = 16) {
    $icons = [
        'check' => '<svg xmlns="http://www.w3.org/2000/svg" width="'.$width.'" height="'.$height.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>',
        'warn'  => '<svg xmlns="http://www.w3.org/2000/svg" width="'.$width.'" height="'.$height.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
        'edit'  => '<svg xmlns="http://www.w3.org/2000/svg" width="'.$width.'" height="'.$height.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>',
        'eye'   => '<svg xmlns="http://www.w3.org/2000/svg" width="'.$width.'" height="'.$height.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>'
    ];
    return $icons[$name] ?? '';
}
?>
