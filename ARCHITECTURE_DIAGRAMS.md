# 🎨 Visualisasi Arsitektur KKN-APP

## Diagram 1: Alur Interaksi Pengguna ke Database

```mermaid
flowchart TD
    A["👤 User/Admin"] -->|Browse| B["🌐 Browser"]
    B -->|HTTP Request| C["🔀 Route Handler<br/>(web.php)"]
    C -->|Forward| D["🎮 Controller<br/>(KelompokController,<br/>PesertaController, dll)"]
    D -->|Query & Fetch| E["📦 Model Layer<br/>(Kelompok, Peserta,<br/>APL, DPL, etc)"]
    E -->|SQL Query| F["💾 MySQL Database"]
    F -->|Result Set| E
    E -->|Data Object| D
    D -->|Pass Data| G["🎨 View/Template<br/>(Blade Views)"]
    G -->|Render HTML| B
    B -->|Display| A
```

## Diagram 2: MVC Architecture KKN-APP

```mermaid
flowchart LR
    subgraph VIEW["🎨 VIEW LAYER (Presentation)"]
        V1["Dashboard"]
        V2["Kelompok Management"]
        V3["Peserta Management"]
        V4["APL/DPL Management"]
        V5["Reports & Export"]
    end
    
    subgraph CONTROLLER["🎮 CONTROLLER LAYER (Business Logic)"]
        C1["KelompokController"]
        C2["PesertaController"]
        C3["AplController"]
        C4["DplController"]
        C5["DashboardController"]
    end
    
    subgraph MODEL["📦 MODEL LAYER (Data Access)"]
        M1["Kelompok"]
        M2["Peserta"]
        M3["Apl"]
        M4["Dpl"]
        M5["Periode"]
        M6["User"]
    end
    
    subgraph DATABASE["💾 DATABASE LAYER"]
        DB1["kelompok table"]
        DB2["peserta table"]
        DB3["apl table"]
        DB4["dpl table"]
        DB5["periode table"]
        DB6["users table"]
    end
    
    V1 --> C5
    V2 --> C1
    V3 --> C2
    V4 --> C3 & C4
    V5 --> C1
    
    C1 --> M1 & M2 & M5
    C2 --> M2
    C3 --> M3
    C4 --> M4
    C5 --> M1 & M2 & M3 & M4 & M5 & M6
    
    M1 --> DB1
    M2 --> DB2
    M3 --> DB3
    M4 --> DB4
    M5 --> DB5
    M6 --> DB6
```

## Diagram 3: User Role-Based Access

```mermaid
flowchart TD
    A["Login<br/>(AuthController)"] --> B{Role Check}
    
    B -->|Admin| C["Admin Dashboard"]
    C --> C1["Periode Management"]
    C --> C2["Kelompok Management"]
    C --> C3["Peserta Management"]
    C --> C4["APL/DPL Management"]
    C --> C5["Randomisasi & Publish"]
    C --> C6["Import/Export Data"]
    
    B -->|Peserta| D["Peserta Portal"]
    D --> D1["View Kelompok Assignment"]
    D --> D2["View APL/DPL Info"]
    
    B -->|APL| E["APL Portal"]
    E --> E1["View Kelompok Assigned"]
    E --> E2["View Peserta List"]
    
    B -->|DPL| F["DPL Portal"]
    F --> F1["View Kelompok Assigned"]
    F --> F2["View Peserta List"]
```

## Diagram 4: Data Flow - Randomisasi & Pembagian Kelompok

```mermaid
flowchart TD
    A["Admin Select Periode"] -->|GET /randomisasi| B["KelompokController<br/>@randomisasi"]
    B -->|Load Data| C["Fetch Peserta by Periode"]
    C -->|From Model| D["MySQL: peserta table"]
    D -->|Result| C
    C -->|Load Data| E["Fetch Kelompok by Periode"]
    E -->|From Model| F["MySQL: kelompok table"]
    F -->|Result| E
    E --> G["Algorithm:<br/>Random Assign"]
    C --> G
    G -->|Generate Result| H["Display Preview<br/>in View"]
    H -->|User Review| I{"Confirm?"}
    I -->|Cancel| A
    I -->|Confirm| J["POST /simpan-hasil"]
    J -->|Update| K["KelompokController<br/>@simpanHasil"]
    K -->|Update Records| L["Peserta::update()"]
    L -->|SQL UPDATE| M["MySQL: peserta table"]
    M -->|Success| N["Assign APL/DPL"]
    N -->|POST /assign-dpl| O["Update Kelompok.id_dpl"]
    N -->|POST /assign-apl| P["Update Kelompok.id_apl"]
    O --> Q["MySQL: kelompok table"]
    P --> Q
    Q --> R["Publish Result<br/>POST /publish"]
    R -->|Set status_publish=1| S["Update Periode"]
    S -->|Lock Changes| T["Success Notification"]
```

## Diagram 5: Database Relationships

