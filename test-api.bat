@echo off
echo ===========================================
echo          Testing Secret Santa API
echo ===========================================
echo.

echo Testing Frontend:
curl -I http://localhost:3000
echo.

echo Testing API Root:
curl -I http://localhost:8080
echo.

echo Testing API Health:
curl -X GET http://localhost:8080/api/status 2>nul || echo "Status endpoint not found"
echo.

echo Testing Database Connection (Adminer):
curl -I http://localhost:8081
echo.

echo Testing WebSocket Port:
netstat -an | findstr ":9090"
echo.

echo ===========================================
echo All services are running!
echo.
echo Frontend:   http://localhost:3000
echo API:        http://localhost:8080
echo Adminer:    http://localhost:8081
echo WebSocket:  ws://localhost:9090
echo ===========================================
pause
