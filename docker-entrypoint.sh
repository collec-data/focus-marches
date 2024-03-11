#!/bin/bash
set -e

echo "démarrage de focus version : $ENVP"
if [[ -z "$ENVP" ]]; then
    echo "La variable de personnalisation ENVP n'est pas définie"
    #remove the style-override.css file if exists
    rm -f style-override.css
else
    cp /app/personalisation/${ENVP}/style-override.css /app/css/
    cp /app/personalisation/${ENVP}/*.png /app/img
    cp /app/personalisation/${ENVP}/favicon.ico /app
    cp /app/personalisation/${ENVP}/${ENVP}.po /app/locale/fr_FR/LC_MESSAGES/dico.po
    cp /app/personalisation/${ENVP}/${ENVP}.mo /app/locale/fr_FR/LC_MESSAGES/dico.mo
fi

exec "$@"
