# Generic test folder 

### Usage:

* Copy the folder "tests" directly into your module directory, for example: **"eshop/modules/oxps/mymodule/"**
* Add new tests by adding files to **"tests/unit/module"** folder
* To run all the tests just run the script **"tests/runtests.sh"**
* To run a particular test class, just run the same script but add a path to the file: **EXAMPLE:** ./runtests.sh ./unit/module/somefileTest.php
* To run code coverage run the "runcoverage.sh" script. **IMPORTANT:** Activate the module in the ADMIN before running the coverage.
* If needs exclude some directorys or files, for that needs edit phpunit.xml file. 
For example if needs remove directory with content named testFiles needs in bracket add new line like: 
	
	< whitelist addUncoveredFilesFromWhitelist="true">	 
    < directory suffix=".php">../</directory>
	< exclude>
		....
		< directory suffix=".php">../testFiles/</directory>
		....
	< /exclude>
	
* To run metrics first needs install pDepend(PHP depend - tool for metrics generation, default which was in PHPUNIT 3.4.* was removed in newest versions). how to do it is wrote there http://pdepend.org/download/index.html.
* To run metrics is just enough execute file ./runmetrics.sh with root user right(required for writing)
* If You have OXMD (Mess Detector) installed in the shop root directory, automatic certification price script is available.
  Just execute the script located in "tests/runcertification.sh".
  Its result will be outputted on screen and also caved into "tests/certification/" folder.
  Additionally You might want to adjust a list of files and folders to ignore. So just open and edit the script section "--exclude".
