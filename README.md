Need to install Python3.9, currently testing with Python 3.9.13.

Instructions for Rasa Initialization are as follows:
	1. Install Python 3.9.13
 	2. 'py -3.9 -m venv .venv' is run to make a virtual environment using python 3.9
  	3. '.venv\Scripts\activate' is used to use the venv
   	4. 'pip install rasa' installs rasa
Since Rasa is already initialized that step can be skipped.    

Instructions for running the Rasa Server:
	1. 'rasa train' after pulling from origin
	2. initialize virtual environment as above in two instances of terminal
	3. 'rasa run actions' in one instance of terminal
	4. 'rasa run --enable-api --cors "*" --port 5005' in another instance of terminal
Server should start and connect with the website.
