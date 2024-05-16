# startrek_payroll

A simple SQL injection vulnerable web application powered by Docker

## Project Summary

This is a simple web application that is vulnerable to SQL injection attacks. The web application is based on the `payroll_app` from the [Metasploitable3 project](https://github.com/rapid7/metasploitable3), and the PHP code is taken (almost) directly from that project. The primary contribution of this project is a Docker environment using docker-compose and consisting of Nginx, PHP and MySQL containers to run the web application easily.

## Project Instructions

Install the project requirements on your choice of operating system, including:

- Docker
- Docker Compose plugin

Run the following:

- `docker-compose up`

Open web browser and visit:

- `localhost:8080`
