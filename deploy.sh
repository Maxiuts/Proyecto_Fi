#!/bin/bash
ssh maxim@100.88.140.50 "wsl -d Ubuntu bash -c \"cd /mnt/c/Users/maxim/proyectos/Proyecto_Fi && git pull && docker compose restart app\""