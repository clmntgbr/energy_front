# Symfony Docker Template

A Symfony 6.1 docker template base project using PHP8.1, Nginx, MariaDb, RabbitMQ, MailDev.

## Getting Started

1. Clone https://github.com/clmntgbr/setup and run `make start`
2. Clone this repo
3. Run `cp .env.dist .env`
6. Run `make init` to initialize the project
7. You can run `make help` to see all commands available

## Overview

Open `https://traefik.traefik.me/dashboard/#/` in your favorite web browser for traefik dashboard

Open `https://maildev.traefik.me` in your favorite web browser for maildev

Open `https://rabbitmq.traefik.me` in your favorite web browser for rabbitmq

Open `https://gas.traefik.me` in your favorite web browser for symfony app
Open `https://gas.traefik.me/admin` for the admin part

## Features

* PHP 8.2
* Nginx 1.20
* RabbitMQ 3-management
* MariaDB 10.4.19
* MailDev
* Traefik latest
* Symfony 6.2

**Enjoy!**
