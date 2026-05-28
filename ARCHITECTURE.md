# 📐 Arsitektur Sistem KKN-APP

## Gambaran Umum

Sistem KKN-APP mengimplementasikan **MVC (Model-View-Controller)** Architecture dengan menggunakan framework **Laravel 11** dan **Bootstrap 5** sebagai UI framework. Diagram berikut menunjukkan alur data dan interaksi antar komponen:

```
                    ┌─────────────────┐
                    │   Bootstrap 5   │
                    │   Admin LTE UI  │
                    └────────┬────────┘
                             │
                    ┌────────▼────────┐
                    │     Browser     │  ◄── User Interaction
                    └────────┬────────┘
                             │
          ┌──────────────────┼──────────────────┐
          │                  │                  │
    ┌─────▼──────┐    ┌────────────┐    ┌─────▼──────┐
    │   Routes   │    │    View    │    │  Assets    │
    │  (web.php) │    │  (Blade)   │    │ (CSS/JS)   │
    └─────┬──────┘    └────────────┘    └────────────┘
          │                  ▲
          │ Forward Request  │ Render
          │                  │
    ┌─────▼──────────────────┴──────┐
    │      Controller Layer         │
    │  - KelompokController        │
    │  - PesertaController         │
    │  - DplController             │
    │  - AplController             │
    │  - DashboardController       │
    │  - PeriodeController         │
    └─────┬──────────────────────────┘
          │
    ┌─────▼──────────────────────────┐
    │       Model Layer              │
    │  - Kelompok                    │
    │  - Peserta                     │
    │  - Dpl                         │
    │  - Apl                         │
    │  - Periode                     │
    │  - User                        │
    │  - TuanRumah                   │
    │  - LogActivity                 │
    └─────┬──────────────────────────┘
          │
    ┌─────▼──────────────────────────┐
    │    Query Request / Response    │
    └─────┬──────────────────────────┘
          │
    ┌─────▼──────────────────────────┐
    │       MySQL Database           │
    │  (XAMPP Database Engine)       │
    └────────────────────────────────┘
```

---

## 1. 🎨 **Layer Presentasi (Presentation Layer)**

### Browser & User Interaction
- **Framework**: Bootstrap 5 + Admin LTE
- **Template Engine**: Laravel Blade
- **Responsive Design**: Mobile-first approach
- **Features**: 
  - Dashboard dengan statistik
  - Form input untuk data entry
  - Tabel dengan DataTable untuk display data
  - Export PDF & Excel functionality

### Main Views Structure
```
resources/views/
├── layouts/
│   ├── app.blade.php              # Master layout
│   ├── sidebar_admin.blade.php    # Admin sidebar
│   ├── sidebar_peserta.blade.php  # Peserta sidebar
│   ├── sidebar_dpl.blade.php      # DPL sidebar
│   └── sidebar_apl.blade.php      # APL sidebar
├── dashboard/                      # Dashboard views
├── kelompok/                       # Kelompok management
├── peserta/                        # Peserta views
├── dpl/                            # DPL views
├── apl/                            # APL views
└── periode/                        # Periode management
```

---

## 2. 🔀 **Layer Routing (Route Handler)**

### Route File: `routes/web.php`

**Authentication Routes:**
```php
GET  /login              → AuthController@showLogin
POST /login              → AuthController@login
GET  /logout             → AuthController@logout
```

**Admin Routes (middleware: ceklogin + role:admin):**
```php
GET    /dashboard        → DashboardController@index
GET    /periode          → PeriodeController@index
POST   /periode/store    → PeriodeController@store
GET    /kelompok         → KelompokController@index
POST   /kelompok/store   → KelompokController@store
GET    /apl              → AplController@index
POST   /apl/store        → AplController@store
GET    /dpl              → DplController@index
POST   /dpl/store        → DplController@store
POST   /simpan-hasil     → KelompokController@simpanHasil
```

**Peserta Routes (middleware: ceklogin + role:peserta):**
```php
GET /hasil-peserta       → PesertaController@hasil
```

**DPL Routes (middleware: ceklogin + role:dpl):**
```php
GET /dpl-view            → DplController@hasilView
GET /dpl-view/detail/:id → DplController@detailView
```

**APL Routes (middleware: ceklogin + role:apl):**
```php
GET /hasil-apl-new       → AplController@hasilNew
GET /hasil-apl-new/detail/:id → AplController@detailNew
```

---

## 3. 🎮 **Layer Controller (Business Logic)**

