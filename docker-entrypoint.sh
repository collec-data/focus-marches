#!/bin/sh
set -e

echo "démarrage de focus version : $ENVP"
cp /app/personalisation/${ENVP}/style.css /app/css/
cp /app/personalisation/${ENVP}/*.png /app/img
cp /app/personalisation/${ENVP}/favicon.ico /app/img
cp /app/personalisation/${ENVP}/${ENVP}.po /app/locale/fr_FR/LC_MESSAGES/dico.po
cp /app/personalisation/${ENVP}/${ENVP}.mo /app/locale/fr_FR/LC_MESSAGES/dico.mo

exec "$@"
