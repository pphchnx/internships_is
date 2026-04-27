<?php
/**
 * staff/manage_news.php — ระบบจัดการข่าวสารและกิจกรรม
 * หน้าที่:
 * - เพิ่ม, แก้ไข และลบข่าวสาร/กิจกรรม
 * - จัดการการอัปโหลดรูปภาพประกอบข่าว
 * - แสดงรายการข่าวทั้งหมดในรูปแบบตาราง
 */
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/auth.php';

checkLogin();
checkRole('staff');

$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'th';
$_SESSION['lang'] = $lang;
$t = require "../lang/{$lang}.php";

$msg = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add' || $action === 'edit') {
        $title   = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $id      = (int)($_POST['id'] ?? 0);
        $img_name = null;

        // Handle image upload
        if (!empty($_FILES['news_image']['name'])) {
            $file = $_FILES['news_image'];
            $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg','jpeg','png','gif','webp']) && $file['error'] === UPLOAD_ERR_OK) {
                $new_name = 'news_' . time() . '_' . uniqid() . '.' . $ext;
                $dest     = '../assets/images/news/' . $new_name;
                if (move_uploaded_file($file['tmp_name'], $dest)) {
                    $img_name = $new_name;
                }
            }
        }

        if ($title && $content) {
            if ($action === 'add') {
                $stmt = $conn->prepare("INSERT INTO news_activities (title, content, news_image) VALUES (?,?,?)");
                $stmt->execute([$title, $content, $img_name]);
                $msg = '<div class="alert-ok">เพิ่มข่าวสำเร็จ!</div>';
            } else {
                if ($img_name) {
                    $stmt = $conn->prepare("UPDATE news_activities SET title=?, content=?, news_image=? WHERE id=?");
                    $stmt->execute([$title, $content, $img_name, $id]);
                } else {
                    $stmt = $conn->prepare("UPDATE news_activities SET title=?, content=? WHERE id=?");
                    $stmt->execute([$title, $content, $id]);
                }
                $msg = '<div class="alert-ok">แก้ไขข่าวสำเร็จ!</div>';
            }
        } else {
            $msg = '<div class="alert-err">กรุณากรอกหัวข้อและเนื้อหาข่าว</div>';
        }
    }

    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        // Remove image file
        $row = $conn->prepare("SELECT news_image FROM news_activities WHERE id=?");
        $row->execute([$id]);
        $f = $row->fetchColumn();
        if ($f && file_exists('../assets/images/news/' . $f)) unlink('../assets/images/news/' . $f);
        $conn->prepare("DELETE FROM news_activities WHERE id=?")->execute([$id]);
        $msg = '<div class="alert-ok">ลบข่าวสำเร็จ!</div>';
    }
}

