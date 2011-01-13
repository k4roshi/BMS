Set Shell = CreateObject("WScript.Shell")

DesktopPath = Shell.SpecialFolders("AllUsersDesktop")
Set link = Shell.CreateShortcut(DesktopPath & "\Archivio iPadSync.lnk")
link.TargetPath = "C:\Program Files\iPadSync"
link.Save

ProgramsPath = Shell.SpecialFolders("AllUsersPrograms")
Set objFSO = CreateObject("Scripting.FileSystemObject")
If not objFSO.FolderExists(ProgramsPath & "\iPadSync") Then
	Set objFolder = objFSO.CreateFolder(ProgramsPath & "\iPadSync")
End If

Set link = Shell.CreateShortcut(ProgramsPath & "\iPadSync\Archivio iPadSync.lnk")
link.TargetPath = "C:\Program Files\iPadSync"
link.Save

Set link = Shell.CreateShortcut(ProgramsPath & "\iPadSync\File di configurazione.lnk")
link.TargetPath = "C:\Program Files\Apache Software Foundation\Apache2.2\htdocs\Config"
link.Save
