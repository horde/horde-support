#!/bin/bash
#
# Usage: horde-build-website-docs.sh [module [/path/to/modules]]

. $(dirname $0)/horde-build-website-docs.conf || exit

SOURCEDIR=${2:-$(pwd)}

# Check if everything is in place
test -x $GENERATOR || {
    echo html.py not found at $GENERATOR.
    exit
}
test -x $COMPONENTS || {
    echo horde-components not found at $COMPONENTS.
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
        [ -f "$DOC_DIR/../README" ] &&
        [ -d "$FILEROOT/app/views/App/apps/$APP" ] &&
        [ "$1" = "" -o "$1" = "$APP" ]; then
        echo -n "$APP "
        APPROOT=$FILEROOT/app/views/App/apps/$APP/docs
        mkdir -p $APPROOT
        # Generate HTML docs
        DOCS=$APPROOT/docs.html
        FILES=$(find "$DOC_DIR" -maxdepth 1 -type f -regex .*/[A-Z_]+ | sort)
        RELEASE_YML=""
        if [ -f "$DOC_DIR/release.yml" ]; then
            FILES="$DOC_DIR/release.yml"$'\n'"$FILES"
            RELEASE_YML=1
        fi
        FILES="$DOC_DIR/../README"$'\n'"$FILES"
        cat > $DOCS <<EOF
<h3>Documentation</h3>

<p>These are the documentation files as distributed with the application's stable Git checkouts. Documentation for the latest release version may differ</p>

<ul>
EOF

        for FILE in $FILES; do
            if [ $(basename "$FILE") = "CHANGES" ]; then
                CHANGES=$APPROOT/CHANGES.html
                echo '<h3>Changes by Release</h3><pre>' > $CHANGES
                cat "$FILE" | sed 's/</\&lt;/g' | sed 's;pear\s*\(bug\|request\)\s*#\([[:digit:]]*\);<a href="http://pear.php.net/bugs/bug.php?id=\2">\0</a>;gi' | sed ':a;N;$!ba;s;\(,\s*\|(\)\(\(bug\|request\)[[:space:]]*#\([[:digit:]]*\)\);\1<a href="http://bugs.horde.org/ticket/\4">\2</a>;gi' >> $CHANGES
                echo '</pre>' >> $CHANGES
                echo -n .
                echo "<li><a href=\"<?php echo \$GLOBALS['host_base'] ?>/apps/$APP/docs/CHANGES\">Changes</a></li>" >> $DOCS
                continue
            fi
            if [ $(basename "$FILE") = "RELEASE_NOTES" -a -n "$RELEASE_YML" ]; then
                continue
            fi
            if [ $(basename "$FILE") = "release.yml" -o \
                 $(basename "$FILE") = "RELEASE_NOTES" ]; then
                NOTES=$APPROOT/RELEASE_NOTES.html
                echo '<h3>Release notes for the latest release</h3><code>' > $NOTES
                if [ $(basename "$FILE") = "release.yml" ]; then
                    $COMPONENTS "$DOC_DIR/.." release --dump \
                        | php -R 'echo htmlspecialchars($argn) . "\n";'
                    $COMPONENTS "$DOC_DIR/.." release --dump \
                        | php -R 'echo htmlspecialchars($argn) . "\n";' \
                        | sed -r 's#(https?://[^ ]*)(\.| |$)#<a href="\1">\1</a>\2#' \
                        | php -R 'echo nl2br($argn) . "<br />\n";' \
                        >> $NOTES
                elif [ ! $RELEASE_YML ]; then
                    php -r "call_user_func(function() { \$notes = include '$FILE'; echo nl2br(htmlspecialchars(\$notes['changes'])); });" >> $NOTES
                fi
                echo '</code>' >> $NOTES
                echo -n .
                echo "<li><a href=\"<?php echo \$GLOBALS['host_base'] ?>/apps/$APP/docs/RELEASE_NOTES\">Release Notes</a></li>" >> $DOCS
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
                BARE=$(echo "$FILE" | sed -r "s|$DOC_DIR/(\.\./)?||")
                if [ $BARE = "README" ]; then
                    DISPLAY=$BARE
                else
                    DISPLAY=$(echo "$BARE" | sed -r "s/_/ /g; s/(^| )([A-Z])([A-Z]*)/\1\U\2\L\3/g")
                fi
                echo "<li><a href=\"<?php echo \$GLOBALS['host_base'] ?>/apps/$APP/docs/$BARE\">$DISPLAY</a></li>" >> $DOCS
            fi
        done
        echo "</ul>" >> $DOCS
        echo " done"
    fi
done
