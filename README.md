# Parts & Parcel — Marketplace & Logistics Platform

[![Continuous Integration (CI)](https://github.com/dreigningking/partsandparcel/actions/workflows/ci.yml/badge.svg)](https://github.com/dreigningking/partsandparcel/actions/workflows/ci.yml)
[![Docker Image](https://img.shields.io/badge/docker%20hub-dreigningking%2Fpartsandparcel-blue.svg?logo=docker)](https://hub.docker.com/r/dreigningking/partsandparcel)
[![AWS Lightsail](https://img.shields.io/badge/aws%20lightsail-live-orange.svg?logo=amazon-aws)](http://35.178.213.234)

Nigeria's marketplace and logistics platform for devices, machines, spare parts, and salvage items.

---

## 🌐 Live Production Deployment
* **Live Marketplace URL**: [http://35.178.213.234](http://35.178.213.234)
* **Cloud Infrastructure**: Amazon Lightsail (`eu-west-2a`, London)
* **Docker Hub Registry**: [`dreigningking/partsandparcel:latest`](https://hub.docker.com/r/dreigningking/partsandparcel)

---

## 🚀 Architecture & Tech Stack

- **Backend**: Laravel 11 (PHP 8.3-FPM)
- **Frontend**: Livewire 3, Alpine.js, Tailwind CSS (Vite compiled)
- **Database**: MySQL 8.0 (Persistent Docker Volume)
- **Cache & User Sessions**: Redis 7 Alpine
- **Queue Workers**: Supervisor-managed asynchronous queue processing (`php artisan queue:work`)
- **Automated CI/CD**:
  - **GitHub Actions CI**: Automated 65+ unit & feature test suite with SQLite in-memory & Redis service.
  - **GitHub Actions CD**: Multi-stage Docker build, pushed to Docker Hub, zero-downtime rolling update on AWS via SSH.

---

## 🧪 Running Tests Locally

```bash
php artisan test
```

## 🐳 Running with Docker

```bash
docker compose up -d
```
