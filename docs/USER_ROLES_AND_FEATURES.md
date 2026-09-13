# Safe Plus Dental Care - System Roles & Feature Documentation

A comprehensive guide explaining the privileges, capabilities, and workflows of each user role within the **Safe Plus Dental Care Management System**.

---

## System Overview & Architecture
The system supports four distinct user roles working in a unified clinical workflow:
1. **Administrator** – System governance, account auditing, clinic inventory, and visiting schedules.
2. **Doctor** – Consultation queues, digital prescription generation, and electronic medical histories.
3. **Patient** – Schedule discovery, live serial booking, payment method selection, and prescription viewing.
4. **Receptionist** – Booking verification/confirmation, billing & invoicing, and equipment delegation.

---

## 1. Administrator (System Governance)

### Primary Responsibility
The Administrator oversees the entire clinical ecosystem, ensuring account security, auditing clinical equipment, monitoring high-level clinic metrics, and managing doctor duty schedules.

### Key Capabilities & Features
* **User Management (CRUD):**
  * View all registered doctors, receptionists, and patients.
  * Direct user creation (adding doctors, receptionists, or patients with initial credentials).
  * Approve pending registrations: Doctors and Receptionists require administrator approval before they can access the system.
  * Suspend/Reactivate accounts to control platform access.
* **Clinical Equipment Inventory:**
  * Register new surgical instruments, chairs, or diagnostic machinery with quantity and status.
  * Monitor equipment availability (`Available`, `In Use`, `Maintenance`) and see which doctor is currently utilizing an instrument.
* **Monthly Clinic Reports & Monitoring:**
  * Real-time metrics overview displaying total registered users, doctors, patients, receptionists, and pending approval requests.
* **Doctor Availability & Shift Scheduling:**
  * Define weekly consultation rosters for doctors (selecting day of the week, starting time, and ending time).
  * Remove or adjust expired or unavailable shifts.

---

## 2. Doctor (Clinical Consultations)

### Primary Responsibility
The Doctor conducts patient consultations, reviews previous medical cases, and generates electronic prescriptions with detailed medication instructions.

### Key Capabilities & Features
* **Live Appointment Queue:**
  * Access a filtered queue showing confirmed and pending appointments scheduled for that specific doctor.
  * View appointment date, time, patient contact info, and patient-reported symptoms.
* **Digital Prescription Generation:**
  * Launch a consultation interface directly from the appointment queue.
  * Document clinical diagnosis and problem details.
  * Provide medical advice and precautions.
  * Dynamically add multiple medicines with specific dosage instructions (e.g., `1+0+1`, `After meal for 5 days`).
  * Submitting the prescription automatically updates the appointment status to `Completed`.
* **Patient History & Case Records:**
  * View the list of patients who have received consultations from this doctor.
  * Inspect complete historical medical records, past diagnoses, and past prescribed medications for each patient.

---

## 3. Patient (Self-Service Portal)

### Primary Responsibility
Patients can check doctor availability, book appointments, track queue serial numbers, designate payment preferences, and access diagnostic prescriptions.

### Key Capabilities & Features
* **Live Schedule Discovery & Serial Booking:**
  * View active clinic doctors and dynamically inspect their visiting days and shift hours.
  * Choose preferred date and time for dental consultations.
  * Submit problem notes.
  * Automatic serial number generation based on existing bookings for that day.
* **Payment Method Selection:**
  * Select payment method (`Cash at Clinic`, `Credit/Debit Card`, `Mobile Banking`, `Health Insurance`) for pending or confirmed bookings.
* **Electronic Prescription Access:**
  * View completed medical prescriptions issued by the attending dentist.
  * Inspect diagnosed conditions, doctor advice, and prescribed medications in a clean RX format.
  * Built-in print capability for physical copies.

---

## 4. Receptionist (Front Desk Operations)

### Primary Responsibility
The Receptionist acts as the bridge between patients and doctors, confirming incoming appointment requests, issuing treatment bills, collecting payments, and allocating tools.

### Key Capabilities & Features
* **Appointment Scheduling & Status Control:**
  * Monitor all clinic-wide appointments across all doctors.
  * Confirm patient bookings (`Pending` -> `Confirmed`) or cancel invalid bookings.
* **Treatment Billing & Invoicing:**
  * View all completed consultations awaiting billing.
  * Generate itemized invoices with total payable amounts.
  * Track payment status (`Unpaid`, `Partial`, `Paid`).
  * Process incoming patient payments and update records in real time.
* **Clinical Equipment Assignment:**
  * Inspect available tools in the clinic inventory.
  * Assign necessary tools (e.g., Dental Scalers, X-Ray Machines) to a specific doctor on duty, automatically marking them as `In Use`.
  * Release instruments back to `Available` stock when the shift ends.

---

## End-to-End Clinical Flowchart

```text
[Patient]                                  [Receptionist]                             [Doctor]
   │                                             │                                       │
   ├─► Books Appointment ───────────────────────►│                                       │
   │   (Gets Serial #)                           ├─► Confirms Appointment ──────────────►│
   │                                             │                                       ├─► Checks Queue
   │                                             │                                       ├─► Examines Patient
   │                                             │                                       └─► Generates Rx
   │                                             │                                           (Marks Completed)
   │                                             │◄──────────────────────────────────────────┤
   │                                             ├─► Generates Bill / Invoice            │
   ├─► Selects Payment Method ──────────────────►├─► Collects Payment (Status: Paid)     │
   └─► Views & Prints Prescription ◄─────────────┴───────────────────────────────────────┘
```

---

## Default System Credentials (For Evaluation)

| Role | Email | Password | Status |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin@dentalclinic.com` | `admin123` | Active |
| **Doctor** | `doctor@dentalclinic.com` | `doctor123` | Active |
| **Patient** | `karim@gmail.com` | `pass` | Active |
| **Receptionist** | Create via Admin or Signup | *User specified* | Requires Admin Approval |
