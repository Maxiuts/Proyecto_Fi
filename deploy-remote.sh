#!/bin/bash
cd /mnt/c/Users/maxim/proyectos/Proyecto_Fi
git pull
docker compose restart app
docker compose exec -T app php artisan migrate --force