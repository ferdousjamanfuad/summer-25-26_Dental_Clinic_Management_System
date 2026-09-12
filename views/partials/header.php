<?php
$navUser = current_user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($pageTitle ?? APP_NAME) ?></title>
    <style>
        /* Minimal styling mapping the demo */
        body { font-family: Arial, sans-serif; margin: 0; background-color: #f4f6f9; color: #333; }
        .navbar { background: #0077b6; color: white; padding: 15px 20px; display: flex; justify-content: space-between; }
        .navbar a { color: white; text-decoration: none; font-weight: bold; }
        .container { padding: 20px; max-width: 1200px; margin: auto; }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .alert-success { background: #d4edda; color: #155724; }
        .alert-error { background: #f8d7da; color: #721c24; }
        .card { background: white; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 10px; text-align: left; }
        th { background-color: #f8f9fa; }
        .btn { padding: 8px 12px; background: #0077b6; color: white; text-decoration: none; border: none; cursor: pointer; border-radius: 4px;}
        .btn:hover { background: #023e8a; }
        .btn-danger { background: #e71d36; }
        .btn-success { background: #2ec4b6; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; }
        .form-control { width: 100%; padding: 8px; box-sizing: border-box; }
        .nav-tabs { display: flex; gap: 10px; margin-bottom: 20px; border-bottom: 2px solid #ddd; padding-bottom: 10px;}
        .nav-tabs a { text-decoration: none; color: #555; font-weight: bold; padding: 5px 10px; }
        .nav-tabs a.active { color: #0077b6; border-bottom: 2px solid #0077b6; }
    </style>
</head>
<body>

<?php if(is_logged_in()): ?>
<div class="navbar">
    <div class="brand">
        <a href="index.php?page=<?= esc($navUser['role']) ?>"><?= esc(APP_NAME) ?></a>
    </div>
    <div class="user-menu">
        Welcome, <?= esc($navUser['name']) ?> (<?= esc(role_label($navUser['role'])) ?>)
        | <a href="index.php?page=logout">Logout</a>
    </div>
</div>
<?php endif; ?>

<div class="container">
    <?php foreach(get_flash() as $msg): ?>
        <div class="alert alert-<?= esc($msg['type']) ?>">
            <?= esc($msg['message']) ?>
        </div>
    <?php endforeach; ?>
