# 🐾 Wildlife Monitoring System

Sistem pemantauan satwa liar berbasis web dengan **RBAC**, **stress test**, dan **optimasi query SQL** — dibangun menggunakan **Laravel 12**.

> **Tugas Besar Sistem Basis Data**  
> Telkom University — Program Studi Informatika  
> Nuevalen Refitra Alswando — 103072430008

---

## 🎯 Fitur Utama

- **Role-Based Access Control (RBAC)** dengan 3 peran:
  - `Administrator`: Akses penuh
  - `Konservasionis Lapangan`: Hanya bisa lihat peta, insiden, notifikasi
  - `Peneliti Ekologi`: Bisa lihat laporan & cuaca
- **Dashboard dinamis** dengan pagination & pencarian
- **Halaman error 403** kustom (hijau)
- **Stress test** menggunakan Apache JMeter (10–1000 user)
- **Optimasi query** dengan indeks & pengurangan `SELECT *`

---

## 🛠️ Tech Stack

- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Blade + Tailwind CSS (Plain HTML)
- **Database**: MySQL 8.0
- **Testing**: Apache JMeter 5.6
- **Deployment**: `php artisan serve` (localhost)

---

## 📦 Instalasi

### Prasyarat
- PHP 8.2+
- Composer
- MySQL
- Node.js (opsional, untuk Tailwind)

### Langkah-Langkah
1. Clone repository:
   ```bash
   git clone https://github.com/nuevalenrefitra/wildlife-monitoring-system.git
   cd wildlife-monitoring-system/laravel-app
