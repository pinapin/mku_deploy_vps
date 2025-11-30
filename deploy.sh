#!/bin/bash

# Laravel Docker Deployment Script
# Usage: ./deploy.sh

echo "🚀 Starting Laravel deployment..."

# Pull latest changes (if using git)
echo "📥 Pulling latest changes..."
git pull origin main

# Stop existing containers
echo "🛑 Stopping existing containers..."
docker-compose down

# Build images
echo "🏗️  Building Docker images..."
docker-compose build --no-cache

# Start containers
echo "▶️  Starting containers..."
docker-compose up -d

# Wait for database to be ready
echo "⏳ Waiting for database..."
sleep 10

# Run migrations
echo "🗄️  Running database migrations..."
docker-compose exec -T app php artisan migrate --force

# Clear and cache config
echo "🔧 Optimizing application..."
docker-compose exec -T app php artisan config:cache
docker-compose exec -T app php artisan route:cache
docker-compose exec -T app php artisan view:cache

# Set permissions
echo "🔐 Setting permissions..."
docker-compose exec -T app chmod -R 755 storage bootstrap/cache

# Show running containers
echo "✅ Deployment completed!"
echo ""
echo "📊 Running containers:"
docker-compose ps

echo ""
echo "📝 Application logs:"
docker-compose logs --tail=20 app