@echo off
setlocal

:: Set your database credentials
set DB_USER=root
set DB_PASS=giocimenI2291928
set DB_NAME=videokeman
set BACKUP_DIR=C:\path\to\your\backup\directory
set TIMESTAMP=%DATE:~-4,4%-%DATE:~-10,2%-%DATE:~-7,2%_%TIME:~0,2%-%TIME:~3,2%-%TIME:~6,2%

:: Create backup directory if it doesn't exist
if not exist "%BACKUP_DIR%" (
    mkdir "%BACKUP_DIR%"
)

:: Backup the database
mysqldump -u %DB_USER% -p%DB_PASS% %DB_NAME% > "%BACKUP_DIR%\%DB_NAME%_%TIMESTAMP%.sql"

:: Check if the backup was successful
if %ERRORLEVEL% neq 0 (
    echo Backup failed!
) else (
    echo Backup successful! File saved as %BACKUP_DIR%\%DB_NAME%_%TIMESTAMP%.sql
)

:: Schedule the task to run daily at a specified time (e.g., 2:00 AM)
schtasks /create /tn "Daily Database Backup" /tr "%~f0" /sc daily /st 02:00 /f

endlocal