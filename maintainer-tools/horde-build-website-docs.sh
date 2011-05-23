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
test -d $FILEROOT || {
    echo $FILEROOT not found.
    exit
}

echo > $FILEROOT/errors.txt

# Search for docs directories
echo "Searching applications..."
IFS=$'\012'
DOC_DIRS=$(find "$SOURCEDIR/" -name docs -a -type d)
echo "Generating documentation..."
for DOC_DIR in $DOC_DIRS; do
    APP=$(basename $(dirname $DOC_DIR))
    if [ $APP != ".." ] &&
        [ -f "$DOC_DIR/README" -o -f "$DOC_DIR/../README" ] &&
        [ -d "$FILEROOT/app/views/App/apps/$APP" ] &&
        [ "$1" = "" -o "$1" = "$APP" ]; then
        echo -n "$APP "
        APPROOT=$FILEROOT/app/views/App/apps/$APP/docs
        mkdir -p $APPROOT
        # Generate HTML docs
        DOCS=$APPROOT/docs.html
        FILES=$(find "$DOC_DIR" -maxdepth 1 -type f -regex .*/[A-Z_]+ | sort)
        FILES="$DOC_DIR/../README"$'\n'"$FILES"
        cat > $DOCS <<EOF
<h3>Documentation</h3>

<p>These are the documentation files as distributed with the application's tarballs and stable CVS checkouts. Documentation for the current development version may differ</p>

<ul>
EOF

        for FILE in $FILES; do
            if [ $(basename "$FILE") = "CHANGES" ]; then
                CHANGES=$APPROOT/CHANGES.html
                echo '<h3>Changes by Release</h3><pre>' > $CHANGES
                cat "$FILE" | sed 's/</\&lt;/g' | sed 's;pear\s*\(bug\|request\)\s*#\([[:digit:]]*\);<a href="http://pear.php.net/bugs/bug.php?id=\2">\0</a>;gi' | sed 's;\(,\s*\|(\)\(\(bug\|request\)\s*#\([[:digit:]]*\)\);\1<a href="http://bugs.horde.org/ticket/\4">\2</a>;gi' >> $CHANGES
                echo '</pre>' >> $CHANGES
                echo -n .
                echo "<li><a href=\"<?php echo \$GLOBALS['host_base'] ?>/apps/$APP/docs/CHANGES\">CHANGES</a></li>" >> $DOCS
                continue
            fi
            if [ $(basename "$FILE") = "RELEASE_NOTES" ]; then
                NOTES=$APPROOT/RELEASE_NOTES.html
                echo '<h3>Release notes for the latest release</h3><pre>' > $NOTES
                php -r "class n { function n() { require_once 'Horde/Release.php'; include '$FILE'; echo \$this->notes['ml']['changes']; } } new n;" | sed 's/</\&lt;/g' >> $NOTES
                echo '</pre>' >> $NOTES
                echo -n .
                echo "<li><a href=\"<?php echo \$GLOBALS['host_base'] ?>/apps/$APP/docs/RELEASE_NOTES\">RELEASE_NOTES</a></li>" >> $DOCS
                continue
            fi
            echo -n .
            TO=$(basename "$FILE" .txt).html
            OUTPUT=$($GENERATOR \
                --output-encoding=UTF-8 \
                --rfc-references \
                "$FILE" \
                2>>$FILEROOT/errors.txt)
            if [ $? ]; then
                OUTPUT=$(echo "$OUTPUT" | sed '1,/<body>/d' | sed '/<\/body>/,$d');
                echo "$OUTPUT" > $APPROOT/$TO;
                if [ "$(uname)" = "FreeBSD" ]; then
                    DISPLAY=$(echo "$FILE" | sed -r "s|$DOC_DIR/(\.\./)?||")
                else
                    DISPLAY=$(echo "$FILE" | sed "s|$DOC_DIR/\(../\)\?||")
                fi
                echo "<li><a href=\"<?php echo \$GLOBALS['host_base'] ?>/apps/$APP/docs/$DISPLAY\">$DISPLAY</a></li>" >> $DOCS
            fi
        done
        echo "</ul>" >> $DOCS
        echo " done"
    fi
done
