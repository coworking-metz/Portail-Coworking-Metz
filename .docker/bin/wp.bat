@echo off
REM ============================================
REM Run WP-CLI inside the WordPress container
REM Usage: wp <command> [arguments]
REM ============================================

setlocal

REM Read PROJECT_NAME from .docker\.env
for /f "tokens=2 delims==" %%a in ('findstr /b "PROJECT_NAME=" ".docker\.env"') do set PROJECT_NAME=%%a

REM Run the command inside the container as www-data
docker exec -it --user www-data %PROJECT_NAME%-wp wp %*

endlocal
