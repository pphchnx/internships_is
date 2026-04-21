/**
 * lang.js — ระบบสลับภาษา (TH/EN)
 * จัดการการเปลี่ยน Query Parameter 'lang' และรีโหลดหน้า
 */
document.addEventListener('DOMContentLoaded', () => {
    const langToggle = document.getElementById('lang-toggle');
    
    if (langToggle) {
        langToggle.addEventListener('click', () => {
            // ดึงภาษาปัจจุบันจากแท็ก <html> (ตั้งค่าโดย PHP $lang)
            const currentLang = document.documentElement.lang || 'th';
            const newLang = currentLang === 'en' ? 'th' : 'en';
            
            // สร้าง URL ใหม่ที่มีพารามิเตอร์ lang
            const url = new URL(window.location.href);
            url.searchParams.set('lang', newLang);
            
            // นำทางไปยัง URL ใหม่ (รีโหลดหน้าพร้อมค่า lang ใหม่)
            window.location.href = url.toString();
        });
    }
});
