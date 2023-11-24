@echo off
cd\
cd C:\inetpub\wwwroot\printAdmin\rotinas
:while
(
	"C:\PHP\php.exe" -c "C:\PHP\php.ini" rotina_120s.php
	timeout 120
	cls
	goto :while
)