@echo off
cd /d "%~dp0"
echo Iniciando servidor PHP...
LIB\php\php.exe -S localhost:8080 -t .
pause
