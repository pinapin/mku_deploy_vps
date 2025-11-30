# Quick Docker Test

## 🔧 Test Commands

### 1. Test Minimal Dockerfile
```bash
docker compose -f docker-compose.debug.yml build
docker compose -f docker-compose.debug.yml up --rm
```

### 2. If that works, test main services
```bash
# Clear cache completely
docker system prune -a -f
docker builder prune -a -f

# Build app service only (main Dockerfile)
docker compose build app --no-cache --pull

# Test if app container can start
docker compose up app --rm
```

### 3. Test Horizon service
```bash
docker compose build horizon --no-cache --pull
docker compose up horizon --rm
```

### 4. Test all services
```bash
docker compose down --remove-orphans
docker compose build --no-cache --pull
docker compose up -d
```

## 🐛 Troubleshooting

If you get "Dockerfile.minimal: no such file or directory":
1. The file is now at: `docker/Dockerfile.minimal`
2. Docker build context is working directory (`.`)
3. Subdirectory path is: `docker/Dockerfile.minimal`

## ✅ Expected Results

1. **Debug container**: Should show Laravel version
2. **App container**: Should start PHP-FPM on port 9000
3. **Horizon container**: Should run Laravel Horizon daemon
4. **Scheduler container**: Should run `php artisan schedule:run` every minute

## 📋 Check Services Status

```bash
# Check all containers
docker compose ps

# Check specific service logs
docker compose logs horizon
docker compose logs scheduler
```