```mermaid
erDiagram
    USERS ||--o{ PESERTA : user_id
    USERS ||--o{ APL : user_id
    USERS ||--o{ DPL : user_id
    
    PERIODE ||--o{ KELOMPOK : id_periode
    PERIODE ||--o{ PESERTA : id_periode
    PERIODE ||--o{ APL : id_periode
    PERIODE ||--o{ DPL : id_periode
    
    KELOMPOK ||--o{ PESERTA : id_kelompok
    KELOMPOK }o--|| APL : id_apl
    KELOMPOK }o--|| DPL : id_dpl
    KELOMPOK }o--|| TUAN_RUMAH : id_tuan_rumah
    
    USERS ||--o{ LOG_ACTIVITIES : user_id
```

## Diagram 6: Request-Response Cycle

```mermaid
sequenceDiagram
    participant User
    participant Browser
    participant Route
    participant Controller
    participant Model
    participant Database
    participant View
    
    User->>Browser: Click "Lihat Kelompok"
    Browser->>Route: GET /kelompok
    Route->>Controller: KelompokController@index
    Controller->>Model: Kelompok::where('periode_id', $id)->get()
    Model->>Database: SELECT * FROM kelompok WHERE periode_id = ?
    Database-->>Model: Return ResultSet
    Model-->>Controller: Return Collection
    Controller->>View: return view('kelompok.hasil', $data)
    View->>Browser: Render HTML (Bootstrap styling)
    Browser-->>User: Display Kelompok Table
```

## Diagram 7: Middleware & Authentication Flow

```mermaid
flowchart TD
    A["Browser Request"] --> B["Global Middleware"]
    B --> C{Route Check}
    
    C -->|Public Routes| D["Auth Check"]
    C -->|Protected Routes| E["Middleware Stack:<br/>ceklogin + role"]
    
    E --> F{User Authenticated?}
    F -->|No| G["Redirect to Login"]
    G --> H["AuthController<br/>@showLogin"]
    
    F -->|Yes| I{Role Match?}
    I -->|No| J["403 Forbidden"]
    I -->|Yes| K["Load Controller"]
    
    K --> L["Execute Action"]
    L --> M["Return Response"]
    M --> N["Send to Browser"]
```

## Diagram 8: Data Import/Export Pipeline

```mermaid
flowchart LR
    subgraph INPUT["📥 INPUT"]
        I1["Excel File<br/>(Peserta/APL/DPL)"]
    end
    
    subgraph PROCESS["⚙️ PROCESSING"]
        P1["ImportController<br/>@preview"]
        P2["Validate Data"]
        P3["Map to Model"]
        P4["Batch Insert"]
    end
    
    subgraph OUTPUT["📤 OUTPUT"]
        O1["PDF Report"]
        O2["Excel Report"]
        O3["CSV Export"]
    end
    
    subgraph STORAGE["💾 STORAGE"]
        S["MySQL Database"]
    end
    
    I1 --> P1
    P1 --> P2
    P2 --> P3
    P3 --> P4
    P4 --> S
    
    S -->|Query Data| O1
    S -->|Query Data| O2
    S -->|Query Data| O3
```

## Diagram 9: State Transitions - Periode Status

```mermaid
stateDiagram-v2
    [*] --> Draft: Create Periode
    Draft --> Active: Activate
    Active --> RandomReady: Data Complete
    RandomReady --> Randomized: Run Randomisasi
    Randomized --> AssignmentReady: Assign APL/DPL
    AssignmentReady --> Published: Publish
    Published --> Archived: Archive
    Archived --> [*]
    
    Published -.->|Unpublish| AssignmentReady
    Active -.->|Edit| Draft
```

## Diagram 10: System Components Overview

```mermaid
graph TB
    subgraph CLIENT["CLIENT SIDE"]
        CA["🖥️ Admin Interface"]
        CP["📱 Peserta Interface"]
        CA2["👨‍🎓 APL Interface"]
        CB["👨‍🏫 DPL Interface"]
    end
    
    subgraph WEB["WEB FRAMEWORK"]
        WA["Laravel 11"]
        WB["Bootstrap 5"]
        WC["Blade Template"]
    end
    
    subgraph BACKEND["BACKEND SERVICES"]
        BA["Controllers"]
        BB["Models"]
        BC["Middleware"]
        BD["Routes"]
    end
    
    subgraph BUSINESS["BUSINESS LOGIC"]
        BLA["Randomisasi Algorithm"]
        BLB["Assignment Logic"]
        BLC["Validation Rules"]
        BLD["Export/Import Logic"]
    end
    
    subgraph DATA["DATA LAYER"]
        DA["Eloquent ORM"]
        DB["Query Builder"]
        DC["Database Connection"]
    end
    
    subgraph STORAGE["STORAGE"]
        SA["MySQL Database"]
        SB["File Storage"]
    end
    
    CLIENT --> WEB
    WEB --> BACKEND
    BACKEND --> BUSINESS
    BUSINESS --> DATA
    DATA --> STORAGE
```
