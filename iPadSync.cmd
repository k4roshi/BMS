@ECHO OFF
IF NOT EXIST "%ALLUSERSPROFILE%\Start Menu\Programs\Startup" goto bis
IF NOT EXIST "%ALLUSERSPROFILE%\Start Menu\Programs\Startup\iPadSync.vbs" copy "C:\Program Files\Apache Software Foundation\Apache2.2\htdocs\iPadSync.vbs" "%ALLUSERSPROFILE%\Start Menu\Programs\Startup"
:bis
IF NOT EXIST "%ALLUSERSPROFILE%\Menu Avvio\Programmi\Esecuzione automatica" goto done
IF NOT EXIST "%ALLUSERSPROFILE%\Menu Avvio\Programmi\Esecuzione automatica\iPadSync.vbs" copy "C:\Program Files\Apache Software Foundation\Apache2.2\htdocs\iPadSync.vbs" "%ALLUSERSPROFILE%\Menu Avvio\Programmi\Esecuzione automatica"
:done
cd "C:\Program Files\Apache Software Foundation\Apache2.2\htdocs"
php.exe Main.php
