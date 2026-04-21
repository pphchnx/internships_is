<?php
// Global Base URL for absolute asset paths
$base_url = '/project_is/hu_internships';
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? htmlspecialchars($page_title) : 'SWU Internships' ?></title>
    <!-- We no longer fetch remote fonts on every page, we use style.css -->
    <link rel="stylesheet" href="<?= $base_url ?>/css/style.css">
    <?php if(isset($extra_css)): ?>
    <link rel="stylesheet" href="<?= $base_url ?>/css/<?= $extra_css ?>">
    <?php endif; ?>
</head>
<body>
