@echo off
REM ============================================
REM Gestion Docker local WordPress (Windows .bat wrapper)
REM Usage : docker up | down | reset | seed
REM ============================================

REM Forcer UTF-8 pour afficher correctement les accents
chcp 65001 >nul

setlocal
set CMD=%1

if "%CMD%"=="" goto usage

REM Vérifier si bash est disponible
where bash >nul 2>nul
if errorlevel 1 (
    echo Erreur : Bash introuvable. Installez Git Bash ou WSL et ajoutez-le au PATH.
    exit /b 1
)

REM Aller dans le dossier bin
pushd "%~dp0" >nul

REM Lancer le script bash correspondant
bash -lc "./%CMD%"
set ERR=%ERRORLEVEL%

popd >nul

if %ERR% neq 0 goto fail
exit /b 0

:fail
echo.
echo Erreur : la commande "%CMD%" a échoué ("%ERR%").
goto usage

:usage
echo.
echo Usage : %~n0 up ^| down ^| reset ^| seed
exit /b 1
