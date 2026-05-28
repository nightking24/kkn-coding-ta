# 🔗 Mapping Diagram ke Komponen KKN-APP

## Diagram Referensi: Admin LTE MVC Architecture

Diagram di bawah menunjukkan alur umum yang diterapkan dalam KKN-APP:

```
                    ┌─────────────────┐
                    │  Admin LTE UI   │
                    │  Bootstrap 5    │
                    └────────┬────────┘
                             │
                    ┌────────▼────────┐
                    │  Browser        │
                    │  (User Interact)│
                    └────────┬────────┘
                             │
          ┌──────────────────┼──────────────────┐
          │                  │                  │
    ┌─────▼──────┐    ┌────────────┐    ┌─────▼──────┐
    │   Route    │    │   View     │    │  Assets    │
    │ (web.php)  │    │ (Blade)    │    │ (CSS/JS)   │
    └─────┬──────┘    └────────────┘    └────────────┘
          │                  ▲
          │ Forward Request  │ Render
          │                  │
    ┌─────▼──────────────────┴──────┐
    │      Controller                │
    │  (Business Logic)              │
    └─────┬──────────────────────────┘
          │
    ┌─────▼──────────────────────────┐
    │       Model                    │
    │  (Data Access)                 │
    └─────┬──────────────────────────┘
          │
    ┌─────▼──────────────────────────┐
    │    Query Request/Response      │
    └─────┬──────────────────────────┘
          │
    ┌─────▼──────────────────────────┐
    │    MySQL Database              │
    └────────────────────────────────┘
```

---

## 📍 Mapping Komponen

### 1️⃣ **UI Layer (Admin LTE + Bootstrap)**

| Elemen Diagram | Komponen KKN-APP | Lokasi File |
|---|---|---|
| Admin LTE Interface | Bootstrap 5 + Admin LTE 3 | `resources/views/layouts/app.blade.php` |
| Sidebar Menu | Sidebar Components | `resources/views/layouts/sidebar_*.blade.php` |
| Dashboard | Dashboard View | `resources/views/dashboard/` |
| Forms | Blade Form Templates | `resources/views/kelompok/`, `resources/views/peserta/`, dll |
| Tables | DataTable Components | `resources/views/*/hasil.blade.php` |
| Styling | CSS Bootstrap | `resources/css/app.css` |
| Interactivity | JavaScript | `resources/js/app.js`, `resources/js/bootstrap.js` |

**Contoh Struktur HTML View:**
```blade
@extends('layouts.app')

@section('content')
    <div class="container">
        <!-- Bootstrap Grid System -->
        <div class="row">
            <div class="col-md-12">
                <!-- Gradient Header -->
                <div class="card border-left-primary">
                    <h3>Data Kelompok</h3>
                </div>
                
                <!-- DataTable -->
                <table class="table table-hover" id="tableKelompok">
                    <!-- Table Data -->
                </table>
            </div>
        </div>
    </div>
@endsection
```

---

### 2️⃣ **Route Layer**

| Elemen Diagram | Komponen KKN-APP | Lokasi File |
|---|---|---|
| Route Request Handler | Route Definitions | `routes/web.php` |
| HTTP Methods (GET/POST) | Laravel Route Verbs | `routes/web.php` |
| Middleware | Authentication & Role Check | `app/Http/Middleware/`, `routes/web.php` |
| URL Parameters | Route Parameters | `routes/web.php` |

**Contoh Route Definition:**
```php
// routes/web.php
Route::middleware(['ceklogin', 'role:admin'])->group(function () {
    // GET request → forward to index method
    Route::get('/kelompok', [KelompokController::class, 'index']);
    
    // POST request → forward to store method
    Route::post('/kelompok/store', [KelompokController::class, 'store']);
    
    // GET request with parameter
    Route::get('/kelompok/edit/{id}', [KelompokController::class, 'edit']);
});
```

**Request Flow:**
```
User Action (Click Link/Submit Form)
    ↓
Browser sends HTTP Request
    ↓
Route Matcher (routes/web.php)
    ↓
Execute Middleware (ceklogin, role:admin)
    ↓
Forward to appropriate Controller method
```

---

### 3️⃣ **Controller Layer (Business Logic)**

| Elemen Diagram | Komponen KKN-APP | Lokasi File |
|---|---|---|
| Request Handler | Controller Classes | `app/Http/Controllers/` |
| Business Logic | Controller Methods | Each controller file |
| Data Validation | Form Request Validation | Controller methods |
| Database Queries | Eloquent Queries | Controller methods |
| Response Handler | return view/redirect | Controller methods |

