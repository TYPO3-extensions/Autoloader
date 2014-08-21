:index:`Content Objects`
^^^^^^^^^^^^^^^

Check and translate:
ContentObjects sind SmartObjects (s.u.) und liegen im Ordner “Classes/Domain/Model/Content/”. Content Objekte erweitern tt_content können aber auch auf bestehende Felder zurückgreifen. Pro ContentObject wird ein normales Content Element an TYPO3 registriert. Dies wird von einem zentralen Controller gerendert und lädt das Template in “Resources/Private/Templates/Content/” welches genauso heißt wie das Model. Übergeben wird das Model und auch die komplette Row des Inhaltselements. Kurz zusammengefasst: Somit wird nur ein Model und ein Template benötigt um ein Inhaltselement auf Basis von tt_content zu bauen.