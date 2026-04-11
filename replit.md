# Superstore - AI-Powered Business Operating System

## Overview

Superstore is an AI-powered Business Operating System (BOS) by Arcádia Technology. It integrates ERP, CRM, Fiscal management, BI, and autonomous agent-based automation into a single platform.

## Project Structure

- `arcadiasuite/` — Main application (React + Express + Python services)
  - `client/` — React 19 + TypeScript frontend (Vite build)
  - `server/` — Express.js backend with Socket.IO
  - `server/python/` — FastAPI Python microservices
  - `shared/` — Drizzle ORM schemas
  - `db/` — PostgreSQL database connection

## Tech Stack

- **Frontend**: React 19, TypeScript, Tailwind CSS 4, shadcn/ui, Vite
- **Backend**: Express.js, Socket.IO, Passport.js (session auth)
- **Database**: PostgreSQL via Drizzle ORM
- **Python Services**: FastAPI (ports 8003-8005)
- **Communication Engine**: Node/TypeScript service (port 8006)

## Architecture

- Port 5000: Main Express server (API + Vite frontend in dev, static files in prod)
- Port 8003: Contabil Python service (FastAPI)
- Port 8004: BI Engine Python service (FastAPI)
- Port 8005: Automation Engine Python service (FastAPI)
- Port 8006: Communication Engine (Node.js)

## Setup

- Dependencies: `cd arcadiasuite && npm install`
- Database: `cd arcadiasuite && npx drizzle-kit push`
- Dev: `cd arcadiasuite && npm run dev`
- Build: `cd arcadiasuite && npm run build`
- Start (prod): `cd arcadiasuite && npm run start`

## Key Notes

- OpenAI API key uses `AI_INTEGRATIONS_OPENAI_API_KEY` env var (falls back to `OPENAI_API_KEY`)
- All OpenAI clients use `|| "placeholder"` fallback so server starts without API key
- PHP/Laravel (Plus ERP) is a separate legacy component not configured in this environment
- Python 3.11 required for python services
- `SESSION_SECRET` env var should be set for production