**Contoh Controller Method:**
```php
// app/Http/Controllers/KelompokController.php
public function index(Request $request)
{
    // 1. Get periode_id from session
    $periode_id = session('periode_id') ?? $request->periode_id;
    
    // 2. Call Model to fetch data
    $kelompok = Kelompok::where('id_periode', $periode_id)
                ->with('peserta', 'apl', 'dpl')
                ->paginate(10);
    
    // 3. Return view with data
    return view('kelompok.hasil', [
        'kelompok' => $kelompok,
        'periode_id' => $periode_id
    ]);
}
```

**Controller Methods Mapping:**
| Aksi User | Route | Controller Method | Deskripsi |
|---|---|---|---|
| Click "Kelompok" | GET /kelompok | `index()` | Tampilkan daftar kelompok |
| Click "Tambah" | GET /kelompok/create | `create()` | Show form tambah kelompok |
| Submit Form | POST /kelompok/store | `store()` | Simpan kelompok baru |
| Click "Edit" | GET /kelompok/edit/{id} | `edit()` | Show form edit kelompok |
| Submit Edit | POST /kelompok/update/{id} | `update()` | Update kelompok |
| Click "Hapus" | GET /kelompok/delete/{id} | `delete()` | Hapus kelompok |

---

### 4️⃣ **Model Layer (Data Access)**

| Elemen Diagram | Komponen KKN-APP | Lokasi File |
|---|---|---|
| Database Abstraction | Eloquent Models | `app/Models/` |
| Table Mapping | Protected $table | Each model file |
| Relationships | Model Methods | Each model file |
| Queries | Eloquent Methods | Model or Controller |
| Data Validation | Model Attributes | Model fillable/guarded |

**Contoh Model:**
```php
// app/Models/Kelompok.php
class Kelompok extends Model
{
    protected $table = 'kelompok';
    protected $primaryKey = 'id_kelompok';
    
    // Define Relationships
    public function peserta()
    {
        return $this->hasMany(Peserta::class, 'id_kelompok');
    }
    
    public function apl()
    {
        return $this->belongsTo(Apl::class, 'id_apl', 'nim');
    }
    
    // Query Example (called from Controller)
    public static function getByPeriode($periode_id)
    {
        return self::where('id_periode', $periode_id)
                   ->with('peserta', 'apl', 'dpl')
                   ->get();
    }
}
```

**Model Usage in Controller:**
```php
// In KelompokController
$kelompok = Kelompok::where('id_periode', $periode_id)->first();
```

---

### 5️⃣ **Database Layer (MySQL)**

| Elemen Diagram | Komponen KKN-APP | Lokasi |
|---|---|---|
| Tables | Database Tables | MySQL Database |
| Columns | Table Columns | Database Schema |
| Relationships | Foreign Keys | Database Schema |
| Queries | SQL Statements | Generated by Eloquent |
| Data Storage | Persistent Data | XAMPP MySQL |

**Database Schema Example:**

```sql
-- Periode Table
CREATE TABLE periode (
    id_periode INT PRIMARY KEY AUTO_INCREMENT,
    nama_periode VARCHAR(255),
    tahun INT,
    semester VARCHAR(10),
    status VARCHAR(50),
    status_publish TINYINT DEFAULT 0,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Kelompok Table
CREATE TABLE kelompok (
    id_kelompok INT PRIMARY KEY AUTO_INCREMENT,
    nomor_kelompok INT,
    desa VARCHAR(255),
    dusun VARCHAR(255),
    id_periode INT,
    id_apl VARCHAR(20),
    id_dpl VARCHAR(20),
    kapasitas INT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (id_periode) REFERENCES periode(id_periode),
    FOREIGN KEY (id_apl) REFERENCES apl(nim),
    FOREIGN KEY (id_dpl) REFERENCES dpl(nik)
);

-- Peserta Table
CREATE TABLE peserta (
    id_peserta INT PRIMARY KEY AUTO_INCREMENT,
    nim VARCHAR(20),
    nama VARCHAR(255),
    prodi VARCHAR(255),
    id_kelompok INT,
    id_periode INT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (id_kelompok) REFERENCES kelompok(id_kelompok),
    FOREIGN KEY (id_periode) REFERENCES periode(id_periode)
);
```

---

## 🔄 Complete Request Lifecycle Example

### Skenario: Admin menambah Kelompok Baru

