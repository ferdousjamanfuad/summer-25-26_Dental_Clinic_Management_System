<?php 
$pageTitle = 'Patient Portal';
require __DIR__ . '/../partials/header.php'; 
?>

<h2>Patient Portal</h2>

<div class="nav-tabs">
    <a href="index.php?page=patient&action=appointments" class="<?= $action === 'appointments' ? 'active' : '' ?>">My Appointments & Payments</a>
    <a href="index.php?page=patient&action=book" class="<?= $action === 'book' ? 'active' : '' ?>">Book New Appointment</a>
    <a href="index.php?page=patient&action=prescriptions" class="<?= $action === 'prescriptions' ? 'active' : '' ?>">My Prescriptions</a>
</div>

<?php if(!empty($error)): ?>
    <div class="alert alert-error"><?= esc($error) ?></div>
<?php endif; ?>

<!-- ========================================== -->
<!-- FEATURE 3: MY APPOINTMENTS & PAYMENTS      -->
<!-- ========================================== -->
<?php if($action === 'appointments'): ?>
    
    <div class="card">
        <h3>My Appointments</h3>
        <table>
            <tr>
                <th>Date & Time</th>
                <th>Doctor</th>
                <th>Serial No.</th>
                <th>Status</th>
                <th>Payment Method</th>
                <th>Action</th>
            </tr>
            <?php if(empty($appointments)): ?>
                <tr><td colspan="6" style="text-align: center;">No appointments found.</td></tr>
            <?php else: ?>
                <?php foreach($appointments as $app): ?>
                <tr>
                    <td><?= nice_date($app['appointment_date']) ?> <br> <small><?= date('h:i A', strtotime($app['appointment_time'])) ?></small></td>
                    <td><strong><?= esc($app['doctor_name']) ?></strong></td>
                    <td><span style="background: #eef8fc; padding: 3px 8px; border-radius: 10px; font-weight: bold;">#<?= esc($app['serial_no']) ?></span></td>
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
                    <td>
                        <?php if(in_array($app['status'], ['pending', 'confirmed'])): ?>
                            <form action="index.php?page=patient&action=update_payment" method="POST" style="display: flex; gap: 5px;">
                                <?= csrf_field() ?>
                                <input type="hidden" name="appointment_id" value="<?= $app['id'] ?>">
                                <select name="payment_method" class="form-control" style="padding: 4px; width: 130px;">
                                    <option value="" <?= empty($app['payment_method']) ? 'selected' : '' ?>>Select Method</option>
                                    <option value="cash" <?= $app['payment_method'] === 'cash' ? 'selected' : '' ?>>Cash (At Clinic)</option>
                                    <option value="card" <?= $app['payment_method'] === 'card' ? 'selected' : '' ?>>Credit/Debit Card</option>
                                    <option value="mobile_banking" <?= $app['payment_method'] === 'mobile_banking' ? 'selected' : '' ?>>Mobile Banking</option>
                                    <option value="insurance" <?= $app['payment_method'] === 'insurance' ? 'selected' : '' ?>>Health Insurance</option>
                                </select>
                                <button type="submit" class="btn" style="padding: 4px 8px; font-size: 0.8rem;">Save</button>
                            </form>
                        <?php else: ?>
                            <?= esc(ucfirst(str_replace('_', ' ', $app['payment_method'] ?: 'Not Selected'))) ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($app['status'] === 'completed'): ?>
                            <a href="index.php?page=patient&action=prescriptions" class="btn btn-success" style="font-size: 0.8rem;">View Prescription</a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </table>
    </div>

