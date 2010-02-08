#!/bin/bash

[ $# == 1 ] || {
    echo "Usage: horde-patch-config.sh configfile"
    exit 1
}

while read -r -a line
do
    [ "${line[0]}" == "-" ] && from="${line[3]}"
    [ "${line[0]}" == "+" ] && to="${line[3]}"
done < <(diff -u "$1" "$1.dist" | grep \$Id)

[[ -z "$from" || -z "$to" ]] && {
    echo "Cannot determine \$Id$ strings. The config file might be up-to-date."
    exit 1
}

file=$(git cat-file blob "$from")
echo "${file//\$Id\$/\$Id: $from \$}" > "patch$from"
file=$(git cat-file blob "$to")
echo "${file//\$Id\$/\$Id: $to \$}" > "patch$to"

diff -u "patch$from" "patch$to" | patch "$1"

rm  "patch$from" "patch$to"
