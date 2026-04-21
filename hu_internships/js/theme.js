/**
 * theme.js — ระบบสลับโหมดมืด/สว่าง (Dark/Light Mode Toggle)
 * จัดการการสลับ Class บน body และอัปเดตสถานะใน localStorage
 */
document.addEventListener('DOMContentLoaded', () => {
    const themeToggle = document.getElementById('theme-toggle');
    const body = document.body;
    const storedTheme = localStorage.getItem('theme');
    const moonIcon = document.getElementById('icon-moon');
    const sunIcon = document.getElementById('icon-sun');

    // 1. ตรัสเตรียม Icon: แสดงผลให้ถูกต้องตามธีมปัจจุบัน
    const updateIcons = (isDark) => {
        if (moonIcon && sunIcon) {
            moonIcon.style.display = isDark ? 'none' : 'block';
            sunIcon.style.display = isDark ? 'block' : 'none';
        }
    };

    // 2. ตรวจสอบธีมที่บันทึกไว้ (ค่าเริ่มต้นคือ Dark Mode ตาม Netflix Style)
    if (storedTheme === 'light') {
        body.classList.remove('dark-mode');
        updateIcons(false);
    } else {
        body.classList.add('dark-mode');
        updateIcons(true);
        // บันทึกเป็น Dark หากยังไม่มีค่าใดๆ
        if (!storedTheme) localStorage.setItem('theme', 'dark');
    }

    // 3. จัดการปุ่มกดสลับธีม
    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            const isDarkMode = body.classList.toggle('dark-mode');
            
            // บันทึกค่าลงคุกกี้/พื้นที่เก็บข้อมูลของเบราว์เซอร์
            localStorage.setItem('theme', isDarkMode ? 'dark' : 'light');
            
            // สลับไอคอนดวงจันทร์/ดวงอาทิตย์
            updateIcons(isDarkMode);
        });
    }
});
