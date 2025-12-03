# 🚀 Load Balancer Implementation Guide

## 📋 Overview

Implementasi Load Balancer untuk VPS 8vCPU, 16GB RAM dengan:
- **1 Nginx Load Balancer** (1-2 vCPU, 2GB RAM)
- **5 Laravel App Instances** (1 vCPU, 3GB RAM each)
- **Shared Database & Cache** (MySQL + Redis)
- **Health Monitoring** (Real-time monitoring)

## 🏗️ Arsitektur

```
┌─────────────────────────────────────────────────────────────────┐
│                    Internet Traffic                         │
│                         │                               │
│                         ▼                               │
│                ┌─────────────┐                         │
│                │   Nginx    │ ← Load Balancer         │
│                │  (Port 80)  │                         │
│                └─────┬─────┘                         │
│                      │                               │
│        ┌─────────────┼─────────────┬─────────────┐   │
│        │             │             │             │   │
│  ┌─────▼─────┐ ┌─────▼─────┐ ┌─────▼─────┐ │
│  │ Laravel #1 │ │ Laravel #2 │ │ Laravel #3 │ │
│  │  PHP-FPM   │ │  PHP-FPM   │ │  PHP-FPM   │ │
│  │   Port 9000│ │   Port 9000│ │   Port 9000│ │
│  └─────────────┘ └─────────────┘ └─────────────┘ │
│                                                   │
│  ┌─────▼─────┐ ┌─────▼─────┐                      │
│  │ Laravel #4 │ │ Laravel #5 │                      │
│  │  PHP-FPM   │ │  PHP-FPM   │                      │
│  │   Port 9000│ │   Port 9000│                      │
│  └─────────────┘ └─────────────┘                      │
│                      │                               │
│        ┌─────────────┼─────────────┐                  │
│        │   MySQL     │   Redis     │                  │
│        │  Database   │   Cache    │                  │
│        └─────────────┴─────────────┘                  │
└─────────────────────────────────────────────────────────────────┘
```

## 🛠️ Langkah Implementasi

### 1. Update Konfigurasi

```bash
# Backup konfigurasi lama
cp docker-compose.yml docker-compose-single.yml

# Gunakan konfigurasi load balancer
cp docker-compose-lb.yml docker-compose.yml
```

### 2. Update Domain & SSL

Edit file: `docker/nginx/conf.d/lb-default.conf`
```nginx
server_name your-domain.com www.your-domain.com;
# Uncomment SSL section setelah punya certificate
```

### 3. Start Load Balancer

```bash
# Stop containers lama
docker-compose down

# Start dengan load balancer
docker-compose up -d --build

# Monitor startup
docker-compose logs -f nginx-lb
```

### 4. Verify Health Status

```bash
# Cek status semua server
php artisan lb:manage status

# Test distribusi load balancer
php artisan lb:manage test

# Monitoring real-time
php artisan monitor:health-check --log-output
```

### 5. Test dengan k6

```bash
# Test basic load balancer
k6 run k6-simple-api-test.js

# Test high-concurrency
k6 run k6-random-question-test.js

# Custom load balancer test
k6 run --env API_BASE_URL=http://your-domain.com/api k6-random-question-test.js
```

## 📊 Resource Allocation

| Component | vCPU | RAM | Purpose |
|-----------|-------|-----|---------|
| Nginx LB | 1-2 | 2GB | Load balancing & static assets |
| Laravel #1 | 1 | 3GB | Application processing |
| Laravel #2 | 1 | 3GB | Application processing |
| Laravel #3 | 1 | 3GB | Application processing |
| Laravel #4 | 1 | 3GB | Application processing |
| Laravel #5 | 1 | 3GB | Application processing |
| MySQL | 3 | 6GB | Shared database |
| Redis | 0.5 | 3GB | Shared cache & session |
| **Total** | **8.5** | **22GB** | *(dengan VPS 8vCPU, 16GB dapat scaling lebih optimal)* |

## 🎯 Performance Expectations

### Sebelum Load Balancer:
- **Single point of failure**
- **Max requests/s**: ~400-500
- **Error rate**: 30-40%
- **Response time**: P95 4-5s

### Setelah Load Balancer:
- **High availability** (4/5 servers can fail)
- **Max requests/s**: **1500-2000**
- **Error rate**: **< 2%**
- **Response time**: **P95 < 1.5s**
- **Zero downtime** during rolling updates

## 🔧 Konfigurasi Advanced

### Nginx Load Balancing Methods

```nginx
# 1. Least Connections (default)
upstream laravel_backend {
    least_conn;
    server laravel_app_1:9000;
    server laravel_app_2:9000;
    # ...
}

# 2. IP Hash (untuk session sticky)
upstream laravel_backend {
    ip_hash;
    server laravel_app_1:9000;
    server laravel_app_2:9000;
    # ...
}

# 3. Round Robin (default fallback)
upstream laravel_backend {
    server laravel_app_1:9000;
    server laravel_app_2:9000;
    # ...
}
```

### Session Management untuk Load Balancer

**Option 1: Redis Session (Recommended)**
```php
// .env
SESSION_DRIVER=redis
SESSION_LIFETIME=120
REDIS_HOST=redis
REDIS_PASSWORD=null
```

**Option 2: Sticky Sessions dengan Nginx**
```nginx
upstream laravel_backend {
    ip_hash; # atau least_conn + ip_hash
    server laravel_app_1:9000;
    server laravel_app_2:9000;
}
```

### Auto-Scaling Configuration

