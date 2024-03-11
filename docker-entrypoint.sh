#!/bin/bash
set -e

echo "démarrage de focus version : $ENVP"
if [[ -z "$ENVP" ]]; then
    echo "La variable de personnalisation ENVP n'est pas définie"
    cp /app/personalisation/default/style-override.css /app/css/
    cp /app/personalisation/default/*.png /app/img
    cp /app/personalisation/default/favicon.ico /app
    # remove logo brands when using default env
    rm -f /img/focus-marches_logo_brand.png
    # no default messages with .po and .mo files
else
    cp /app/personalisation/${ENVP}/style-override.css /app/css/
    cp /app/personalisation/${ENVP}/*.png /app/img
    cp /app/personalisation/${ENVP}/favicon.ico /app
    cp /app/personalisation/${ENVP}/${ENVP}.po /app/locale/fr_FR/LC_MESSAGES/dico.po
    cp /app/personalisation/${ENVP}/${ENVP}.mo /app/locale/fr_FR/LC_MESSAGES/dico.mo
fi

exec "$@"
