# Makefile for DIMP utilities.

SHELL	= /bin/sh
CC	= gcc
CFLAGS	= -O2
OBJECTS	= jsmin.o
INSPATH	= /usr/local/bin

all: jsmin

jsmin:  ${OBJECTS}
	${CC} ${CFLAGS} -o $@ ${OBJECTS}

warnings: ${OBJECTS}
	  ${CC} ${CFLAGS} -Wall -o $@ ${OBJECTS}

install:
	cp -f jsmin ${INSPATH}

clean:
	rm -f *.o *~ core jsmin

jsmin.o: jsmin.c
