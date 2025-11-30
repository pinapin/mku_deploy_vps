# 🧪 Load Testing Mode Guide

## 📋 Overview

Sistem telah dimodifikasi untuk mendukung mode testing khusus untuk load testing. Mode ini memungkinkan testing dengan 1000+ concurrent users tanpa bergantung pada SSO eksternal.

## 🚀 Cara Mengaktifkan Mode Testing

### 1. Update Environment Variables

Edit file `.env`:

```bash
# Aktifkan mode testing
LOAD_TESTING_ENABLED=true
LOAD_TESTING_AUTO_LOGIN=false
```

### 2. Clear Cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### 3. Akses Form Login Testing

Buka browser: `http://localhost:8000/login`

 akan muncul form login testing dengan badge merah "MODE TESTING".

## 👥 User Testing Credentials

### Default Users (10 users):
```
NIM: 2021001  Password: test123
NIM: 2021002  Password: test123
NIM: 2021003  Password: test123
...
NIM: 2021010  Password: test123
```

### Additional Users (jika perlu):
```
NIM: 2021011-2021020  Password: test123
```

## 🔧 Konfigurasi tambahan

### Menambah User Testing Baru

Edit file `config/testing.php`:

```php
'users' => [
    '2021021' => [
        'password' => 'test123',
        'nama' => 'Test User 21',
        'prodi' => 'S1 Teknik Informatika',
        'email' => 'test21@student.umk.ac.id',
    ],
    // Tambahkan user lain...
],
```

### Auto-Login Mode

Untuk load testing yang lebih agresif:

```bash
LOAD_TESTING_AUTO_LOGIN=true
```

Ini akan mengaktifkan auto-login dengan parameter `?test_user=2021001`

## 🎯 Menjalankan Load Test

### 1. Install k6
```bash
# Windows
choco install k6

# macOS
brew install k6

# Linux
sudo apt-get install k6
```

### 2. Jalankan Load Test
```bash
k6 run load-test-ujian.js
```

### 3. Generate Report
```bash
k6 run --out html=report.html load-test-ujian.js
```

## 🔄 Cara Kembali ke SSO Normal (Rollback)

### Metode 1: Environment Variable (Recommended)

Edit file `.env`:
```bash
# Nonaktifkan mode testing
LOAD_TESTING_ENABLED=false
LOAD_TESTING_AUTO_LOGIN=false
```

Clear cache:
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### Metode 2: Hapus File Testing (Optional)

Jika ingin menghapus semua file testing:

```bash
# Backup terlebih dahulu
cp app/Http/Controllers/AuthController.php app/Http/Controllers/AuthController.php.backup
cp app/Http/Middleware/CheckSession.php app/Http/Middleware/CheckSession.php.backup

# Hapus file-file testing (jika yakin)
rm config/testing.php
rm app/Http/Middleware/TestingMode.php
rm resources/views/auth/testing-login.blade.php
```

Restore original files:
```bash
cp app/Http/Controllers/AuthController.php.backup app/Http/Controllers/AuthController.php
cp app/Http/Middleware/CheckSession.php.backup app/Http/Middleware/CheckSession.php
```

## 📊 Monitoring Saat Load Test

### Database Monitoring
```sql
-- Monitor active sessions
SELECT COUNT(*) as active_sessions
FROM sesi_ujian
WHERE status = 'berlangsung';

-- Monitor answer submission rate
SELECT COUNT(*) as jawaban_per_menit
FROM jawaban_mahasiswa
WHERE created_at >= NOW() - INTERVAL 1 MINUTE;

-- Monitor login attempts
SELECT COUNT(*) as login_attempts_per_menit
FROM login_logs
WHERE created_at >= NOW() - INTERVAL 1 MINUTE;
```

### Application Logs
```bash
# Monitor Laravel logs
tail -f storage/logs/laravel.log | grep "Testing login"

# Monitor load test specific logs
tail -f storage/logs/laravel.log | grep -E "(Testing|Load test)"
```

## ⚠️ Security Notes

### ⚠️ PENTING: Mode Testing HARUS dinonaktifkan di Production!

1. **JANGAN PERNAH** mengaktifkan `LOAD_TESTING_ENABLED=true` di production
2. **JANGAN PERNAH** commit file `.env` dengan testing mode enabled
3. **SELALU** rollback ke SSO normal setelah testing selesai
4. **MONITOR** log untuk suspicious activity saat testing

### Security Features Built-in:
- Testing users hanya tersedia saat `LOAD_TESTING_ENABLED=true`
- Session testing diberi flag `testing_mode: true`
- Auto login disable secara default
- Login attempts di-log untuk monitoring

## 🐛 Troubleshooting

### Login Gagal
```bash
# Check if testing mode enabled
php artisan tinker
>>> config('testing.enabled')
```

### Form Login Tidak Muncul
```bash
# Clear cache
php artisan config:clear
php artisan view:clear

# Check route
php artisan route:list | grep login
```

### Session Issues
```bash
# Clear sessions
php artisan session:table
php artisan migrate:fresh --seed
```

### Database Performance
```sql
-- Check slow queries
SHOW PROCESSLIST;
SHOW FULL PROCESSLIST;

-- Optimize tables
OPTIMIZE TABLE jawaban_mahasiswa;
OPTIMIZE TABLE sesi_ujian;
```

## 📈 Expected Results

### Load Test Targets:
- **Concurrent Users:** 1000
- **Duration:** ~32 minutes
- **Response Time:** <5 seconds (95th percentile)
- **Success Rate:** >95%
- **Database Throughput:** Monitor jawaban insertion rate

### Performance Metrics:
- Login success rate
- Answer submission rate
- Exam completion rate
- Database query performance
- Memory usage
- CPU usage

## 📞 Support

Jika mengalami masalah:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Check database connections
3. Verify environment configuration
4. Monitor system resources

---

**INGAT:** Mode testing hanya untuk development/testing环境. SELALU rollback ke SSO normal setelah testing selesai!