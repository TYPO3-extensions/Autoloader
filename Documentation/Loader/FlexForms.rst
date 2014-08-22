:index:`FlexForms`
^^^^^^^^^

FlexForms are located in the "Configuration/FlexForms/" folder. All flex forms are XML files (please check TYPO3 core documentation) with the name of the plugin (upper camel case). The loader scans the folder and register the XML files to the TYPO3 core. So: Just use the same name for your Plugin and flex form file.

Notice: The loader change the subtypes_excludelist and subtypes_addlist of the given plugin signature. If you have any custom changes to this properties of tt_content you have to run your changes after the autoloader call.