```yaml
# docker-compose-scaling.yml (opsional)
services:
  nginx-lb:
    deploy:
      replicas: 2  # Dual load balancers for high availability

  laravel_app:
    deploy:
      replicas: 8  # Auto-scale sampai 8 instances
```

## 🚨 Monitoring & Alerting

### Health Check Endpoints

```bash
# Load balancer health
curl http://localhost/health

# Application health (behind LB)
curl http://localhost/app-health

# API health (untuk testing)
curl http://localhost/api/ujian/health
```

### Real-time Monitoring

```bash
# Start continuous monitoring
php artisan monitor:health-check --log-output

# Check load balancer status
php artisan lb:manage status

# View metrics
php artisan lb:manage metrics
```

### Docker Health Checks

```bash
# Container health status
docker ps --format "table {{.Names}}\t{{.Status}}"

# Individual container health
docker inspect laravel_app_1 | jq '.[0].State.Health.Status'
docker inspect laravel_nginx_lb | jq '.[0].State.Health.Status'
```

## 🔄 Rolling Updates & Zero Downtime

### Update Laravel Code

```bash
# 1. Update setengah dari laravel_app_5
php artisan lb:manage status  # Pastikan semua sehat

# 2. Scale down untuk update
docker-compose stop laravel_app_1 laravel_app_2

# 3. Update remaining servers
docker-compose up -d --build laravel_app_3 laravel_app_4 laravel_app_5

# 4. Test dengan beberapa request
php artisan lb:manage test

# 5. Scale up lagi
docker-compose start laravel_app_1 laravel_app_2

# 6. Verifikasi semua sehat
php artisan lb:manage status
```

### Database Updates

```bash
# 1. Backup database
docker exec laravel_db_lb mysqldump -u root -p mku_app > backup.sql

# 2. Update database (dengan minimal downtime)
docker-compose stop laravel_app_1 laravel_app_2 laravel_app_3 laravel_app_4 laravel_app_5
docker-compose up -d --build db

# 3. Wait database ready
docker-compose logs -f db

# 4. Start aplikasi
docker-compose start laravel_app_1 laravel_app_2 laravel_app_3 laravel_app_4 laravel_app_5
```

## 🔍 Troubleshooting

### Masalah Umum

**1. Load Balancer Tidak Merespons**
```bash
# Cek konfigurasi nginx
docker exec laravel_nginx_lb nginx -t

# Reload nginx
docker exec laravel_nginx_lb nginx -s reload

# Lihat logs
docker logs laravel_nginx_lb
```

**2. Server Tidak Ditemukan**
```bash
# Verifikasi network
docker network ls
docker network inspect laravel_laravel

# Test koneksi antar container
docker exec laravel_nginx_lb ping laravel_app_1
```

**3. Session Hilang**
```bash
# Cek Redis connection
docker exec laravel_redis_lb redis-cli ping

# Verifikasi session config
docker exec laravel_app_1 php artisan tinker
>>> config('session.driver')
```

**4. Database Connection Error**
```bash
# Cek MySQL connection
docker exec laravel_db_lb mysql -u root -p -e "SHOW PROCESSLIST;"

# Monitoring connection pool
docker exec laravel_db_lb mysql -u root -p -e "SHOW STATUS LIKE 'Threads_connected';"
```

## 📈 K6 Load Testing Commands

### Basic Load Balancer Test
```bash
# Test dengan 1000 concurrent users
k6 run --vus 1000 --duration 5m k6-simple-api-test.js

# Test API endpoint spesifik
k6 run --env API_BASE_URL=http://your-domain.com/api k6-random-question-test.js

# Full load balancer test
k6 run k6-ujian-api.js
```

### Performance Thresholds

```javascript
// Target performance dengan load balancer
export let options = {
    thresholds: {
        http_req_duration: ['p(95)<1500'],  // P95 < 1.5s
        http_req_failed: ['rate<0.02'],    // < 2% error rate
        errors: ['rate<0.02'],           // < 2% custom errors
    },
    stages: [
        { duration: '2m', target: 200 },
        { duration: '2m', target: 500 },
        { duration: '2m', target: 1000 },
        { duration: '2m', target: 1500 },
        { duration: '2m', target: 2000 },
        { duration: '2m', target: 1000 },
        { duration: '2m', target: 500 },
        { duration: '2m', target: 100 },
    ],
};
```

## ✅ Success Criteria

### Load Balancer Berhasil Jika:
- [ ] **Uptime 99.9%+** untuk 24 jam
- [ ] **Response time P95 < 1.5s**
- [ ] **Error rate < 2%**
- [ ] **Handle 2000+ concurrent users**
- [ ] **Zero downtime** saat rolling update
- [ ] **Auto-recovery** dari failed server
- [ ] **Health monitoring** working

### Performance Improvements:
- **Response time**: 4.33s → **< 1.5s** (-65%)
- **Throughput**: 455 req/s → **2000+ req/s** (+340%)
- **Error rate**: 39% → **< 2%** (-95%)
- **Uptime**: Single point → **99.9%+** HA

## 🎯 Next Steps

1. **Deploy load balancer** dengan `docker-compose up -d --build`
2. **Test functionality** dengan `php artisan lb:manage test`
3. **Monitor performance** dengan `php artisan monitor:health-check`
4. **Load testing** dengan k6 scripts
5. **Fine-tune configuration** berdasarkan hasil test
6. **Setup SSL/TLS** untuk production
7. **Configure monitoring** dengan prometheus + grafana (optional)

**Selamat!** 🎉 Load balancer siap meningkatkan performa Laravel Anda secara signifikan!