Docutils Support for Horde
==========================

:Author:	Jon Parise
:Revision:	$Revision: 1.2 $
:Date:		$Date: 2004/05/04 14:42:49 $

Overview
--------
`Docutils`_ is a set of tools for processing plaintext documentation and
rendering it in more useful formats, such as HTML, XML, and HTML Help.  The
plaintext documentation is written using the `reStructuredText`_ (reST)
markup specification, an easy-to-read, what-you-see-is-what-you-get
plaintext markup standard.

Setup
-----
The `Docutils`_ tools are written using `Python`_, so you'll need to install
that first.  Then, simply follow the `quick start guide`_ to install and
test your setup.

Authoring Documentation
-----------------------
You'll need to familiarize yourself with the `reStructuredText`_ format
before you'll be able to author any new documentation, but, fortunately,
it's very easy to learn.  The `primer`_ and `quick reference`_ documents are
extremely useful.

Rendering Documentation
-----------------------
To render a reST-formatted plaintext file as HTML, simply use the supplied
``html.py`` script::

	python html.py [--config=docutils.conf] source.txt output.html

If you use the supplied ``docutils.conf`` file, as well, the Horde
stylesheet (``hordedoc.css``) will be applied to the HTML document.

.. _Docutils: http://docutils.sourceforge.net/
.. _reStructuredText: http://docutils.sourceforge.net/rst.html
.. _Python: http://www.python.org/
.. _quick start guide: http://docutils.sourceforge.net/README.html#quick-start
.. _primer: http://docutils.sourceforge.net/docs/rst/quickstart.html
.. _quick reference: http://docutils.sourceforge.net/docs/rst/quickref.html
