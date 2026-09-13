<?php 
$pageTitle = 'Doctor Workspace';
require __DIR__ . '/../partials/header.php'; 
?>

<h2>Doctor Workspace</h2>

<div class="nav-tabs">
    <a href="index.php?page=doctor&action=queue" class="<?= in_array($action, ['queue', 'prescribe']) ? 'active' : '' ?>">Appointment Queue</a>
    <a href="index.php?page=doctor&action=patients" class="<?= in_array($action, ['patients', 'patient_details']) ? 'active' : '' ?>">My Patients</a>
</div>

<?php if(!empty($error)): ?>
    <div class="alert alert-error"><?= esc($error) ?></div>
<?php endif; ?>

<!-- Appointment Queue -->
<?php if($action === 'queue'): ?>
    
    <div class="card">
        <h3>Today's & Upcoming Queue</h3>
        <table>
            <tr>
                <th>Date & Time</th>
                <th>Patient Name</th>
                <th>Contact</th>
                <th>Status</th>
                <th>Notes</th>
                <th>Action</th>
            </tr>
            <?php if(empty($appointments)): ?>
                <tr><td colspan="6" style="text-align: center;">No appointments found.</td></tr>
            <?php else: ?>
                <?php foreach($appointments as $app): ?>
                <tr>
                    <td><?= nice_date($app['appointment_date']) ?> <br> <small><?= date('h:i A', strtotime($app['appointment_time'])) ?></small></td>
                    <td><strong><?= esc($app['patient_name']) ?></strong></td>
                    <td><?= esc($app['contact']) ?></td>
                    <td>
                        <?php if($app['status'] === 'pending'): ?>
                            <span style="color:orange; font-weight:bold;">Pending</span>
                        <?php elseif($app['status'] === 'confirmed'): ?>
                            <span style="color:blue; font-weight:bold;">Confirmed</span>
                        <?php elseif($app['status'] === 'completed'): ?>
                            <span style="color:green; font-weight:bold;">Completed</span>
                        <?php else: ?>
                            <span style="color:red; font-weight:bold;">Cancelled</span>
                        <?php endif; ?>
                    </td>
                    <td><?= esc($app['notes'] ?: '-') ?></td>
                    <td>
                        <?php if(in_array($app['status'], ['pending', 'confirmed'])): ?>
                            <a href="index.php?page=doctor&action=prescribe&id=<?= $app['id'] ?>" class="btn btn-success" style="font-size: 0.8rem;">Treat / Prescribe</a>
                        <?php else: ?>
                            <i><?= ucfirst($app['status']) ?></i>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </table>
    </div>

<!-- Prescription Form -->
<?php elseif($action === 'prescribe'): ?>
    
    <div class="card">
        <h3>Consultation & Prescription</h3>
        <p><strong>Patient:</strong> <?= esc($appointment['patient_name']) ?> | <strong>Date:</strong> <?= nice_date($appointment['appointment_date']) ?></p>
        
        <form action="index.php?page=doctor&action=save_prescription" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="appointment_id" value="<?= $appointment['id'] ?>">
            <input type="hidden" name="patient_id" value="<?= $appointment['patient_id'] ?>">
            
            <div class="form-group">
                <label>Diagnosis / Problem Details <span style="color:red;">*</span></label>
                <textarea name="diagnosis" class="form-control" rows="3" required placeholder="Describe the dental issue..."></textarea>
            </div>
            
            <div class="form-group">
                <label>Additional Notes / Advice</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="Rest for 2 days, avoid cold food..."></textarea>
            </div>

            <h4 style="border-bottom: 1px solid #eee; padding-bottom: 5px;">Medicines</h4>
            <div id="medicine-list">
                <div style="display: flex; gap: 10px; margin-bottom: 10px;" class="med-row">
                    <input type="text" name="med_name[]" class="form-control" placeholder="Medicine Name (e.g. Napa)" style="flex:2;">
                    <input type="text" name="med_dosage[]" class="form-control" placeholder="Dosage (1+1+1)" style="flex:1;">
                    <input type="text" name="med_instructions[]" class="form-control" placeholder="After meal" style="flex:2;">
                </div>
            </div>
            <button type="button" class="btn" onclick="addMedRow()" style="background: #6c757d; font-size: 0.85rem; margin-bottom: 15px;">+ Add More Medicine</button>
            <br>
            <button type="submit" class="btn btn-success" style="width: 200px;">Save & Complete</button>
            <a href="index.php?page=doctor&action=queue" style="margin-left: 15px; color: #555;">Cancel</a>
        </form>
    </div>

    <script>
        function addMedRow() {
            const row = document.querySelector('.med-row').cloneNode(true);
            row.querySelectorAll('input').forEach(input => input.value = '');
            document.getElementById('medicine-list').appendChild(row);
        }
    </script>

<!-- My Patients -->
<?php elseif($action === 'patients'): ?>
    
    <div class="card">
        <h3>My Patients</h3>
        <table>
            <tr>
                <th>Patient Name</th>
                <th>Contact</th>
                <th>Gender</th>
                <th>Action</th>
            </tr>
            <?php if(empty($patients)): ?>
                <tr><td colspan="4" style="text-align: center;">No patients assigned yet.</td></tr>
            <?php else: ?>
                <?php foreach($patients as $p): ?>
                <tr>
                    <td><strong><?= esc($p['name']) ?></strong></td>
                    <td><?= esc($p['contact']) ?></td>
                    <td><?= esc(ucfirst($p['gender'] ?: '-')) ?></td>
                    <td>
                        <a href="index.php?page=doctor&action=patient_details&id=<?= $p['id'] ?>" class="btn" style="font-size: 0.8rem;">View History</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </table>
    </div>

<?php elseif($action === 'patient_details'): ?>
    
    <div class="card">
        <h3>Patient Medical History</h3>
        <p><a href="index.php?page=doctor&action=patients" style="color:#0077b6;">&larr; Back to Patient List</a></p>
        
        <?php if(empty($patient_history)): ?>
            <p>No past prescriptions or medical history found for this patient.</p>
        <?php else: ?>
            <?php foreach($patient_history as $hist): ?>
            <div style="border: 1px solid #ccc; padding: 15px; margin-bottom: 15px; border-radius: 5px;">
                <h4 style="margin-top: 0; color: #0077b6;">Date: <?= nice_date($hist['appointment_date']) ?></h4>
                <p><strong>Diagnosis:</strong> <?= esc($hist['diagnosis']) ?></p>
                <?php if(!empty($hist['notes'])): ?>
                    <p><strong>Notes/Advice:</strong> <?= esc($hist['notes']) ?></p>
                <?php endif; ?>
                
                <?php if(!empty($hist['medicines'])): ?>
                    <h5 style="margin-bottom: 5px;">Medicines Prescribed:</h5>
                    <ul style="margin-top: 0;">
                        <?php foreach($hist['medicines'] as $med): ?>
                            <li><?= esc($med['medicine_name']) ?> - <i><?= esc($med['dosage']) ?></i> (<?= esc($med['instructions']) ?>)</li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

<?php endif; ?>

<?php require __DIR__ . '/../partials/footer.php'; ?>
