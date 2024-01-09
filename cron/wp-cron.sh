#!/bin/bash
# Service géré par systemctl status cron_degaine.service
# sudo nano /etc/systemd/system/cron_degaine.service

cd "$(dirname "$0")"/../..
attente=60


# Vérifie si le drapeau --now est passé
for arg in "$@"
do
    if [ "$arg" == "--now" ]; then
        attente=0
    fi
done


while true; do
  start_time=$(date +%s)

    echo "WP Cron"

    wp cron event run --due-now --path=/home/sabr8669/htdocs/coworking-metz.fr


  end_time=$(date +%s)
  duration=$((end_time - start_time))

  # Check if the command took longer than 1 minute
  if [ $duration -ge $attente ]; then
    sleep 0  # Immediately start the next command
  else
    echo "Attente"
    sleep $(($attente - duration))  # Wait for 1 minute before executing the command again
  fi
done