<!-- ========================================== -->
<!-- FEATURE 1: SERIAL CHECKING & BOOKING       -->
<!-- ========================================== -->
<?php elseif($action === 'book'): ?>
    
    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <h3>Book New Appointment</h3>
        <p>Select a doctor and date to automatically generate your serial number in the queue.</p>
        
        <form action="index.php?page=patient&action=save_booking" method="POST">
            <?= csrf_field() ?>
            
            <div class="form-group">
                <label>Select Doctor <span style="color:red;">*</span></label>
                <select name="doctor_id" class="form-control" required onchange="showSchedule(this.value)">
                    <option value="">-- Choose a Doctor --</option>
                    <?php foreach($doctors as $doc): ?>
                        <option value="<?= $doc['id'] ?>">Dr. <?= esc($doc['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div id="schedule-info" style="margin-bottom: 15px; padding: 10px; background: #eef8fc; border-radius: 4px; display: none;">
                <strong>Doctor's Visiting Schedule:</strong>
                <ul id="schedule-list" style="margin: 5px 0 0; padding-left: 20px;"></ul>
            </div>
            
            <div style="display: flex; gap: 15px;">
                <div class="form-group" style="flex: 1;">
                    <label>Preferred Date <span style="color:red;">*</span></label>
                    <input type="date" name="appointment_date" class="form-control" required min="<?= date('Y-m-d') ?>">
                </div>
                
                <div class="form-group" style="flex: 1;">
                    <label>Preferred Time <span style="color:red;">*</span></label>
                    <input type="time" name="appointment_time" class="form-control" required>
                </div>
            </div>
            
            <div class="form-group">
                <label>Problem / Notes (Optional)</label>
                <textarea name="notes" class="form-control" rows="3" placeholder="Describe your dental issue..."></textarea>
            </div>
            
            <button type="submit" class="btn" style="width: 100%;">Confirm Booking & Get Serial</button>
        </form>
    </div>
    
    <script>
        const schedules = <?= json_encode($schedules) ?>;
        
        function showSchedule(doctorId) {
            const infoDiv = document.getElementById('schedule-info');
            const list = document.getElementById('schedule-list');
            list.innerHTML = '';
            
            if (!doctorId || !schedules[doctorId] || schedules[doctorId].length === 0) {
                list.innerHTML = '<li>No schedule available for this doctor. Please contact reception.</li>';
                infoDiv.style.display = 'block';
                return;
            }
            
            schedules[doctorId].forEach(s => {
                // Convert 24h to 12h AM/PM
                const start = new Date('1970-01-01T' + s.start_time).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                const end = new Date('1970-01-01T' + s.end_time).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                
                const li = document.createElement('li');
                li.textContent = `${s.day_of_week}: ${start} - ${end}`;
                list.appendChild(li);
            });
            infoDiv.style.display = 'block';
        }
    </script>

<!-- ========================================== -->
<!-- FEATURE 2: VIEW PRESCRIPTIONS              -->
<!-- ========================================== -->
<?php elseif($action === 'prescriptions'): ?>
    
    <div class="card">
        <h3>My Medical Prescriptions</h3>
        
        <?php if(empty($history)): ?>
            <p style="text-align: center; color: #888;">No prescriptions found. Once a doctor completes your appointment, your prescription will appear here.</p>
        <?php else: ?>
            <?php foreach($history as $hist): ?>
            <div style="border: 1px solid #ddd; padding: 20px; margin-bottom: 20px; border-radius: 8px; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <div style="display: flex; justify-content: space-between; border-bottom: 2px solid #0077b6; padding-bottom: 10px; margin-bottom: 15px;">
                    <div>
                        <h3 style="margin: 0; color: #0077b6;">Clinic Prescription</h3>
                        <p style="margin: 5px 0 0;"><strong>Dr. <?= esc($hist['doctor_name']) ?></strong></p>
                    </div>
                    <div style="text-align: right;">
                        <h4 style="margin: 0; color: #555;">Date: <?= nice_date($hist['appointment_date']) ?></h4>
                        <p style="margin: 5px 0 0;">Ref: #<?= esc($hist['id']) ?></p>
                    </div>
                </div>
                
                <p><strong>Diagnosis:</strong> <?= esc($hist['diagnosis']) ?></p>
                <?php if(!empty($hist['notes'])): ?>
                    <p><strong>Advice:</strong> <?= esc($hist['notes']) ?></p>
                <?php endif; ?>
                
                <?php if(!empty($hist['medicines'])): ?>
                    <h4 style="margin-top: 20px; border-bottom: 1px dashed #ccc; padding-bottom: 5px;">Rx (Medicines)</h4>
                    <table style="width: 100%; border: none; margin-top: 10px;">
                        <tr style="background: #f8f9fa;">
                            <th style="padding: 8px; border: none; border-bottom: 1px solid #ddd; text-align: left;">Medicine Name</th>
                            <th style="padding: 8px; border: none; border-bottom: 1px solid #ddd; text-align: left;">Dosage</th>
                            <th style="padding: 8px; border: none; border-bottom: 1px solid #ddd; text-align: left;">Instructions</th>
                        </tr>
                        <?php foreach($hist['medicines'] as $med): ?>
                            <tr>
                                <td style="padding: 8px; border: none; border-bottom: 1px solid #eee;"><strong><?= esc($med['medicine_name']) ?></strong></td>
                                <td style="padding: 8px; border: none; border-bottom: 1px solid #eee;"><?= esc($med['dosage']) ?></td>
                                <td style="padding: 8px; border: none; border-bottom: 1px solid #eee;"><?= esc($med['instructions']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php endif; ?>
                <div style="margin-top: 20px; text-align: right;">
                    <button class="btn" onclick="window.print()" style="background: #6c757d;">🖨️ Print Prescription</button>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

<?php endif; ?>

<?php require __DIR__ . '/../partials/footer.php'; ?>