```
┌─ Step 1: User Interaction ─────────────────────┐
│ Admin klik "Tambah Kelompok"                  │
│ Browser: GET /kelompok/create                 │
└───────────────────────────────────────────────┘
                    ↓
┌─ Step 2: Route Matching ──────────────────────┐
│ routes/web.php match: Route::get(              │
│   '/kelompok/create',                         │
│   [KelompokController::class, 'create']       │
│ )                                             │
└───────────────────────────────────────────────┘
                    ↓
┌─ Step 3: Middleware ──────────────────────────┐
│ 1. ceklogin → Check user authenticated        │
│ 2. role:admin → Check user is admin           │
│ ✓ Proceed to controller                       │
└───────────────────────────────────────────────┘
                    ↓
┌─ Step 4: Controller Logic ────────────────────┐
│ public function create()                      │
│ {                                             │
│   $tuan_rumah = TuanRumah::all();             │
│   return view(                                │
│     'kelompok.create',                        │
│     ['tuan_rumah' => $tuan_rumah]             │
│   );                                          │
│ }                                             │
└───────────────────────────────────────────────┘
                    ↓
┌─ Step 5: Model Query ─────────────────────────┐
│ TuanRumah::all()                              │
│ ↓ (Eloquent translates to)                    │
│ SELECT * FROM tuan_rumah                      │
└───────────────────────────────────────────────┘
                    ↓
┌─ Step 6: Database ────────────────────────────┐
│ MySQL executes SELECT query                   │
│ Returns result set                            │
└───────────────────────────────────────────────┘
                    ↓
┌─ Step 7: Render View ─────────────────────────┐
│ resources/views/kelompok/create.blade.php     │
│ Receives: ['tuan_rumah' => Collection]        │
│ Renders HTML form with Bootstrap styling      │
└───────────────────────────────────────────────┘
                    ↓
┌─ Step 8: Browser Display ─────────────────────┐
│ Admin sees form with:                         │
│ - Input fields (nomor, desa, dusun, etc)      │
│ - Dropdown for tuan_rumah                     │
│ - Submit button                               │
└───────────────────────────────────────────────┘
                    ↓
┌─ Step 9: User Submits ────────────────────────┐
│ Admin fills form and click Submit              │
│ Browser: POST /kelompok/store                 │
│ Data: {nomor_kelompok: 1, desa: "Jaten", ...} │
└───────────────────────────────────────────────┘
                    ↓
┌─ Step 10: Validation ─────────────────────────┐
│ Controller@store():                           │
│ Validate input data                           │
│ - nomor_kelompok: required, unique             │
│ - desa: required                              │
│ - kapasitas: required, numeric                │
│ ✓ Validation passed                           │
└───────────────────────────────────────────────┘
                    ↓
┌─ Step 11: Save to Model ──────────────────────┐
│ Kelompok::create($validated)                  │
│ ↓ (Translates to)                             │
│ INSERT INTO kelompok (columns...) VALUES (...) │
└───────────────────────────────────────────────┘
                    ↓
┌─ Step 12: Database Insert ────────────────────┐
│ MySQL inserts new record                      │
│ Returns: id_kelompok = 5                      │
└───────────────────────────────────────────────┘
                    ↓
┌─ Step 13: Redirect ───────────────────────────┐
│ Controller returns:                           │
│ return redirect('/kelompok')                  │
│ with('success', 'Kelompok berhasil ditambah') │
└───────────────────────────────────────────────┘
                    ↓
┌─ Step 14: Browser Redirect ───────────────────┐
│ Browser: GET /kelompok                        │
│ (Repeat Step 2-8 for list view)               │
│ ↓                                             │
│ Display updated list with new kelompok        │
│ Show success notification                     │
└───────────────────────────────────────────────┘
```

---

## 📊 Key Principles Applied

### ✅ Separation of Concerns
- **View** (Blade): Only handles presentation
- **Controller**: Only handles business logic
- **Model**: Only handles data access
- **Route**: Only handles URL mapping

### ✅ DRY (Don't Repeat Yourself)
- Reusable components in `resources/views/components/`
- Base controller for common functionality
- Model relationships to avoid data duplication

### ✅ MVC Pattern Benefits
- **Maintainability**: Easy to locate and modify code
- **Testability**: Each layer can be tested independently
- **Scalability**: Add new features without affecting existing code
- **Reusability**: Components used across multiple pages

---

## 🎯 Summary Mapping

```
Real User Action → HTTP Request → Route → Middleware → Controller
                                                          ↓
                                                      Model ← Database
                                                          ↓
                                                        View
                                                          ↓
                                                    HTML Response
                                                          ↓
                                                      Browser
                                                          ↓
                                                    Display to User
```

**Setiap layer memiliki tanggung jawab spesifik dan tidak saling mencampur logika!**