### Controller Files Location
```
app/Http/Controllers/
├── AuthController.php           # Authentication logic
├── DashboardController.php      # Dashboard & reports
├── KelompokController.php       # Kelompok management
├── PesertaController.php        # Peserta management
├── DplController.php            # DPL management
├── AplController.php            # APL management
├── PeriodeController.php        # Periode management
├── ImportController.php         # Data import (Excel)
└── LogAktivitasController.php   # Activity logging
```

### Key Controller Methods

#### **KelompokController**
- `index()` - Tampilkan daftar kelompok
- `create()` - Form tambah kelompok
- `store()` - Simpan kelompok baru
- `edit()` - Form edit kelompok
- `update()` - Update kelompok
- `randomisasi()` - Acak peserta ke kelompok
- `simpanHasil()` - Simpan hasil pembagian
- `hasilPembagian()` - Tampilkan hasil pembagian
- `assignDpl()` - Assign DPL ke kelompok
- `assignApl()` - Assign APL ke kelompok
- `exportExcel()` - Export ke Excel
- `exportPDF()` - Export ke PDF
- `publish()` - Publish hasil pembagian
- `unpublish()` - Unpublish hasil pembagian

#### **PesertaController**
- `hasil()` - Tampilkan hasil bagi untuk peserta
- `tempatkan()` - Tempatkan peserta ke kelompok
- `pindah()` - Pindah peserta ke kelompok lain
- `tukar()` - Tukar peserta antar kelompok
- `halamanPindah()` - Form pindah peserta
- `halamanTukar()` - Form tukar peserta

---

## 4. 📦 **Layer Model (Data Access Layer)**

### Model Files Location
```
app/Models/
├── User.php              # User authentication
├── Periode.php           # Periode data
├── Kelompok.php          # Kelompok data
├── Peserta.php           # Peserta/participant data
├── Apl.php               # APL (field mentor) data
├── Dpl.php               # DPL (field supervisor) data
├── TuanRumah.php         # Host/employer data
└── LogActivity.php       # Activity log
```

### Model Relationships

**Periode Model:**
```
hasMany: Kelompok
hasMany: Peserta
hasMany: Apl
hasMany: Dpl
```

**Kelompok Model:**
```
belongsTo: Periode
hasMany: Peserta
belongsTo: Apl (as: apl_data)
belongsTo: Dpl (as: dpl_data)
belongsTo: TuanRumah
```

**Peserta Model:**
```
belongsTo: Periode
belongsTo: Kelompok
belongsTo: User
```

**Apl Model:**
```
belongsTo: Periode
hasMany: Kelompok (melalui relasi)
```

**Dpl Model:**
```
belongsTo: Periode
hasMany: Kelompok (melalui relasi)
```

---

## 5. 💾 **Database Layer (MySQL)**

### Database Tables Structure

#### **users**
- id, name, email, password, role, created_at, updated_at

#### **periode**
- id_periode, nama_periode, tahun, semester, status, status_publish, created_at, updated_at

#### **kelompok**
- id_kelompok, nomor_kelompok, desa, dusun, alamat, id_periode, id_apl, id_dpl, kapasitas, latitude, longitude, status, created_at, updated_at

#### **peserta**
- id_peserta, nim, nama, email, prodi, gender, no_hp, user_id, id_periode, id_kelompok, created_at, updated_at

#### **apl** (Field Mentor)
- nim, nama, email, prodi, fakultas, user_id, id_periode, created_at, updated_at

#### **dpl** (Field Supervisor)
- nik, nama, email, prodi, fakultas, nidn, user_id, id_periode, created_at, updated_at

#### **tuan_rumah** (Host)
- id_tuan_rumah, nama, alamat, no_hp, desa, dusun, status

#### **log_activities**
- id, user_id, action, description, table_name, record_id, created_at

---

## 6. 🔐 **Authentication & Authorization**

### Middleware
```php
ceklogin              # Check if user is authenticated
role:admin            # Only admin access
role:peserta          # Only peserta access
role:dpl              # Only DPL access
role:apl              # Only APL access
```

### User Roles
- **Admin**: Manage semua data, periode, randomisasi
- **Peserta**: View hasil pembagian kelompok
- **APL**: View kelompok yang ditugaskan
- **DPL**: View kelompok yang ditugaskan

---

## 7. 📊 **Data Flow Example: Create Kelompok**

