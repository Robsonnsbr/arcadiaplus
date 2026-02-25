# =========================
# STAGE 1 — BUILDER
# =========================
FROM node:20-slim AS builder

WORKDIR /app

# Dependências de build
RUN apt-get update && apt-get install -y \
    git \
    python3.11 \
    python3.11-venv \
    python3-pip \
    openjdk-17-jre-headless \
    build-essential \
    libpq-dev \
    gfortran \
    libffi-dev \
    libfreetype6-dev \
    libpng-dev \
    bash \
    curl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Python venv + libs (como antes)
RUN python3.11 -m venv /opt/venv \
 && /opt/venv/bin/pip install --upgrade pip \
 && /opt/venv/bin/pip install --no-cache-dir \
    fastapi \
    uvicorn \
    pandas \
    numpy \
    psycopg2-binary \
    httpx \
    pydantic \
    lxml \
    python-multipart \
    python-docx \
    cryptography \
    signxml \
    zeep \
    beautifulsoup4 \
    matplotlib \
    pymongo \
    pyopenssl

ENV PATH="/opt/venv/bin:$PATH"

# Node deps (com dev)
COPY package.json package-lock.json* ./
RUN npm install --include=dev

# Projeto inteiro
COPY . .

# Build TS
RUN npm run build


# =========================
# STAGE 2 — RUNNER
# =========================
FROM node:20-slim
	
WORKDIR /app

# Runtime libs
RUN apt-get update && apt-get install -y \
    python3 \
    python3-venv \
    python3-pip \
    openjdk-17-jre-headless \
    libpq-dev \
    libffi-dev \
    libfreetype6-dev \
    libpng-dev \
    bash \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Python venv pronto
COPY --from=builder /opt/venv /opt/venv
ENV PATH="/opt/venv/bin:$PATH"

# Código JS compilado
COPY --from=builder /app/dist ./dist

# Engines e serviços
COPY --from=builder /app/server ./server
COPY --from=builder /app/metabase ./metabase

# Node manifests
COPY --from=builder /app/package.json ./
COPY --from=builder /app/package-lock.json* ./

# Apenas deps de produção
RUN npm install --omit=dev

ENV NODE_ENV=production
ENV PORT=5000

EXPOSE 5000

CMD ["node", "dist/index.cjs"]
