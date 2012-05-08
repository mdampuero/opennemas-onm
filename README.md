ONM Framework
=============
Framework focused in journalism workflow.

Generating documentation for ONM
================================

You must have installed in your system:

        sudo pear  channel-discover pear.docblox-project.org
        sudo pear  channel-discover pear.michelf.com
        sudo pear install docblox/docblox-beta

Issue at your terminal:

        make doc

You can generate each one separately by issuing:

        make generate-docblox-doc
