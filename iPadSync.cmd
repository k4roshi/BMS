@ECHO OFF
IF NOT EXIST "%ALLUSERSPROFILE%\Start Menu\Programs\Startup" goto bis
IF NOT EXIST "%ALLUSERSPROFILE%\Start Menu\Programs\Startup\iPadSync.vbs" copy "C:\Program Files\Apache Software Foundation\Apache2.2\htdocs\iPadSync.vbs" "%ALLUSERSPROFILE%\Start Menu\Programs\Startup"
ECHO Installazione completata, per favore riavvia il computer.
:bis
IF NOT EXIST "%ALLUSERSPROFILE%\Menu Avvio\Programmi\Esecuzione automatica" goto done
IF NOT EXIST "%ALLUSERSPROFILE%\Menu Avvio\Programmi\Esecuzione automatica\iPadSync.vbs" copy "C:\Program Files\Apache Software Foundation\Apache2.2\htdocs\iPadSync.vbs" "%ALLUSERSPROFILE%\Menu Avvio\Programmi\Esecuzione automatica"
ECHO Installazione completata, per favore riavvia il computer.
:done
cd "C:\Program Files\Apache Software Foundation\Apache2.2\htdocs"
php.exe Main.php
