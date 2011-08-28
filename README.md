Generating documentation for ONM
================================
There are 3 different systems to generate ONM documentation:

- Doxygen
- PHPDocumentor
- APIGen
- Docblox

Software requirements
---------------------
You must have installed in your system:

* APIGen:

        http://apigen.org/#installation

* doxygen:

        sudo apt-get install doxygen

* PhpDocumentor:

        sudo pear install PhpDocumentor

* Docblox:

        sudo pear  channel-discover pear.docblox-project.org
        sudo pear  channel-discover pear.michelf.com
        sudo pear install docblox/docblox-beta

Generating all the documentation
--------------------------------

Issue at your terminal:

        make doc

You can generate each one separately by issuing:

        make generate-apigen-doc
        make generate-phpdoc-doc
        make generate-doxygen-doc
        make generate-docblox-doc

Compiling PDF documentation file
--------------------------------
Doxygen generates LaTeX source files that could be compiled to a PDF.
For compiling the PDF file issue at your terminal:

        make generate-doxygen-doc

and go to the folder

        doc/doxygen/latex
and issue:

        make

This will create a refman.pdf file with all the documentation generated.
