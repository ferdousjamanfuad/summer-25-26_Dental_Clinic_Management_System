<?php 
$pageTitle = 'Receptionist Desk';
require __DIR__ . '/../partials/header.php'; 
?>

<h2>Reception Desk</h2>

<div class="nav-tabs">
    <a href="index.php?page=receptionist&action=appointments" class="<?= $action === 'appointments' ? 'active' : '' ?>">Manage Appointments</a>
    <a href="index.php?page=receptionist&action=billing" class="<?= $action === 'billing' ? 'active' : '' ?>">Billing & Invoices</a>
    <a href="index.php?page=receptionist&action=equipment" class="<?= $action === 'equipment' ? 'active' : '' ?>">Assign Equipment</a>
</div>

<?php if(!empty($error)): ?>
    <div class="alert alert-error"><?= esc($error) ?></div>
<?php endif; ?>

<!-- ========================================== -->
<!-- FEATURE 2: MANAGE APPOINTMENTS             -->
<!-- ========================================== -->
<?php if($action === 'appointments'): ?>
    
    <div class="card">
        <h3>All Appointments Schedule</h3>
        <p>Review patient bookings and confirm or cancel their schedule.</p>
        <table>
            <tr>
                <th>Date & Time</th>
                <th>Patient</th>
                <th>Doctor</th>
                <th>Serial</th>
                <th>Current Status</th>
                <th>Update Status</th>
            </tr>
            <?php if(empty($appointments)): ?>
                <tr><td colspan="6" style="text-align: center;">No appointments found.</td></tr>
            <?php else: ?>
                <?php foreach($appointments as $app): ?>
                <tr>
                    <td><?= nice_date($app['appointment_date']) ?> <small><?= date('h:i A', strtotime($app['appointment_time'])) ?></small></td>
                    <td><strong><?= esc($app['patient_name']) ?></strong></td>
                    <td>Dr. <?= esc($app['doctor_name']) ?></td>
                    <td><span style="background: #eef8fc; padding: 2px 6px; border-radius: 5px;">#<?= esc($app['serial_no']) ?></span></td>
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
                        <?php if($app['status'] === 'pending' || $app['status'] === 'confirmed'): ?>
                            <form action="index.php?page=receptionist&action=update_appointment" method="POST" style="display: flex; gap: 5px;">
                                <?= csrf_field() ?>
                                <input type="hidden" name="appointment_id" value="<?= $app['id'] ?>">
                                <select name="status" class="form-control" style="padding: 4px; width: 110px;">
                                    <option value="pending" <?= $app['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="confirmed" <?= $app['status'] === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                    <option value="cancelled" <?= $app['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                </select>
                                <button type="submit" class="btn" style="padding: 4px 8px; font-size: 0.8rem;">Update</button>
                            </form>
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
<!-- FEATURE 1: BILLING & INVOICES              -->
<!-- ========================================== -->
<?php elseif($action === 'billing'): ?>
    
    <div class="card" style="margin-bottom: 20px;">
        <h3>Generate New Bill (Completed Appointments)</h3>
        <table>
            <tr>
                <th>Date</th>
                <th>Patient Name</th>
                <th>Doctor</th>
                <th>Action</th>
            </tr>
            <?php if(empty($unbilled_appointments)): ?>
                <tr><td colspan="4" style="text-align: center;">No unbilled completed appointments.</td></tr>
            <?php else: ?>
                <?php foreach($unbilled_appointments as $app): ?>
                <tr>
                    <td><?= nice_date($app['appointment_date']) ?></td>
                    <td><strong><?= esc($app['patient_name']) ?></strong></td>
                    <td>Dr. <?= esc($app['doctor_name']) ?></td>
                    <td>
                        <form action="index.php?page=receptionist&action=generate_bill" method="POST" style="display: flex; gap: 5px; align-items: center;">
                            <?= csrf_field() ?>
                            <input type="hidden" name="appointment_id" value="<?= $app['id'] ?>">
                            <input type="hidden" name="patient_id" value="<?= $app['patient_id'] ?>">
                            <input type="number" name="total_amount" class="form-control" placeholder="Amount (<?= CURRENCY ?>)" required min="1" step="0.01" style="width: 120px; padding: 4px;">
                            <button type="submit" class="btn btn-success" style="padding: 4px 8px; font-size: 0.8rem;">Create Invoice</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </table>
    </div>

    <div class="card">
        <h3>Invoice & Payment History</h3>
        <table>
            <tr>
                <th>Invoice #</th>
                <th>Date</th>
                <th>Patient Name</th>
                <th>Total Bill</th>
                <th>Paid Amount</th>
                <th>Status</th>
                <th>Receive Payment</th>
            </tr>
            <?php if(empty($bills)): ?>
                <tr><td colspan="7" style="text-align: center;">No bills generated yet.</td></tr>
            <?php else: ?>
                <?php foreach($bills as $b): ?>
                <tr>
                    <td><strong>INV-<?= 1000 + $b['id'] ?></strong></td>
                    <td><?= nice_date($b['appointment_date']) ?></td>
                    <td><?= esc($b['patient_name']) ?></td>
                    <td><?= money($b['total_amount']) ?></td>
                    <td><strong style="color: green;"><?= money($b['paid_amount']) ?></strong></td>
                    <td>
                        <?php if($b['status'] === 'paid'): ?>
                            <span style="color:green; font-weight:bold;">Paid</span>
                        <?php elseif($b['status'] === 'partial'): ?>
                            <span style="color:orange; font-weight:bold;">Partial</span>
                        <?php else: ?>
                            <span style="color:red; font-weight:bold;">Unpaid</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($b['status'] !== 'paid'): ?>
                            <form action="index.php?page=receptionist&action=update_payment" method="POST" style="display: flex; gap: 5px;">
                                <?= csrf_field() ?>
                                <input type="hidden" name="bill_id" value="<?= $b['id'] ?>">
                                <input type="number" name="paid_amount" class="form-control" value="<?= $b['total_amount'] ?>" step="0.01" style="width: 80px; padding: 4px;">
                                <select name="status" class="form-control" style="padding: 4px;">
                                    <option value="paid">Paid</option>
                                    <option value="partial">Partial</option>
                                </select>
                                <button type="submit" class="btn" style="padding: 4px 8px; font-size: 0.8rem;">Update</button>
                            </form>
                        <?php else: ?>
                            <i style="color: #888;">Clear</i>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </table>
    </div>

<!-- ========================================== -->
<!-- FEATURE 3: ASSIGN EQUIPMENT                -->
<!-- ========================================== -->
<?php elseif($action === 'equipment'): ?>
    
    <div class="card">
        <h3>Clinic Equipment Assignment</h3>
        <p>Assign surgical tools or machines to specific doctors during their shift.</p>
        <table>
            <tr>
                <th>Equipment Name</th>
                <th>Description</th>
                <th>Qty</th>
                <th>Status</th>
                <th>Currently Assigned To</th>
                <th>Action</th>
            </tr>
            <?php if(empty($equipment)): ?>
                <tr><td colspan="6" style="text-align: center;">No equipment found in inventory.</td></tr>
            <?php else: ?>
                <?php foreach($equipment as $eq): ?>
                <tr>
                    <td><strong><?= esc($eq['name']) ?></strong></td>
                    <td><?= esc($eq['description']) ?></td>
                    <td><?= esc($eq['quantity']) ?></td>
                    <td>
                        <?php if($eq['status'] === 'available'): ?>
                            <span style="color:green; font-weight:bold;">Available</span>
                        <?php elseif($eq['status'] === 'in_use'): ?>
                            <span style="color:orange; font-weight:bold;">In Use</span>
                        <?php else: ?>
                            <span style="color:red; font-weight:bold;">Maintenance</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?= $eq['assigned_to'] ? 'Dr. ' . esc($eq['doctor_name']) : '<em>None</em>' ?>
                    </td>
                    <td>
                        <?php if($eq['status'] === 'available' || empty($eq['assigned_to'])): ?>
                            <form action="index.php?page=receptionist&action=assign_equipment" method="POST" style="display: flex; gap: 5px;">
                                <?= csrf_field() ?>
                                <input type="hidden" name="equipment_id" value="<?= $eq['id'] ?>">
                                <select name="doctor_id" class="form-control" required style="padding: 4px; width: 140px;">
                                    <option value="">Select Doctor...</option>
                                    <?php foreach($doctors as $doc): ?>
                                        <option value="<?= $doc['id'] ?>">Dr. <?= esc($doc['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="btn" style="padding: 4px 8px; font-size: 0.8rem;">Assign</button>
                            </form>
                        <?php elseif($eq['status'] === 'in_use' && !empty($eq['assigned_to'])): ?>
                            <form action="index.php?page=receptionist&action=unassign_equipment" method="POST">
                                <?= csrf_field() ?>
                                <input type="hidden" name="equipment_id" value="<?= $eq['id'] ?>">
                                <button type="submit" class="btn" style="padding: 4px 8px; font-size: 0.8rem; background: #6c757d;">Release Item</button>
                            </form>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </table>
    </div>

<?php endif; ?>

<?php require __DIR__ . '/../partials/footer.php'; ?>
