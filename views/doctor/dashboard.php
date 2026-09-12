<?php 
$pageTitle = 'Doctor Workspace';
require __DIR__ . '/../partials/header.php'; 
?>

<h2>Doctor Workspace</h2>
<div class="card">
    <p>Welcome, Dr. <?= esc($me['name']) ?>.</p>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
