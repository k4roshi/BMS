@ECHO OFF
cd "C:\Program Files\Apache Software Foundation\Apache2.2\htdocs"
installer_shortcuts.vbs

cacls "C:\Program Files\Apache Software Foundation\Apache2.2\htdocs\Tmp" /e /g everyone:F
cacls "C:\Program Files\iPadSync" /e /g everyone:F

