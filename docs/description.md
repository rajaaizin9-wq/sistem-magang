1. Ringkasan Eksekutif
SIMAGANG adalah sistem informasi untuk mengintegrasikan proses magang mahasiswa mulai dari pendaftaran, administrasi, penentuan dosen pembimbing, konversi mata kuliah pilihan, pelaksanaan, logbook, seminar bila diperlukan, pelaporan, dan penilaian.
Dokumen ini adalah baseline perencanaan sebelum coding. Fokusnya adalah memastikan setiap fitur memiliki hubungan yang jelas dengan role, UI, workflow, database, keamanan, dan testing.
2. Requirement yang Telah Dikunci
Nama sistem: SIMAGANG.
Bag. TU.
Wadek 1 ikut memverifikasi data/pengajuan konversi.
Konversi hanya untuk mata kuliah pilihan.
Seminar bersifat opsional sesuai keputusan dosen mata kuliah.
Mahasiswa boleh memiliki lebih dari satu riwayat/periode magang.
Logbook wajib dan harus diverifikasi dosen.
Penilaian akhir berasal dari dosen.
Daftar jenis dokumen wajib belum ditentukan, sehingga tidak diasumsikan. yang pasti harus ada surat pengantar, surat balasan instansi atau surat pendukung dari instansi.
3. Aktor Sistem
Role


Tanggung Jawab


Mahasiswa


Pendaftaran, dokumen, pelaksanaan, logbook, konversi MK pilihan, laporan.


Bag. TU


Administrasi, verifikasi dokumen, pengelolaan data dan monitoring.


Kaprodi


Menentukan dosen pembimbing dan monitoring.


Dosen Pembimbing


Memantau & memverifikasi logbook, memverifikasi konversi.


Dosen Mata Kuliah


Memverifikasi konversi dan memberikan penilaian.


Wadek 1


Ikut memverifikasi data/pengajuan konversi.




4. Fitur
4.1 MVP / Wajib
Login/logout dan role-based access.
Pendaftaran dan riwayat magang; satu mahasiswa dapat memiliki lebih dari satu periode.
Dokumen wajib: sistem mendukung daftar dokumen wajib dan verifikasi; jenis dokumen final masih perlu ditentukan.
Administrasi dan status pengajuan.
Penentuan dosen pembimbing oleh Kaprodi.
Konversi hanya untuk mata kuliah pilihan.
Verifikasi konversi oleh Dosen MK → Dosen Pembimbing → Wadek 1.
Pelaksanaan magang dan logbook wajib.
Verifikasi logbook oleh dosen.
Seminar opsional sesuai keputusan dosen.
Laporan akhir dan penilaian akhir oleh dosen.
Dashboard progress dan audit aktivitas.
4.2 Prioritas Berikutnya
Notifikasi email/sistem.
Export PDF/Excel.
Dashboard statistik lanjutan.
Template surat otomatis.
Object storage untuk dokumen.
6. User Flow
6.1 Mahasiswa
Login → Dashboard → Pendaftaran → Upload dokumen → TU verifikasi → Administrasi → Kaprodi menentukan pembimbing → Konversi MK pilihan jika diperlukan → Dosen MK verifikasi → Dosen Pembimbing verifikasi → Wadek 1 verifikasi → Pelaksanaan → Logbook → Verifikasi dosen → Magang selesai → Seminar jika diminta dosen → Laporan akhir → Penilaian dosen → Selesai.
6.2 Konversi
Mahasiswa memilih MK pilihan → Submit → Dosen Mata Kuliah → Dosen Pembimbing → Wadek 1 → Approved/Rejected.
6.3 Logbook
Mahasiswa mengisi → Submit → Dosen memeriksa → Verified atau Revision Required → jika revisi, mahasiswa memperbaiki dan submit kembali.
6.4 Seminar
Seminar tidak otomatis wajib. Seminar hanya dijalankan apabila dosen memutuskan bahwa mahasiswa perlu melakukan seminar.
7. Status Workflow
Magang: DRAFT → SUBMITTED → ADMIN_REVIEW → REVISION_REQUIRED → ADMIN_APPROVED → SUPERVISOR_ASSIGNED → INTERNSHIP_ACTIVE → INTERNSHIP_COMPLETED → REPORT_SUBMITTED → ASSESSMENT → COMPLETED
Konversi: DRAFT → SUBMITTED → LECTURER_REVIEW → SUPERVISOR_REVIEW → VICE_DEAN_REVIEW → APPROVED / REJECTED
Logbook: DRAFT → SUBMITTED → REVISION_REQUIRED → RESUBMITTED → VERIFIED
8. UI/UX Planning
Style modern, profesional, akademik, bersih, tidak terlalu ramai.
Mobile, tablet, dan desktop responsive.
Primary navy/blue; secondary slate; background terang; status menggunakan warna semantik.
Komponen konsisten: button, input, select, textarea, card, badge, alert, table, pagination, tabs, stepper, modal, timeline, file upload.
Dashboard mahasiswa menampilkan progress, status dokumen, pembimbing, logbook, dan action yang perlu dilakukan.
Dashboard staff menampilkan pengajuan yang menunggu tindakan, filter, tabel, dan status.
9. Tech Stack
Komponen


