@echo off
cd\
cd C:\inetpub\wwwroot\printAdmin\rotinas
:while
(
	"C:\PHP\php.exe" -c "C:\PHP\php.ini" rotina_60s.php
	timeout 30
	cls
	goto :while
)