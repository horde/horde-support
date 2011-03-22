#!/bin/bash
#
# Usage: horde-build-website-docs.sh [module [/path/to/modules /path/to/HEAD]]

. $(dirname $0)/horde-build-website-docs.conf || exit

SOURCEDIR=${2:-$(pwd)}
HEADDIR=${3:-$(pwd)}

# Check if everything is in place
test -x $GENERATOR || {
    echo html.py not found at $GENERATOR.
    exit
}
test -f $CONFIG || {
    echo docutils.conf not found at $CONFIG.
    exit
}
test -d $WEBROOT || {
    echo $WEBROOT not found.
    exit
}

echo > $WEBROOT/errors.txt

# Search for docs directories
echo "Searching applications..."
DOC_DIRS=$(find $SOURCEDIR/ -name docs -a -type d)
echo "Generating documentation..."
for DOC_DIR in $DOC_DIRS; do
    APP=$(basename $(dirname $DOC_DIR))
    if [ $APP != ".." ] &&
        [ -f $DOC_DIR/../README ] &&
        [ "$1" = "" -o "$1" = "$APP" ]; then
        echo -n "$APP "
        mkdir -p $WEBROOT/$APP/docs
        # Generate HTML docs
        DOCS=$WEBROOT/$APP/docs/docs.html
        FILES=$(find $DOC_DIR -maxdepth 1 -type f -regex .*/[A-Z_]+ | sort)
        FILES="$DOC_DIR/../README $FILES"
        if [ $APP = "horde" ]; then FILES="$FILES $DOC_DIR/../po/README"; fi
        cat > $DOCS <<EOF
<h3>Documentation</h3>

<p>These are the documentation files as distributed with the application's tarballs and stable CVS checkouts. Documentation for the current development version may differ</p>

<ul>
EOF

        for FILE in $FILES; do
            if [ $(basename $FILE) = "CHANGES" ]; then
                CHANGES=$WEBROOT/$APP/docs/CHANGES.html
                echo '<h1>Changes by Release</h1><pre>' > $CHANGES
                cat $FILE | sed 's/</\&lt;/g' | sed 's;pear\s*\(bug\|request\)\s*#\([[:digit:]]*\);<a href="http://pear.php.net/bugs/bug.php?id=\2">\0</a>;gi' | sed 's;\(,\s*\|(\)\(\(bug\|request\)\s*#\([[:digit:]]*\)\);\1<a href="http://bugs.horde.org/ticket/\4">\2</a>;gi' >> $CHANGES
                echo '</pre>' >> $CHANGES
                echo -n .
                echo "<li><a href=\"?f=CHANGES.html\">CHANGES</a></li>" >> $DOCS
                continue
            fi
            if [ $(basename $FILE) = "RELEASE_NOTES" ]; then
                NOTES=$WEBROOT/$APP/docs/RELEASE_NOTES.html
                echo '<h1>Release notes for the latest release</h1><pre>' > $NOTES
                php -r "class n { function n() { require_once 'Horde/Release.php'; include '$FILE'; echo \$this->notes['ml']['changes']; } } new n;" | sed 's/</\&lt;/g' >> $NOTES
                echo '</pre>' >> $NOTES
                echo -n .
                echo "<li><a href=\"?f=RELEASE_NOTES.html\">RELEASE_NOTES</a></li>" >> $DOCS
                continue
            fi
            echo -n .
            if [ $(basename $(dirname $FILE)) = "po" ]; then
                TO=po_README.html
                SRC=http://cvs.horde.org/co.php/horde/po/README?onb=FRAMEWORK_3
            else
                TO=$(basename $FILE .txt).html
                SRC=http://cvs.horde.org/co.php/$APP/docs/$(basename $FILE)?onb=FRAMEWORK_3
            fi
            OUTPUT=$($GENERATOR \
                --generator \
                --source-url=$SRC \
                --output-encoding=UTF-8 \
                --rfc-references \
                $FILE \
                2>>$WEBROOT/errors.txt)
            if [ $? ]; then
                OUTPUT=$(echo "$OUTPUT" | sed '1,/<body>/d' | sed '/<\/body>/,$d');
                echo "$OUTPUT" > $WEBROOT/$APP/docs/$TO;
                if [ "$(uname)" = "FreeBSD" ]; then
                    DISPLAY=$(echo $FILE | sed -r "s|$DOC_DIR/(\.\./)?||")
                else
                    DISPLAY=$(echo $FILE | sed "s|$DOC_DIR/\(../\)\?||")
                fi
                echo "<li><a href=\"?f=$TO\">$DISPLAY</a></li>" >> $DOCS
            fi
        done
        echo "</ul>" >> $DOCS
        echo " done"
    fi
done
