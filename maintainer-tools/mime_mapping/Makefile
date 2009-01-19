# $Horde: framework/devtools/mime_mapping/Makefile,v 1.2 2008/11/06 04:31:35 chuck Exp $

OUT=mime.mapping.php
HOST=`hostname -f`

all:  ${OUT}
${OUT}:
	HOST=${HOST} php convert.php > ${OUT}
clean:
	rm -f ${OUT}
