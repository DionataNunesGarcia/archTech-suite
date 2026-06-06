#!/bin/bash
# Shared library of helper code for DDEV commands.
# @see .ddev/commands/host
# @see .ddev/commands/web

OPTIONS=""
PARAMETERS=""
for argument in $@;
do
  if [[ $argument == -* ]];
  then
    OPTIONS="$OPTIONS $argument"
  else
    PARAMETERS="$PARAMETERS $argument"
  fi
done
OPTIONS="${OPTIONS#"${OPTIONS%%[![:space:]]*}"}"
PARAMETERS="${PARAMETERS#"${PARAMETERS%%[![:space:]]*}"}"

if [ -d "$DDEV_DOCROOT/modules/custom" ]; then
  PARAMETERS=${PARAMETERS:-$DDEV_DOCROOT/modules/custom}
else
  PARAMETERS=${PARAMETERS:-$DDEV_DOCROOT/modules}
fi

PARAMETERS=$(echo $PARAMETERS | sed -r "s|[^ ]+/$DDEV_DOCROOT/modules/|$DDEV_DOCROOT/modules/|g")
PARAMETERS=$(echo $PARAMETERS | sed -r "s|[^ ]+/$DDEV_DOCROOT/recipes/|$DDEV_DOCROOT/recipes/|g")

function _echo() {
  echo -e "$1""${@:2}"$NORM$RESET
}

BLACK='\033[0;30m'
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[0;33m'
BLUE='\033[0;34m'
PINK='\033[0;35m'
CYAN='\033[0;36m'
WHITE='\033[0;37m'
BOLD=$(tput bold)
RESET='\033[0m'
NORM=$(tput sgr0)