$news_all = $conn->query("SELECT * FROM news_activities ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

$page_title = 'จัดการข่าวสารและกิจกรรม';
$extra_css = 'manage_news.css'; // โหลด css/manage_news.css ผ่าน header.php
require_once '../includes/header.php';
require_once '../includes/navbar.php';
?>


<div class="news-mgmt">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; border-bottom:1px solid var(--border-color); padding-bottom:.75rem; flex-wrap:wrap; gap:.75rem;">
        <h2 style="font-size:1.8rem; margin:0;">จัดการข่าวสารและกิจกรรม</h2>
        <div style="display:flex; gap:.5rem;">
            <button onclick="document.getElementById('addForm').style.display=document.getElementById('addForm').style.display==='none'?'block':'none'" class="btn btn-primary">+ เพิ่มข่าวใหม่</button>
            <a href="../dashboard.php" class="btn" style="border:1px solid var(--border-color); color:var(--text-color);">&larr; ย้อนกลับ</a>
        </div>
    </div>

    <?= $msg ?>

    <!-- ADD FORM -->
    <div id="addForm" style="display:none; margin-bottom:2rem;">
        <div class="card" style="padding:1.75rem;">
            <h3 style="margin-bottom:1.25rem; font-size:1.2rem;">เพิ่มข่าวใหม่</h3>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">
                <div class="form-grid" style="margin-bottom:1rem;">
                    <div class="form-group">
                        <label class="form-label">หัวข้อข่าว <span style="color:var(--primary-color);">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="กรอกหัวข้อข่าว..." required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">รูปภาพประกอบข่าว (ไม่บังคับ)</label>
                        <input type="file" name="news_image" class="form-control" accept="image/*" style="padding:.5rem;">
                    </div>
                </div>
                <div class="form-group" style="margin-bottom:1rem;">
                    <label class="form-label">เนื้อหาข่าว <span style="color:var(--primary-color);">*</span></label>
                    <textarea name="content" class="form-control" rows="5" placeholder="กรอกเนื้อหาข่าวสารและกิจกรรม..." required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">บันทึกข่าว</button>
            </form>
        </div>
    </div>

    <!-- EDIT MODAL -->
    <div id="editModal" style="display:none; position:fixed; inset:0; z-index:500; background:rgba(0,0,0,.6); backdrop-filter:blur(4px); align-items:center; justify-content:center; padding:1rem;" onclick="if(event.target===this)closeEdit()">
        <div class="card" style="max-width:640px; width:100%; padding:2rem; position:relative; max-height:88vh; overflow-y:auto;">
            <button onclick="closeEdit()" style="position:absolute;top:1rem;right:1rem;background:none;border:none;font-size:1.4rem;cursor:pointer;opacity:.5;" onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=.5">&#x2715;</button>
            <h3 style="margin-bottom:1.25rem;">แก้ไขข่าว</h3>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="editId">
                <div class="form-grid" style="margin-bottom:1rem;">
                    <div class="form-group">
                        <label class="form-label">หัวข้อข่าว *</label>
                        <input type="text" name="title" id="editTitle" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">รูปภาพใหม่ (ถ้าต้องการเปลี่ยน)</label>
                        <input type="file" name="news_image" class="form-control" accept="image/*" style="padding:.5rem;">
                    </div>
                </div>
                <div id="editCurrentImg" style="margin-bottom:1rem;"></div>
                <div class="form-group" style="margin-bottom:1.25rem;">
                    <label class="form-label">เนื้อหาข่าว *</label>
                    <textarea name="content" id="editContent" class="form-control" rows="7" required></textarea>
                </div>
                <div style="display:flex; gap:.75rem;">
                    <button type="submit" class="btn btn-primary">บันทึกการเปลี่ยนแปลง</button>
                    <button type="button" onclick="closeEdit()" class="btn" style="border:1px solid var(--border-color);">ยกเลิก</button>
                </div>
            </form>
        </div>
    </div>

    <!-- NEWS TABLE -->
    <div class="card" style="overflow:hidden; padding:0;">
        <?php if (empty($news_all)): ?>
            <div style="padding:3rem; text-align:center; opacity:.5;">ยังไม่มีข่าวสารในระบบ</div>
        <?php else: ?>
        <table class="news-table">
            <thead><tr><th>รูป</th><th>หัวข้อ</th><th>เนื้อหา (ย่อ)</th><th>วันที่</th><th style="text-align:right;">จัดการ</th></tr></thead>
            <tbody>
            <?php foreach ($news_all as $n): ?>
            <tr>
                <td>
                    <?php if (!empty($n['news_image'])): ?>
                        <img src="../assets/images/news/<?= htmlspecialchars($n['news_image']) ?>" class="news-thumb">
                    <?php else: ?>
                        <div class="news-no-img">ไม่มีรูป</div>
                    <?php endif; ?>
                </td>
                <td style="font-weight:600; max-width:200px;"><?= htmlspecialchars($n['title']) ?></td>
                <td style="max-width:260px; opacity:.7;"><?= htmlspecialchars(mb_substr($n['content'], 0, 80)) ?>...</td>
                <td style="white-space:nowrap; font-size:.82rem; opacity:.6;"><?= date('d M Y', strtotime($n['created_at'])) ?></td>
                <td style="text-align:right; white-space:nowrap;">
                    <button onclick="openEdit(<?= $n['id'] ?>, <?= htmlspecialchars(json_encode($n['title']), ENT_QUOTES) ?>, <?= htmlspecialchars(json_encode($n['content']), ENT_QUOTES) ?>, '<?= htmlspecialchars($n['news_image'] ?? '') ?>')"
                        class="btn" style="padding:.35rem .7rem; font-size:.82rem; background:rgba(59,130,246,.1); color:#3b82f6; border:1px solid #3b82f6;">แก้ไข</button>
                    <form method="POST" style="display:inline;" onsubmit="return confirm('ต้องการลบข่าวนี้ใช่ไหม?')">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= $n['id'] ?>">
                        <button type="submit" class="btn" style="padding:.35rem .7rem; font-size:.82rem; background:rgba(239,68,68,.1); color:#ef4444; border:1px solid #ef4444;">ลบ</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<script>
function openEdit(id, title, content, img) {
    document.getElementById('editId').value      = id;
    document.getElementById('editTitle').value   = title;
    document.getElementById('editContent').value = content;
    var ci = document.getElementById('editCurrentImg');
    ci.innerHTML = img ? '<p style="font-size:.85rem;opacity:.7;margin:0;">รูปปัจจุบัน: <img src="../assets/images/news/' + img + '" style="height:60px;border-radius:6px;margin-left:.5rem;vertical-align:middle;"></p>' : '';
    var m = document.getElementById('editModal'); m.style.display='flex'; document.body.style.overflow='hidden';
}
function closeEdit() { document.getElementById('editModal').style.display='none'; document.body.style.overflow=''; }
document.addEventListener('keydown', function(e){ if(e.key==='Escape') closeEdit(); });
</script>

<?php require_once '../includes/footer.php'; ?>
