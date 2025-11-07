#!/usr/bin/env bash
# Test script to simulate Render deployment locally using Docker

set -e

echo "🔧 Testing Render deployment locally..."
echo ""

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    echo "❌ Docker is not installed. Please install Docker to test the deployment."
    exit 1
fi

echo "📦 Building Docker image..."
docker build -t taskware-test .

echo ""
echo "🚀 Running container..."
docker run -d \
    --name taskware-test \
    -p 8080:8080 \
    -e APP_NAME=Taskware \
    -e APP_ENV=production \
    -e APP_DEBUG=false \
    -e APP_KEY=base64:$(openssl rand -base64 32) \
    -e DB_CONNECTION=sqlite \
    -e GUEST_DB_CONNECTION=guest_sqlite \
    -e LOG_CHANNEL=stderr \
    taskware-test

echo ""
echo "⏳ Waiting for application to start..."
sleep 5

echo ""
echo "🔍 Checking application status..."
if curl -s -o /dev/null -w "%{http_code}" http://localhost:8080 | grep -q "200\|302"; then
    echo "✅ Application is running successfully!"
    echo ""
    echo "🌐 Access the application at: http://localhost:8080"
    echo ""
    echo "📋 Container logs:"
    docker logs taskware-test | tail -n 20
    echo ""
    echo "To view live logs: docker logs -f taskware-test"
    echo "To stop the container: docker stop taskware-test"
    echo "To remove the container: docker rm taskware-test"
else
    echo "❌ Application failed to start properly."
    echo ""
    echo "📋 Container logs:"
    docker logs taskware-test
    echo ""
    echo "Cleaning up..."
    docker stop taskware-test
    docker rm taskware-test
    exit 1
fi

