Spelling
========

Spell-Checker and Lemmatizer

--------
To run the files in the main python program, the user must download:

1. the nltk module http://nltk.org/
2. the pyenchant library http://pythonhosted.org/pyenchant/download.html
3. the wordnet corpus using nltk.download()

The WordNext Corpus
-------------------

On Ubuntu 12.04, here's what I did to get the WordNet corpus:

Open up a terminal and run python from the command-line (you'll want
to run w/root privs to make it easier to install):

  $ sudo python
  Python 2.7.3 (default, Apr 10 2013, 05:46:21) 
  [GCC 4.6.3] on linux2
  Type "help", "copyright", "credits" or "license" for more information.

The import nltk and run its downloader:

  >>> import nltk
  >>> nltk.download()
  showing info http://nltk.googlecode.com/svn/trunk/nltk_data/index.xml

This will open a handy-dandy GUI.

Click on the 'Corpora' tab at the top, and scroll down to 'W'.  

Choose 'WordNet' (not the 'WordNet-InfoContent). As of July 2013, this
corpus weighs-in at 10.3 MB.