Pilihan


Alasan


Frontend


Tailwind CSS


Cocok untuk form, dashboard, tabel, dan workflow administrasi; sederhana untuk MVP.


Backend


Codeigniter 4 / PHP


Routing, validation, ORM, authentication, authorization, storage, migration.


Database


ySQL


Relational database dan foreign key kuat.


Authentication


Authentication


Session, hashing, middleware, authorization.


Storage


Local development → object storage bila diperlukan


Cocok untuk dokumen.


Deployment


Linux VPS/Cpanel


Mendukung PHP, database, storage, domain, dan HTTPS.




10. Database Architecture
Model database harus mendukung satu mahasiswa memiliki banyak riwayat/periode magang.
Tabel


Fungsi


Field Utama


users


Akun


id PK, name, email, password, role, is_active


program_studies


Program studi


id PK, code, name


students


Profil mahasiswa


id PK, user_id FK, nim, program_study_id FK, phone, address


lecturers


Profil dosen


id PK, user_id FK, nidn, program_study_id FK


internships


Riwayat/periode magang


id PK, student_id FK, company_name, address, position, start_date, end_date, status


internship_documents


Dokumen magang


id PK, internship_id FK, type, file_path, status, uploaded_by, verified_by, verified_at


supervisor_assignments


Pembimbing


id PK, internship_id FK, lecturer_id FK, assigned_by, status


internship_logbooks


Logbook wajib


id PK, internship_id FK, week, date, activity, result, obstacle, attachment, status, verified_by, notes


courses


Mata kuliah


id PK, code, name, credits, program_study_id FK, lecturer_id FK, is_elective


conversion_requests


Pengajuan konversi


id PK, internship_id FK, student_id FK, status, notes


conversion_request_courses


MK yang dipilih


id PK, conversion_request_id FK, course_id FK, status


conversion_approvals


Approval konversi


id PK, conversion_id FK, approver_id FK, role, status, notes, approved_at


seminars


Seminar opsional


id PK, internship_id FK, is_required, requested_by, schedule, location, status


assessments


Penilaian dosen


id PK, internship_id FK, assessor_id FK, assessment_type, score, notes


notifications


Notifikasi


id PK, user_id FK, title, message, type, read_at


activity_logs


Audit


id PK, user_id FK, action, module, description, ip_address, created_at




10.1 ERD 


11. API Planning
Method


Endpoint


Fungsi


Auth


Role


POST


/api/login


Login


Tidak


Semua


POST


/api/logout


Logout


Ya


Semua


GET


/api/profile


Profil


Ya


Semua


POST


/api/internships


Buat pengajuan


Ya


Mahasiswa


GET


/api/internships


Daftar/detail sesuai hak akses


Ya


Sesuai role


PUT


/api/internships/{id}


Update pengajuan


Ya


Mahasiswa


POST


/api/documents


Upload dokumen


Ya


Mahasiswa/TU


POST


/api/documents/{id}/verify


Verifikasi dokumen


Ya


TU


POST


/api/supervisors


Tentukan pembimbing


Ya


Kaprodi


POST


/api/logbooks


Buat logbook


Ya


Mahasiswa


POST


/api/logbooks/{id}/verify