```
1. User Browser
   └─→ Klik "Tambah Kelompok" di Admin Dashboard

2. Route Layer
   └─→ GET /kelompok/create
       └─→ KelompokController@create

3. Controller
   └─→ Load form view dengan data tuan_rumah
       └─→ return view('kelompok.create')

4. View Layer
   └─→ resources/views/kelompok/create.blade.php
       └─→ Render HTML form dengan Bootstrap styling

5. User Input
   └─→ Fill form & submit
       └─→ POST /kelompok/store

6. Controller
   └─→ Validate input
       └─→ Create Model instance
       └─→ Call Model save method

7. Model
   └─→ Kelompok::create($data)
       └─→ Query Builder prepare data

8. Database
   └─→ INSERT INTO kelompok (...)
       └─→ Return inserted record

9. Controller
   └─→ Redirect dengan success message
       └─→ return redirect('/kelompok')

10. Browser
    └─→ Display success notification
        └─→ Show updated kelompok list
```

---

## 8. 🎯 **Key Features Architecture**

### Feature: Randomisasi & Pembagian Kelompok

```
Input Phase:
  User (Admin) → Select Periode → View APL/DPL/Peserta

Randomization Phase:
  → KelompokController@randomisasi
  → Random assign Peserta to Kelompok
  → Maintain Kelompok capacity & constraints

Assignment Phase:
  → KelompokController@assignDpl
  → KelompokController@assignApl
  → Update Kelompok.id_dpl & Kelompok.id_apl

Publish Phase:
  → KelompokController@publish
  → Set Periode.status_publish = 1
  → Lock all changes (validation in update methods)

Display Phase:
  → DashboardController@detail
  → PesertaController@hasil (for peserta role)
  → DplController@hasilView (for dpl role)
  → AplController@hasilNew (for apl role)
```

### Feature: Data Import/Export

```
Import Flow:
  → ImportController@index (show form)
  → ImportController@preview (preview data)
  → ImportController@store (import with validation)
  → Uses: Maatwebsite/Excel package
  → Models: Peserta, Apl, Dpl

Export Flow:
  → KelompokController@exportExcel
  → KelompokController@exportPDF
  → Uses: Maatwebsite/Excel & Barryvdh/DomPDF
  → Generate file & download
```

---

## 9. 📦 **External Packages**

```
composer.json Dependencies:
- laravel/framework         # Core framework
- laravel/tinker            # Interactive console
- maatwebsite/laravel-excel # Excel import/export
- barryvdh/laravel-dompdf   # PDF generation
- spatie/laravel-activitylog # Activity logging
```

---

## 10. 🚀 **Technology Stack Summary**

| Layer | Technology |
|-------|-----------|
| **Frontend** | HTML5, CSS3 (Bootstrap 5), JavaScript, Blade |
| **Backend** | PHP 8.1+, Laravel 11 |
| **Database** | MySQL 5.7+ |
| **Server** | Apache (XAMPP) |
| **Build Tool** | Vite |
| **Package Manager** | Composer (PHP), NPM (Node.js) |
| **UI Framework** | Bootstrap 5, Admin LTE |

---

## 11. 📝 **Development Workflow**

### Setting Routes → Controller → Model → Database

```
1. Define Route in routes/web.php
   ↓
2. Create/Update Controller with action method
   ↓
3. Create/Update Model with relationships
   ↓
4. Run Migration to create/modify database tables
   ↓
5. Create/Update Blade view templates
   ↓
6. Test via browser
```

---

## 12. 🔍 **File Locations Quick Reference**

```
📦 Project Root: c:\xampp\htdocs\kkn-app\

├── 📁 app/
│   ├── Http/Controllers/      # Business logic
│   ├── Models/                # Database models
│   ├── Exports/               # Export classes
│   └── Imports/               # Import classes
│
├── 📁 routes/
│   └── web.php                # Route definitions
│
├── 📁 resources/
│   ├── views/                 # Blade templates
│   ├── css/                   # Stylesheets
│   └── js/                    # JavaScript files
│
├── 📁 database/
│   ├── migrations/            # Database schema
│   ├── seeders/               # Sample data
│   └── factories/             # Test data generators
│
├── 📁 config/
│   ├── app.php                # App configuration
│   ├── database.php           # Database configuration
│   └── ...
│
├── 📁 storage/
│   ├── app/                   # User uploads
│   └── logs/                  # Application logs
│
└── 📁 public/
    ├── index.php              # Entry point
    ├── css/                   # Compiled CSS
    └── js/                    # Compiled JS
```

---

## 📚 **Additional Resources**

- **Laravel Documentation**: https://laravel.com/docs
- **Bootstrap Documentation**: https://getbootstrap.com/docs
- **MySQL Documentation**: https://dev.mysql.com/doc
- **Blade Template Engine**: https://laravel.com/docs/blade
- **Eloquent ORM**: https://laravel.com/docs/eloquent

---

**Generated**: May 28, 2026
**Version**: 1.0
**Status**: Active
