Hooks
^^^^^

Check and translate:
Hooks werden aus dem Ordner “Classes/Hooks” geladen. Es werden alle Klassen geprüft und nach einer “@hook” Annotation gesucht, welche entweder an einer Klasse oder an einer Methode stehen kann (je nachdem wie der Ziel-Hook integriert ist). Die Annotation “@hook TYPO3_CONF_VARS|SC_OPTIONS|recordlist/mod1/index.php|drawFooterHook” sorgt somit dafür, dass die Funktion an der die Annotation gefunden wird mit dem entsprechend verknüpft wird.