Verifikasi/revisi logbook


Ya


Dosen Pembimbing


POST


/api/conversions


Ajukan konversi MK pilihan


Ya


Mahasiswa


POST


/api/conversions/{id}/approve


Approval bertahap


Ya


Dosen MK/Dospem/Wadek


POST


/api/seminars


Kelola seminar jika diperlukan


Ya


Mahasiswa/Dosen


POST


/api/assessments


Input nilai


Ya


Dosen




13. Security Checklist
Password hashing; authentication; role-based authorization dan policy.
Server-side input validation; SQL Injection protection melalui ORM/query binding; XSS protection; CSRF.
Rate limiting pada endpoint sensitif.
File upload: validasi MIME/ekstensi/ukuran dan blokir file executable.
Environment variables untuk secret/credential; jangan commit .env.
Production error tidak menampilkan stack trace sensitif.
HTTPS pada production.
Audit log untuk aktivitas penting.
Backup database dan dokumen.
14. Testing Plan
Jenis


Contoh


Functional


Pendaftaran, upload, verifikasi, pembimbing, konversi, logbook, seminar, penilaian.


UI/Responsive


Form, tabel, navigasi, mobile 360/390/430 px, tablet, desktop.


API


Validation, status code, auth, response.


Authentication


Login, logout, reset password, session.


Authorization


Role dan kepemilikan data.


Database


Foreign key, constraint, transaction.


Security


CSRF, XSS, SQL injection, upload, rate limit, secrets.


Performance


Query, pagination, upload, dashboard.




15. Deployment
Siapkan source dan asset production.
Konfigurasi server PHP/web server/database.
Buat database production dan jalankan migration.
Siapkan .env production secara aman.
Konfigurasi storage dokumen.
Konfigurasi domain dan HTTPS.
Lakukan smoke test seluruh workflow.
Aktifkan backup dan prosedur pemulihan.
16. Dokumentasi
README
Installation Guide
Database Documentation
API Documentation
User Guide
Admin Guide
Lecturer Guide
Deployment Guide
Change Log
17. Estimasi Kompleksitas
Fitur


Level


Login/logout


🟢 Mudah


Pendaftaran


🟡 Sedang


Dokumen wajib


🟡 Sedang


Penentuan pembimbing


🟢 Mudah


Konversi MK pilihan


🔴 Sulit


Logbook + verifikasi


🟡 Sedang


Seminar opsional


🟡 Sedang


Penilaian dosen


🟡 Sedang


Multi-role authorization


🔴 Sulit


Audit log


🟡 Sedang




19. Keputusan Final & Yang Masih Terbuka
Item


Status


Nama SIMAGANG


FINAL


Bag. TU = Administrasi surat


FINAL


Wadek 1 ikut verifikasi konversi


FINAL


Konversi hanya MK pilihan


FINAL


Seminar opsional sesuai keputusan dosen


FINAL


Mahasiswa dapat >1 riwayat/periode magang


FINAL


Logbook wajib dan diverifikasi dosen


FINAL


Penilaian akhir berasal dari dosen


PERLU DITENTUKAN
Jenis dokumen wajib


PERLU DITENTUKAN


Rubrik/bobot nilai


PERLU DITENTUKAN


Batas jumlah MK/SKS konversi


PERLU DITENTUKAN




20. Final Project Blueprint
SIMAGANG adalah sistem web terintegrasi yang memusatkan seluruh proses magang. MVP memprioritaskan workflow inti dan tidak menambahkan fitur yang tidak memiliki manfaat jelas.
Konsep: digitalisasi proses magang dari pendaftaran sampai penilaian.
Target: Mahasiswa, TU, Kaprodi, Dosen Pembimbing, Dosen Mata Kuliah, Wadek 1.
Tech stack: Codeigniter 4 + Tailwind CSS + MySQL.
Database: relational, mendukung banyak riwayat magang.
Workflow utama: Pendaftaran → Administrasi → Pembimbing → Konversi bila ada → Pelaksanaan → Logbook → Seminar bila diminta → Pelaporan → Penilaian.
Security: authentication, RBAC, validation, CSRF, XSS, SQLi protection, upload security, audit log.
Coding dimulai setelah requirement yang masih terbuka diputuskan.


