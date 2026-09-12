<?php 
$pageTitle = 'Patient Portal';
require __DIR__ . '/../partials/header.php'; 
?>

<h2>Patient Portal</h2>
<div class="card">
    <p>Welcome, <?= esc($me['name']) ?>.</p>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
